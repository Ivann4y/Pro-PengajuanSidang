<?php
session_start();
// Pastikan user adalah dosen dan datanya ada di session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}
if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    die("Error: Data dosen tidak ditemukan di session. Silakan login kembali.");
}

include '../../koneksi/koneksiAndrew.php';
if ($conn === false) {
    die("Koneksi gagal: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

if (!isset($_POST['id_sidang']) || empty($_POST['id_sidang'])) {
    // If no ID is posted, we cannot proceed.
    die("Error: ID Sidang tidak valid atau tidak diberikan.");
}
$id_sidang = (int)$_POST['id_sidang'];
$jenis_sidang_url = isset($_POST['tipe']) ? $_POST['tipe'] : null;
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];

// ------------------------------
// Handle Download Dokumen
// ------------------------------
if (isset($_GET['download']) && $_GET['download'] === 'main') {
    $sql_download = "SELECT dok_laporan, judul FROM Sidang WHERE id_sidang = ?";
    $stmt_download = sqlsrv_query($conn, $sql_download, [$id_sidang]);
    if ($stmt_download && $row = sqlsrv_fetch_array($stmt_download, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['dok_laporan'])) {
            $file_content = $row['dok_laporan'];
            $filename = "Laporan_Sidang_{$id_sidang}.pdf"; // Default
            // Detect file type
            $magic_pdf = "\x25\x50\x44\x46"; // %PDF
            $magic_zip = "\x50\x4b\x03\x04"; // PK..
            if (substr($file_content, 0, 4) === $magic_pdf) $filename = "Laporan_Sidang_{$id_sidang}.pdf";
            elseif (substr($file_content, 0, 4) === $magic_zip) $filename = "Laporan_Sidang_{$id_sidang}.zip";
            else $filename = "Laporan_Sidang_{$id_sidang}.dat";
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $file_content; exit;
        }
        die("Dokumen tidak ditemukan di database.");
    } else {
        die("Gagal mengambil data dokumen.");
    }
}

// ------------------------------
// Ambil detail sidang dan cek hak akses dosen
// ------------------------------
$sql_sidang = "
SELECT s.id_sidang, s.id_kelompok, s.judul, s.jenis_sidang, s.status_ajuan, 
       mk.nama_matkul, k.nomor_kelompok, k.tahun_ajaran, k.id_matkul,
       CASE WHEN s.jenis_sidang = 'Tugas Akhir' THEN 'TA' ELSE 'Semester' END AS label_sidang
FROM Sidang s
JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
WHERE s.id_sidang = ?
";
$stmt_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
$data_sidang = sqlsrv_fetch_array($stmt_sidang, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) die("Data sidang tidak ditemukan.");

// --------- Cek otorisasi dosen ---------
$authorized = false;
if ($data_sidang['jenis_sidang'] === 'Tugas Akhir') {
    // Cek apakah dosen adalah pembimbing kelompok ini
    $sql_auth = "SELECT 1 FROM Bimbingan WHERE id_kelompok = ? AND nomor_dosen = ? AND isPembimbing = 1";
    $stmt_auth = sqlsrv_query($conn, $sql_auth, [$data_sidang['id_kelompok'], $nomorDosen]);
    if ($stmt_auth && sqlsrv_fetch_array($stmt_auth, SQLSRV_FETCH_NUMERIC)) $authorized = true;
} else {
    // Cek Pengampu_Kelas (hanya Pengampu mata kuliah tersebut di tahun ajaran ini)
    $sql_auth = "SELECT 1 FROM Pengampu_Kelas WHERE nomor_dosen = ? AND id_matkul = ? AND tahun_ajaran = ?";
    $stmt_auth = sqlsrv_query($conn, $sql_auth, [$nomorDosen, $data_sidang['id_matkul'], $data_sidang['tahun_ajaran']]);
    if ($stmt_auth && sqlsrv_fetch_array($stmt_auth, SQLSRV_FETCH_NUMERIC)) $authorized = true;
}
if (!$authorized) die("Anda tidak berwenang melihat detail pengajuan ini.");

// Ambil anggota kelompok
$sql_anggota = "
    SELECT k.nim, m.nama_mhs
    FROM Kelompok k
    JOIN Mahasiswa m ON k.nim = m.nim
    WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.id_matkul = ? AND k.jenis_sidang = ?
    ORDER BY m.nama_mhs
";
$params_anggota = [$data_sidang['nomor_kelompok'], $data_sidang['tahun_ajaran'], $data_sidang['id_matkul'], $data_sidang['jenis_sidang']];
$stmt_anggota = sqlsrv_query($conn, $sql_anggota, $params_anggota);
$anggota_kelompok = [];
while ($stmt_anggota && ($row = sqlsrv_fetch_array($stmt_anggota, SQLSRV_FETCH_ASSOC))) {
    $anggota_kelompok[] = $row;
}

