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

// =================================================================
// PERUBAHAN 1: Logika Download File dari Database
// =================================================================
if (isset($_POST['download']) && $_POST['download'] === 'main') {
    // Ambil hanya kolom dok_laporan dari database
    $sql_download = "SELECT dok_laporan, jenis_sidang FROM Sidang WHERE id_sidang = ?";
    $stmt_download = sqlsrv_query($conn, $sql_download, [$id_sidang]);
    
    if ($stmt_download && $row = sqlsrv_fetch_array($stmt_download, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['dok_laporan'])) {
            // Tentukan nama file default berdasarkan jenis sidang
            $file_extension = ".dat"; // Ekstensi default
            $file_content = $row['dok_laporan'];
            
            // Heuristik sederhana untuk menentukan tipe file dari "magic numbers"
            $magic_pdf = "\x25\x50\x44\x46"; // %PDF
            $magic_docx = "\x50\x4b\x03\x04"; // PK..

            if (substr($file_content, 0, 4) === $magic_pdf) {
                $file_extension = ".pdf";
            } elseif (substr($file_content, 0, 4) === $magic_docx) {
                // Bisa jadi docx, pptx, atau zip. Kita gunakan .zip sebagai fallback umum.
                $file_extension = ".zip";
            }

            $filename = "Laporan_Sidang_" . $id_sidang . $file_extension;

            // Kirim header untuk memulai download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            // Cetak isi file biner ke browser
            echo $file_content;
            exit;
        } else {
            die("Dokumen tidak ditemukan di database.");
        }
    } else {
        die("Gagal mengambil data dokumen.");
    }
}


// --- Kode pengambilan data utama Anda tetap sama ---
// 1. Ambil data utama sidang, nama pembimbing, matkul
$sql_main = "
    WITH SidangInfo AS (
        SELECT DISTINCT
            s.id_sidang, s.id_kelompok, s.judul, 
            d.nama_dosen AS nama_pembimbing,
            mk.nama_matkul
        FROM Sidang s
        JOIN Bimbingan b ON s.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
        JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
        LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        LEFT JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
    )
    SELECT * FROM SidangInfo WHERE id_sidang = ?";
$stmt_main = sqlsrv_query($conn, $sql_main, [$id_sidang]);

