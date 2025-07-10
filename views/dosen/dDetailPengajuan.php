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

$id_sidang = $_POST['id_sidang'] ?? $_GET['id_sidang'] ?? null;
if (empty($id_sidang)) {
    die("Error: ID Sidang tidak valid atau tidak diberikan.");
}
$id_sidang = (int)$id_sidang;
$jenis_sidang_url = $_POST['tipe'] ?? $_GET['tipe'] ?? null;
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];

// [REPLACE THIS BLOCK]
// ------------------------------
// Handle Download Dokumen
// ------------------------------
if (isset($_POST['download']) && $_POST['download'] === 'main') {
    // Join with Detail_Sidang to get the original filename
    $sql_download = "SELECT s.dok_laporan, ds.nama_file 
                     FROM Sidang s
                     LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
                     WHERE s.id_sidang = ?";
    $stmt_download = sqlsrv_query($conn, $sql_download, [$id_sidang]);
    
    if ($stmt_download && $row = sqlsrv_fetch_array($stmt_download, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['dok_laporan'])) {
            $file_path_from_db = $row['dok_laporan']; // e.g., 'uploads/somefile.pdf'
            $original_filename = $row['nama_file'] ?? basename($file_path_from_db);
            
            // Construct the full server path to the file
            $full_file_path = __DIR__ . '/../../' . $file_path_from_db;
            
            if (file_exists($full_file_path)) {
                // Set generic headers to force download for any file type
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $original_filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($full_file_path));
                
                // Read the file and send it to the output buffer
                flush(); // Flush system output buffer
                readfile($full_file_path);
                exit;
            } else {
                die("Error: File tidak ditemukan di server pada path: " . htmlspecialchars($full_file_path));
            }
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
SELECT s.id_sidang, s.id_kelompok, s.judul, k.jenis_sidang, s.status_ajuan, 
       mk.nama_matkul, k.nomor_kelompok, k.tahun_ajaran, k.id_matkul,
       CASE WHEN k.jenis_sidang = 'Tugas Akhir' THEN 'TA' ELSE 'Semester' END AS label_sidang
FROM Sidang s
LEFT JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
WHERE s.id_sidang = ?
";
$stmt_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
$data_sidang = sqlsrv_fetch_array($stmt_sidang, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) die("Data sidang tidak ditemukan.");

$judul = $data_sidang['judul'];

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
    $sql_dosen = "
    SELECT d.nama_dosen 
    FROM Bimbingan b 
    JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen 
    WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
    $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$data_sidang['id_kelompok']]);
    while ($stmt_dosen && ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
        $dosen_pembimbing[] = $row['nama_dosen'];
    }
}

// ------------- Approve / Reject -------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data_sidang['status_ajuan'] === 'Pending') {
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
  <!-- <link rel="stylesheet" href="../../assets/css/dDokumenRevisi.css"> -->
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
                <b></b>
                <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                <b></b>
            </li>
            <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                <b></b>
                <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                <b></b>
            </li>
            <li class="NavSide__sidebar-item">
                <b></b>
                <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                <b></b>
            </li>
            <li class="NavSide__sidebar-item">
                <b></b>
                <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                <b></b>
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
            <h2 class="text-heading text-black" style="font-weight: 700;">Detail Pengajuan - <?= htmlspecialchars($judul) ?></h2>
            <div class="card mb-3 info-pengajuan">
                <h5 class="fw-bold section">Informasi Pengajuan</h5>

                <div class="row mt-2">
                    <div class="col-md-6 section">
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-hashtag me-0"></i><span class="fw-bold ms-0">ID Kelompok</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['id_kelompok'] ?? '-') ?></div>
                        </div>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-users me-0"></i><span class="fw-bold ms-0">Nomor Kelompok</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['nomor_kelompok'] ?? '-') ?></div>
                        </div>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-calendar-days me-0"></i><span class="fw-bold ms-0">Tahun Ajaran</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['tahun_ajaran'] ?? '-') ?></div>
                        </div>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-book me-0"></i><span class="fw-bold ms-0">Mata Kuliah</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['nama_matkul'] ?? 'N/A') ?></div>
                        </div>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-people-group me-0"></i><span class="fw-bold ms-0">Anggota Kelompok</span></div>
                            <div class="value-row ms-4">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($anggota_kelompok as $anggota): ?>
                                        <li><?= htmlspecialchars($anggota['nama_mhs']) . " (" . htmlspecialchars($anggota['nim']) . ")" ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 section">
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-file-invoice me-0"></i><span class="fw-bold ms-0">Judul Sidang</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['judul'] ?? '-') ?></div>
                        </div>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-tag me-0"></i><span class="fw-bold ms-0">Jenis Sidang</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['label_sidang']) ?></div>
                        </div>
                        <?php if ($data_sidang['jenis_sidang'] === 'Tugas Akhir' && !empty($dosen_pembimbing)): ?>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-user-tie me-0"></i><span class="fw-bold ms-0">Dosen Pembimbing</span></div>
                            <div class="value-row ms-4">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($dosen_pembimbing as $nama): ?>
                                        <li><?= htmlspecialchars($nama) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-clipboard-question me-0"></i><span class="fw-bold ms-0">Status Pengajuan</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['status_ajuan']) ?></div>
                        </div>
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
                            <i class="fa-solid fa-file-lines me-2"></i><?= htmlspecialchars($doc_data['dok_laporan']) ?>
                        </button>
                    </form>
                    <?php else : ?>
                        <p class="text-muted">Tidak ada dokumen yang diunggah</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($data_sidang['status_ajuan'] === 'Pending'): ?>
            <div class="action-buttons mt-4 d-flex justify-content-between align-items-center">
                <a href="dPengajuan.php" class="btn btn-secondary btn-circle">
                    <i class="fa-solid fa-circle-arrow-left"></i>
                    <span class="ms-2">Kembali</span>
                </a>
                <div>
                    <button type="button" class="btn btn-danger btn-circle me-2" id="btnTolakOpenModal">Tolak</button>
                    <form id="approveForm" method="POST" style="display: inline;">
                    <input type="hidden" name="id_sidang" value="<?= htmlspecialchars($id_sidang) ?>">
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
                                <input type="hidden" name="id_sidang" value="<?= htmlspecialchars($id_sidang) ?>">
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
<script src=" ../../assets/js/dDetailPengajuan.js"></script>
</body>
</html>