// Dosen Pembimbing (list all, if TA)
$dosen_pembimbing = [];
if ($data_sidang['jenis_sidang'] === 'Tugas Akhir') {
    $sql_dosen = "SELECT d.nama_dosen FROM Bimbingan b JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
    $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$data_sidang['id_kelompok']]);
    while ($stmt_dosen && ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
        $dosen_pembimbing[] = $row['nama_dosen'];
    }
}

// ------------- Approve / Reject -------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $authorized && $data_sidang['status_ajuan'] === 'Pending') {
    if (isset($_POST['approve'])) {
        $sql_update = "UPDATE Sidang SET status_ajuan = 'Approved' WHERE id_sidang = ?";
        sqlsrv_query($conn, $sql_update, [$id_sidang]);
        $_SESSION['success'] = "Sidang berhasil disetujui";
        header("Location: dPengajuan.php"); exit();
    }
    if (isset($_POST['reject'])) {
        $catatan = trim($_POST['catatan'] ?? '');
        if (empty($catatan)) $_SESSION['error'] = "Silakan isi alasan penolakan.";
        else {
            $sql_update = "UPDATE Sidang SET status_ajuan = 'Rejected' WHERE id_sidang = ?";
            sqlsrv_query($conn, $sql_update, [$id_sidang]);
            $_SESSION['success'] = "Sidang berhasil ditolak";
            header("Location: dPengajuan.php"); exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../css/button-style.css">
  <link rel="stylesheet" href="../../assets/css/dDetailPengajuan.css">
  <link rel="stylesheet" href="../../extra/style.css">
  <title>Detail Pengajuan</title>
</head>
<body class="p-4">
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
        </div>
        <main class="NavSide__main-content" id="dPengajuan">
            <h2 class="mb-4">Detail Pengajuan</h2>
            <div class="card mb-3 info-pengajuan">
                <h5 class="fw-semibold section">Informasi Pengajuan</h5>
                <div class="row mt-2">
                    <div class="col-md-6 section">
                        <p class="mb-1 fw-bold">ID Kelompok</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['id_kelompok'] ?? '-') ?></p>
                        <p class="mb-1 fw-bold">Nomor Kelompok</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['nomor_kelompok'] ?? '-') ?></p>
                        <p class="mb-1 fw-bold">Tahun Ajaran</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['tahun_ajaran'] ?? '-') ?></p>
                        <p class="mb-1 fw-bold">Mata Kuliah</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['nama_matkul'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6 section">
                        <p class="mb-1 fw-bold">Judul Sidang</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['judul'] ?? '-') ?></p>
                        <p class="mb-1 fw-bold">Jenis Sidang</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['label_sidang']) ?></p>
                        <?php if ($data_sidang['jenis_sidang'] === 'Tugas Akhir'): ?>
                            <p class="mb-1 fw-bold">Dosen Pembimbing</p>
                            <ul class="fw-normal ps-3 mb-3">
                                <?php foreach ($dosen_pembimbing as $nama): ?>
                                    <li><?= htmlspecialchars($nama) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <p class="mb-1 fw-bold">Status Pengajuan</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['status_ajuan']) ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 section">
                        <p class="mb-1 fw-bold">Anggota Kelompok</p>
                        <ul class="fw-normal ps-3 mb-3">
                            <?php foreach ($anggota_kelompok as $anggota): ?>
                                <li><?= htmlspecialchars($anggota['nama_mhs']) . " (" . htmlspecialchars($anggota['nim']) . ")" ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card mb-3 dokumen-sidang">
                <h5 class="fw-semibold">Dokumen Laporan</h5>
                <div class="mt-2">
                    <?php
                    $sql_check_doc = "SELECT dok_laporan FROM Sidang WHERE id_sidang = ?";
                    $stmt_check_doc = sqlsrv_query($conn, $sql_check_doc, [$id_sidang]);
                    $doc_data = sqlsrv_fetch_array($stmt_check_doc, SQLSRV_FETCH_ASSOC);
                    ?>
                    <?php if (!empty($doc_data['dok_laporan'])) : ?>
                        <form action="dDetailPengajuan.php" method="POST" style="display: inline-block;">
                        <input type="hidden" name="id_sidang" value="<?= htmlspecialchars($id_sidang) ?>">
                        <input type="hidden" name="tipe" value="<?= htmlspecialchars($jenis_sidang_url) ?>">
                        <input type="hidden" name="download" value="main">
                        <button type="submit" class="text-decoration-none base-tombol berkas-laporan" style="border: 1px solid #212529 !important;">
                            <i class="fa-solid fa-file-lines me-2"></i>Unduh Dokumen Laporan
                        </button>
                    </form>
                    <?php else : ?>
                        <p class="text-muted">Tidak ada dokumen yang diunggah</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($authorized && $data_sidang['status_ajuan'] === 'Pending'): ?>
            <div class="action-buttons mt-4 d-flex justify-content-between align-items-center">
                <a href="dPengajuan.php" class="btn btn-secondary btn-circle">
                    <i class="fa-solid fa-circle-arrow-left"></i>
                    <span class="ms-2">Kembali</span>
                </a>
                <div>
                    <button type="button" class="btn btn-danger btn-circle me-2" id="btnTolakOpenModal">Tolak</button>
                    <form id="approveForm" method="POST" style="display: inline;">
                        <button type="button" class="btn btn-success btn-circle" id="btnSetujuiOpenModal">Setujui</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="mt-4">
                <a href="dPengajuan.php" class="btn btn-secondary btn-circle">
                    <i class="fa-solid fa-circle-arrow-left"></i>
                    <span class="ms-2">Kembali</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Modal Approve -->
            <div class="modal fade" id="modalKonfirmasiSetujui" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 text-center py-4 px-3">
                        <div class="modal-header border-0 justify-content-center">
                            <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel">Perhatian</h4>
                        </div>
                        <div class="modal-body">
                            <p class="mb-4 fw-semibold">Apakah Anda yakin ingin menyetujui pengajuan ini?</p>
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-secondary px-4 py-2 fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                                <button type="button" class="btn btn-success px-4 py-2 fw-semibold" id="confirmSetujuiBtn">Lanjutkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Reject -->
            <div class="modal fade" id="modalKonfirmasiTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 text-center py-4 px-3">
                        <form id="rejectForm" method="POST">
                            <div class="modal-header border-0 justify-content-center">
                                <h4 class="modal-title fw-bold" id="modalTolakLabel">Alasan Penolakan</h4>
                            </div>
                            <div class="modal-body">
                                <p class="mb-3 fw-semibold">Harap masukkan alasan penolakan pengajuan ini.</p>
                                <textarea name="catatan" class="form-control" rows="4" placeholder="Ketik alasan di sini..."></textarea>
                                <input type="hidden" name="reject" value="1">
                            </div>
                            <div class="modal-footer border-0 justify-content-center gap-3">
                                <button type="button" class="btn btn-secondary px-4 py-2 fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold">Tolak Pengajuan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal Logout -->
            <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div style="background-color: rgb(67, 54, 240);">
                            <div class="modal-header">
                                <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                            </div>
                        </div>
                        <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                            <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle logic
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    if (menuToggle && sidebar) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // Modal SweetAlert for Approve/Reject
    const modalSetujui = new bootstrap.Modal(document.getElementById('modalKonfirmasiSetujui'));
    const modalTolak = new bootstrap.Modal(document.getElementById('modalKonfirmasiTolak'));

    let btnSetujui = document.getElementById('btnSetujuiOpenModal');
    let btnTolak = document.getElementById('btnTolakOpenModal');

    if (btnSetujui) {
        btnSetujui.addEventListener('click', function () {
            modalSetujui.show();
        });
    }
    if (btnTolak) {
        btnTolak.addEventListener('click', function () {
            modalTolak.show();
        });
    }

    let confirmSetujuiBtn = document.getElementById('confirmSetujuiBtn');
    if (confirmSetujuiBtn) {
        confirmSetujuiBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Pengajuan Berhasil Disetujui!',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            }).then((result) => {
                if (result.isConfirmed) {
                    const approveForm = document.getElementById('approveForm');
                    let approveInput = approveForm.querySelector('input[name="approve"]');
                    if (!approveInput) {
                        approveInput = document.createElement('input');
                        approveInput.type = 'hidden';
                        approveInput.name = 'approve';
                        approveInput.value = 'Approve';
                        approveForm.appendChild(approveInput);
                    }
                    approveForm.submit();
                }
            });
        });
    }

    let rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const catatan = this.querySelector('textarea[name="catatan"]').value.trim();
            if (catatan === "") {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Silakan isi alasan penolakan terlebih dahulu.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                });
            } else {
                Swal.fire({
                    title: 'Pengajuan Telah Ditolak!',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let rejectInput = this.querySelector('input[name="reject"]');
                        if (!rejectInput) {
                            rejectInput = document.createElement('input');
                            rejectInput.type = 'hidden';
                            rejectInput.name = 'reject';
                            rejectInput.value = 'Reject';
                            this.appendChild(rejectInput);
                        }
                        this.submit();
                    }
                });
            }
        });
    }
});
</script>
</body>
</html>
