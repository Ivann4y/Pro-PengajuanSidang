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

include '../../koneksi/koneksiJoin.php';
require_once __DIR__ . '/../../control/kirimNotifikasi.php';
if ($conn === false) {
    die("Koneksi gagal: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

$id_sidang = $_POST['id_sidang'] ?? $_GET['id_sidang'] ?? $_GET['id'] ?? null;
if (empty($id_sidang)) {
    die("Error: ID Sidang tidak valid atau tidak diberikan.");
}
$id_sidang = (int)$id_sidang;
$jenis_sidang_url = $_POST['tipe'] ?? $_GET['tipe'] ?? null;
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];

// Tangkap parameter filter dari halaman sebelumnya
$from_status = $_POST['from_status'] ?? $_GET['from_status'] ?? 'Pending';
$from_filter = $_POST['from_filter'] ?? $_GET['from_filter'] ?? 'Semua';
$from_page = $_POST['from_page'] ?? $_GET['from_page'] ?? '1';

// Bangun query string untuk URL kembali
$back_query_string = http_build_query([
    'status' => $from_status,
    'filter' => $from_filter,
    'page' => $from_page
]);

$kembali_url = "dPengajuan.php?" . $back_query_string;

if (isset($_GET['download']) && $_GET['download'] === 'laporan' && isset($_GET['id'])) {
    $id_sidang_download = (int)$_GET['id'];

    // Ambil path file dan nama file asli dari database
    $sql_file = "SELECT dok_laporan, dok_final FROM Sidang WHERE id_sidang = ?";
    $stmt_file = sqlsrv_query($conn, $sql_file, [$id_sidang_download]);

    if ($stmt_file && $file_data = sqlsrv_fetch_array($stmt_file, SQLSRV_FETCH_ASSOC)) {
        $path_laporan = $file_data['dok_laporan'];
        $nama_file_asli = $file_data['dok_final'] ?? basename($path_laporan); // fallback kalau kosong

        $full_path = __DIR__ . '/../../' . $path_laporan;

        if (file_exists($full_path)) {
            // Set headers untuk download
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($nama_file_asli) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));

            // Flush output buffer dan kirim file
            flush();
            readfile($full_path);
            exit;
        } else {
            die(" File tidak ditemukan di server. Path: " . htmlspecialchars($full_path));
        }
    } else {
        die("Gagal mengambil data file dari database.");
    }
}

// ------------------------------
// Ambil detail sidang dan cek hak akses dosen
// ------------------------------
// ... (setelah blok handle download)
$sql_sidang = "
SELECT s.id_sidang, s.id_kelompok, s.judul, k.jenis_sidang, s.status_ajuan, s.alasan_tolak,
       mk.nama_matkul, k.nomor_kelompok, k.tahun_ajaran, k.id_matkul,
       CASE WHEN k.jenis_sidang = 'Tugas Akhir' THEN 'TA' ELSE 'Semester' END AS label_sidang