if ($stmt_main === false) {
    die("Error saat mengambil data sidang: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$data_sidang = sqlsrv_fetch_array($stmt_main, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) {
    die("Data sidang tidak ditemukan.");
}

// Tentukan label jenis sidang
$jenis_sidang_label = (isset($data_sidang['nama_matkul']) && strcasecmp($data_sidang['nama_matkul'], 'Tugas Akhir') == 0)
    ? 'Sidang Tugas Akhir'
    : 'Sidang Semester';



// --- Sisa dari logika PHP Anda tetap sama ---
// 2. Ambil semua anggota kelompok
$id_kelompok = $data_sidang['id_kelompok'];
$anggota_kelompok = [];
$sql_anggota = "SELECT m.nim, m.nama_mhs 
                FROM Kelompok_Mahasiswa km 
                JOIN Mahasiswa m ON km.nim = m.nim 
                WHERE km.id_kelompok = ?";
$stmt_anggota = sqlsrv_query($conn, $sql_anggota, [$id_kelompok]);
if ($stmt_anggota) {
    while ($row = sqlsrv_fetch_array($stmt_anggota, SQLSRV_FETCH_ASSOC)) {
        $anggota_kelompok[] = $row;
    }
}

// 3. Handle aksi Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- LOGIKA UNTUK APPROVE ---
    if (isset($_POST['approve'])) {
         
        $sql_action = "UPDATE Sidang SET status_ajuan = ? WHERE id_sidang = ?";
        // Mengirim nilai sebagai 1 untuk status disetujui
        $params_action = ['Approve', $id_sidang]; 
        
        $stmt_action = sqlsrv_query($conn, $sql_action, $params_action);

        if ($stmt_action === false) {
            die("GAGAL MENYETUJUI SIDANG: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
        }

        $_SESSION['success'] = "Sidang berhasil disetujui";
        header("Location: dPengajuan.php");
        exit();
    }

    // --- LOGIKA UNTUK REJECT ---
    elseif (isset($_POST['reject'])) {
        $catatan = $_POST['catatan'] ?? '';

        if (empty($catatan)) {
            $_SESSION['error'] = "Silakan isi catatan penolakan."; // Catatan tidak disimpan, tapi validasi tetap baik
        } else {

            $sql_action = "UPDATE Sidang SET status_ajuan = ? WHERE id_sidang = ?";
            // Mengirim nilai sebagai 2 untuk status ditolak
            $params_action = ['Reject', $id_sidang];

            $stmt_action = sqlsrv_query($conn, $sql_action, $params_action);

            if ($stmt_action === false) {
                die("GAGAL MENOLAK SIDANG: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
            }
            
            $_SESSION['success'] = "Sidang berhasil ditolak";
            header("Location: dPengajuan.php");
            exit();
        }
    }
    
    // Redirect kembali ke halaman yang sama untuk me-refresh data
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_sidang=" . $id_sidang . "&tipe=" . $jenis_sidang_url);
    exit();
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
                    <b></b><b></b>
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"> 
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
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
                        
                   <p class="mb-1 fw-bold">Kelompok <?= htmlspecialchars($data_sidang['id_kelompok'] ?? '-') ?></p>
                        <ul class="fw-bold ps-3 mb-3">
                            <?php if (!empty($anggota_kelompok)) : ?>
                                <?php foreach ($anggota_kelompok as $anggota) : ?>
                                    <li class="fw-normal"><?= htmlspecialchars($anggota['nama_mhs']) . " (" . htmlspecialchars($anggota['nim']) . ")" ?></li>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <li class="fw-normal text-muted">Tidak ada data anggota.</li>
                            <?php endif; ?>
                        </ul>

                    </div>

                    <div class="col-md-6 section">
                        <p class="mb-1 fw-bold">Judul Sidang</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['judul'] ?? '-') ?></p>

                        <p class="mb-1 fw-bold">Jenis Sidang</p>
                        <p class="fw-normal"><?= htmlspecialchars($jenis_sidang_label) ?></p>

                        <p class="mb-1 fw-bold">Dosen Pembimbing</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['nama_pembimbing'] ?? '-') ?></p>
                        
                        <p class="mb-1 fw-bold">Mata Kuliah</p>
                        <p class="fw-normal"><?= htmlspecialchars($data_sidang['nama_matkul'] ?? 'N/A') ?></p>
                    </div>
                </div>
            </div>

            <div class="card mb-3 dokumen-sidang">
                <h5 class="fw-semibold">Dokumen Laporan</h5>
                <div class="mt-2">
                    <?php
                    // Cek ulang ketersediaan dokumen langsung dari database
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
            
<!-- This single form will handle both Approve and Reject actions -->
            <form id="actionForm" method="POST" action="dDetailPengajuan.php">
                <!-- Hidden fields to pass necessary data -->
                <input type="hidden" name="id_sidang" value="<?= htmlspecialchars($id_sidang) ?>">
                <input type="hidden" name="tipe" value="<?= htmlspecialchars($jenis_sidang_url) ?>">
                <input type="hidden" id="approveInput" name="approve" value="">
                <input type="hidden" id="rejectInput" name="reject" value="">
                <input type="hidden" id="catatanInput" name="catatan" value="">

                <div class="action-buttons mt-4 d-flex justify-content-between align-items-center">
                    <a href="dPengajuan.php" class="btn btn-secondary btn-circle">
                        <i class="fa-solid fa-circle-arrow-left"></i>
                        <span class="ms-2">Kembali</span>
                    </a>
                    <div>
                        <button type="button" class="btn btn-danger btn-circle me-2" id="btnTolakOpenModal">Tolak</button>
                        <button type="button" class="btn btn-success btn-circle" id="btnSetujuiOpenModal">Setujui</button>
                    </div>
                </div>
            </form>

            <!-- The "Tolak" modal should NOT have its own form tag -->
            <div class="modal fade" id="modalKonfirmasiTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
                 <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 text-center py-4 px-3">
                        <div class="modal-header border-0 justify-content-center">
                            <h4 class="modal-title fw-bold" id="modalTolakLabel">Alasan Penolakan</h4>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3 fw-semibold">Harap masukkan alasan penolakan pengajuan ini.</p>
                            <textarea id="catatanTextarea" class="form-control" rows="4" placeholder="Ketik alasan di sini..."></textarea>
                        </div>
                        <div class="modal-footer border-0 justify-content-center gap-3">
                             <button type="button" class="btn btn-secondary px-4 py-2 fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                             <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" id="confirmTolakBtn">Tolak Pengajuan</button>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div style="background-color: rgb(67, 54, 240);">
                                <div class="modal-header"><h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1></div>
                            </div>
                            <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                            <div class="modal-footer justify-content-center border-0">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                                <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="modal fade" id="modalKonfirmasiSetujui" tabindex="-1" aria-labelledby="modalSetujuiLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 text-center py-4 px-3">
                            <div class="modal-header border-0 justify-content-center">
                                <h4 class="modal-title fw-bold" id="modalSetujuiLabel">Konfirmasi Persetujuan</h4>
                            </div>
                            <div class="modal-body">
                                <p class="mb-3 fw-semibold">Anda yakin ingin menyetujui pengajuan sidang ini?</p>
                            </div>
                            <div class="modal-footer border-0 justify-content-center gap-3">
                                 <button type="button" class="btn btn-secondary px-4 py-2 fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                                 <button type="button" class="btn btn-success px-4 py-2 fw-semibold" id="confirmSetujuiBtn">Ya, Lanjutkan</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalSetujui = new bootstrap.Modal(document.getElementById('modalKonfirmasiSetujui'));
    const modalTolak = new bootstrap.Modal(document.getElementById('modalKonfirmasiTolak'));
    const mainForm = document.getElementById('actionForm');

    // Buka modal SETUJUI
    document.getElementById('btnSetujuiOpenModal').addEventListener('click', function () {
        modalSetujui.show();
    });

    // Buka modal TOLAK
    document.getElementById('btnTolakOpenModal').addEventListener('click', function () {
        modalTolak.show();
    });

    // Handle Lanjutkan button in aPROVE modal
    document.getElementById('confirmSetujuiBtn').addEventListener('click', function () {
        mainForm.querySelector('#approveInput').value = '1';
        mainForm.querySelector('#rejectInput').value = ''; // Clear other action
        mainForm.submit();
    });

    // Handle Tolak Pengajuan button in REJECT modal
    document.getElementById('confirmTolakBtn').addEventListener('click', function () {
        const catatan = document.getElementById('catatanTextarea').value.trim();
        if (catatan === "") {
            Swal.fire({
                title: 'Gagal',
                text: 'Silakan isi alasan penolakan terlebih dahulu.',
                icon: 'error',
                confirmButtonColor: '#4B68FB'
            });
            return; // Stop submission
        }
        
        mainForm.querySelector('#catatanInput').value = catatan;
        mainForm.querySelector('#rejectInput').value = '1';
        mainForm.querySelector('#approveInput').value = ''; // Clear other action
        mainForm.submit();
    });

    // Your existing sidebar toggle logic can remain here
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    if (menuToggle) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }
});
</script>
</body>
</html>