FROM Sidang s
LEFT JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
WHERE s.id_sidang = ?
";
$stmt_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
if ($stmt_sidang === false) {
    die("Error saat mengambil data sidang: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$data_sidang = sqlsrv_fetch_array($stmt_sidang, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) die("Data sidang tidak ditemukan.");

$judul = $data_sidang['judul'];

$sql_check_doc = "SELECT dok_laporan, dok_final FROM Sidang WHERE id_sidang = ?";
$stmt_check_doc = sqlsrv_query($conn, $sql_check_doc, [$id_sidang]);
if ($stmt_check_doc === false) {
    die("Error saat mengambil data dokumen: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$doc_data = sqlsrv_fetch_array($stmt_check_doc, SQLSRV_FETCH_ASSOC);

// Tentukan nama file yang akan ditampilkan nanti di HTML
$nama_file_tampil = '';
if ($doc_data && !empty($doc_data['dok_laporan'])) {
    $nama_file_tampil = !empty($doc_data['dok_final']) ? $doc_data['dok_final'] : basename($doc_data['dok_laporan']);
}
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
// Ambil NIM anggota kelompok untuk notifikasi
$nims_mahasiswa = array_map(function($m) { return $m['nim']; }, $anggota_kelompok);

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
        // Kirim notifikasi ke admin (ad01) dan ke mahasiswa
        $judul_sidang = $data_sidang['judul'] ?? '';
        $nomor_kelompok = $data_sidang['nomor_kelompok'] ?? '';
        $pesan_admin = "Pengajuan sidang kelompok $nomor_kelompok dengan judul '$judul_sidang' telah disetujui dosen. Mohon dijadwalkan.";
        kirimNotifikasi('ad01', $pesan_admin, $nomorDosen, $conn);
        $pesan_mhs = "Pengajuan sidang kelompok $nomor_kelompok dengan judul '$judul_sidang' telah disetujui. Silakan menunggu penjadwalan dari admin.";
        foreach ($nims_mahasiswa as $nim_mhs) {
            kirimNotifikasi($nim_mhs, $pesan_mhs, $nomorDosen, $conn);
        }
        header("Location: dPengajuan.php"); exit();
    }
    if (isset($_POST['reject']) || isset($_POST['catatan'])) { // Trigger dari tombol utama atau dari modal
        $catatan = trim($_POST['catatan'] ?? '');
        if (empty($catatan)) {
            $_SESSION['error'] = "Silakan isi alasan penolakan.";
        } else {
            // BARU: Tambahkan kolom alasan_tolak ke query UPDATE
            $sql_update = "UPDATE Sidang SET judul = ?, status_ajuan = ?, dok_laporan = ?, dok_final = ? WHERE id_sidang = ?";
            $params = [$judul, $status_ajuan, $dok_laporan_path, $nama_file_asli, $id_sidang_existing];
            $stmt_update = sqlsrv_query($conn, $sql_update, $params);

            if ($stmt_update) {
                $_SESSION['success'] = "Sidang berhasil ditolak";
                
                // Kirim notifikasi yang lebih informatif ke mahasiswa
                $judul_sidang = $data_sidang['judul'] ?? '';
                $nomor_kelompok = $data_sidang['nomor_kelompok'] ?? '';
                
                // BARU: Sertakan alasan penolakan di notifikasi
                $pesan_mhs = "Pengajuan sidang kelompok $nomor_kelompok dengan judul '$judul_sidang' DITOLAK. Alasan: \"$catatan\". Silakan perbaiki dan ajukan kembali.";
                
                foreach ($nims_mahasiswa as $nim_mhs) {
                    kirimNotifikasi($nim_mhs, $pesan_mhs, $nomorDosen, $conn);
                }
                header("Location: dPengajuan.php"); 
                exit();
            } else {
                 $_SESSION['error'] = "Gagal memperbarui database.";
            }
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
  <link rel="stylesheet" href="../../assets/css/breadcrumb.css">
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
            <?php 
            require_once '../../control/function.php'; 
            echo generateBreadcrumb(getPageTitle('dDetailPengajuan'), 'dosen', [
                ['url' => 'dDaftarSidang.php', 'text' => 'Daftar Sidang']
            ]); 
            ?>
            <div class="mb-4">
                <h2 class="text-heading text-black" style="font-weight: 700;">Detail Pengajuan - <?= htmlspecialchars($judul) ?></h2>
            </div>
                <div class="card mb-4 info-pengajuan">
                <h5 class="fw-bold section">Informasi Pengajuan</h5>

                <div class="row mt-2">
                    <div class="col-md-6 section"> 
                        
                        <div class="info-group mb-3">
                            <div class="label-row"><i class="fa-solid fa-users me-0"></i><span class="fw-bold ms-0">Nomor Kelompok</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['nomor_kelompok'] ?? '-') ?></div>
                        </div>

                        <div class="info-group mb-3">
                            <div class="label-row"><i class="fa-solid fa-calendar-days me-0"></i><span class="fw-bold ms-0">Tahun Ajaran</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['tahun_ajaran'] ?? '-') ?></div>
                        </div>
                        
                        <div class="info-group mb-3">
                            <div class="label-row"><i class="fa-solid fa-book me-0"></i><span class="fw-bold ms-0">Mata Kuliah</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['nama_matkul'] ?? 'N/A') ?></div>
                        </div>
                        
                        <div class="info-group mb-3">
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
                        <div class="info-group mb-3">
                            <div class="label-row"><i class="fa-solid fa-file-invoice me-0"></i><span class="fw-bold ms-0">Judul Sidang</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['judul'] ?? '-') ?></div>
                        </div>
                        <div class="info-group mb-3">
                            <div class="label-row"><i class="fa-solid fa-tag me-0"></i><span class="fw-bold ms-0">Jenis Sidang</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['label_sidang']) ?></div>
                        </div>
                        <?php if ($data_sidang['jenis_sidang'] === 'Tugas Akhir' && !empty($dosen_pembimbing)): ?>
                        <div class="info-group mb-3">
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
                        <div class="info-group mb-3">
                            <div class="label-row"><i class="fa-solid fa-clipboard-question me-0"></i><span class="fw-bold ms-0">Status Pengajuan</span></div>
                            <div class="value-row ms-4"><?= htmlspecialchars($data_sidang['status_ajuan']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-semibold">Dokumen Laporan</h5>
            <div class="file-buttons-container d-flex flex-wrap">
                <div class="mt-2">
                    <?php if (!empty($doc_data) && !empty($doc_data['dok_laporan'])) : ?>
                        <a href="dDetailPengajuan.php?download=laporan&id=<?= htmlspecialchars($id_sidang) ?>" class="text-decoration-none base-tombol berkas-laporan">
                            <i class="fa-solid fa-file-lines me-2"></i><?= htmlspecialchars($nama_file_tampil) ?>
                        </a>
                    <?php else : ?>
                        <p class="text-muted">Tidak ada dokumen yang diunggah</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (
                isset($data_sidang['status_ajuan']) &&
                strtolower($data_sidang['status_ajuan']) === 'rejected' &&
                !empty($data_sidang['alasan_tolak'])
            ): ?>
                    <div class="alert alert-danger mt-4">
                        <h6 class="alert-heading fw-bold">
                            <i class="fas fa-times-circle me-2"></i>Pengajuan Ditolak
                        </h6>
                        <hr>
                        <p class="mb-1"><strong>Alasan Penolakan:</strong></p>
                        <p class="fst-italic p-2 rounded" style="background-color: #f8d7da;">
                            <?= htmlspecialchars($data_sidang['alasan_tolak']) ?>
                        </p>
                    </div>
            <?php endif; ?>
            
            <?php if ($data_sidang['status_ajuan'] === 'Pending'): ?>
            <div class="action-buttons mt-4 d-flex justify-content-between align-items-center">
                <a href="<?= $kembali_url ?>" class="btn btn-secondary btn-circle">
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
                <a href="<?= $kembali_url ?>" class="btn btn-secondary btn-circle">
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
