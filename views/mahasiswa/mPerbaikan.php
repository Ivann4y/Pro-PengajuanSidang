<?php
session_start(); // Selalu mulai sesi terlebih dahulu

// Cek apakah ada sesi 'is_logged_in' dan nilainya true
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Jika tidak, redirect ke halaman login
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Gunakan path absolut dari root web server Anda
    header("Location: /Sidang/Pro-PengajuanSidang/index.php"); 
    exit(); // Hentikan eksekusi skrip
}

// Cek apakah role-nya adalah 'mahasiswa'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    // Jika bukan mahasiswa, tendang keluar
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: /Sidang/Pro-PengajuanSidang/index.php");
    exit(); // Hentikan eksekusi skrip
}
include '../../koneksi/koneksiAndrew.php'; // Koneksi ke SQL Server Anda

$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
}

// Cek apakah ID sidang ada di dalam session.
if (!isset($_SESSION['selected_sidang_id']) || !is_numeric($_SESSION['selected_sidang_id'])) {
    die("Error: Tidak ada sidang yang dipilih. Silakan kembali ke halaman daftar sidang dan pilih salah satu.");
}
$id_sidang = (int) $_SESSION['selected_sidang_id'];


// === LOGIKA FETCH DATA ===
$nama_mahasiswa = '';
$status_revisi = '';
$status_pengajuan = 'Belum Disetujui';
$catatan_list = [];

// Query untuk mengambil informasi dasar
$query_info = "
    SELECT TOP 1 ds.status_revisi, m.nama_mhs
    FROM Detail_Sidang ds
    JOIN Sidang s ON ds.id_sidang = s.id_sidang
    JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
    JOIN Mahasiswa m ON km.nim = m.nim
    WHERE ds.id_sidang = ?
";
$params_info = array($id_sidang);
$stmt_info = sqlsrv_query($conn, $query_info, $params_info);
if ($stmt_info === false) {
    die("Error saat menjalankan query info: <br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$data_info = sqlsrv_fetch_array($stmt_info, SQLSRV_FETCH_ASSOC);
if (!$data_info) {
    die("Error: Data sidang dengan ID " . htmlspecialchars($id_sidang) . " tidak ditemukan.");
}
$nama_mahasiswa = $data_info['nama_mhs'];
$status_revisi = $data_info['status_revisi'];
if (empty(trim($status_revisi))) {
    $status_revisi = 'Belum Ada Revisi';
}

// Query untuk mengambil catatan perbaikan
$query_catatan = "
    SELECT ds.catatan_sidang, d.nama_dosen
    FROM Detail_Sidang ds
    JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
    WHERE ds.id_sidang = ?
    ORDER BY d.nama_dosen ASC
";
$params_catatan = array($id_sidang);
$stmt_catatan = sqlsrv_query($conn, $query_catatan, $params_catatan);
if ($stmt_catatan === false) {
    die("Error saat menjalankan query catatan: <br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
while ($row = sqlsrv_fetch_array($stmt_catatan, SQLSRV_FETCH_ASSOC)) {
    $catatan_list[] = $row;
}

// === LOGIKA FILE UPLOAD ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        $folder_target = "uploads/";
        if (!file_exists($folder_target)) {
            mkdir($folder_target, 0755, true);
        }

        $file_asli = basename($_FILES["fileInput"]["name"]);
        $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
        $file_unik = 'revisi_' . $id_sidang . '_' . time() . '.' . $ekstensi_file;
        $path_target = $folder_target . $file_unik;
        $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");

        if (!in_array($ekstensi_file, $ekstensi_diizinkan) || $_FILES["fileInput"]["size"] > 5242880) { // Max 5MB
            $_SESSION['pesan'] = "Error: Format atau ukuran file tidak sesuai.";
        } else {
            if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                $query_update_dokumen = "
                    UPDATE Detail_Sidang 
                    SET dok_revisi = ?, status_revisi = 'Menunggu Persetujuan' 
                    WHERE id_sidang = ?
                ";
                $params_update = array($path_target, $id_sidang);
                $stmt_update = sqlsrv_query($conn, $query_update_dokumen, $params_update);

                if ($stmt_update) {
                    $_SESSION['pesan'] = "Sukses: File revisi '" . htmlspecialchars($file_asli) . "' berhasil diunggah.";
                } else {
                    unlink($path_target);
                    $_SESSION['pesan'] = "Error: Gagal menyimpan informasi file ke database.";
                }
            } else {
                $_SESSION['pesan'] = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
            }
        }
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Sidang & Perbaikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/mPerbaikan.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" /></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mdetailSidang.php"><span
                            class="fw-semibold">Detail Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span
                            class="fw-semibold">Perbaikan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mNilaiakhir.php"><span
                            class="fw-semibold">Nilai Akhir</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mSidang.php"><span
                            class="fw-semibold">Kembali</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        </div>
        <main class="NavSide__main-content">
            <div
                class="page-content-header-wrapper d-flex flex-column flex-md-row justify-content-md-between align-items-md-start">
                <h1 class="fs-2">Detail Sidang - Sistem Pengajuan Sidang</h1>
                <div class="d-flex flex-column align-items-start align-items-md-end">
                    <span class="badge-custom status-belum-disetujui mb-2">Status Pengajuan :
                        <?php echo htmlspecialchars($status_pengajuan); ?></span>
                    <span
                        class="badge-custom status-<?php echo strtolower(str_replace(' ', '-', $status_revisi)); ?>">Status
                        Revisi : <?php echo htmlspecialchars($status_revisi); ?></span>
                </div>
            </div>
            <h1 class="fs-4 fw-semibold mb-3">Catatan Perbaikan - <?php echo htmlspecialchars($nama_mahasiswa); ?></h1>
            <div class="mt-4">
                <?php if (empty($catatan_list)): ?>
                    <div class="alert alert-info">Belum ada catatan perbaikan untuk sidang ini.</div>
                <?php else: ?>
                    <?php foreach ($catatan_list as $index => $catatan): ?>
                        <div class="card-comment mb-3" data-bs-toggle="modal"
                            data-bs-target="#modalDetail<?php echo $index; ?>">
                            <strong><?php echo htmlspecialchars($catatan['nama_dosen']); ?> - Penguji</strong>
                            <p class="mt-2 mb-0 text-truncate-2">
                                <?php echo htmlspecialchars($catatan['catatan_sidang']); ?><span
                                    class="text-selengkapnya">Selengkapnya...</span>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="revision-card mt-4">
                <h5 class="fw-bold" style="color:#4B68FB;">Dokumen Revisi</h5>
                <form id="revisionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                    enctype="multipart/form-data">
                    <label for="fileInput" class="upload-area-v2 mt-3" id="uploadArea">
                        <div id="initial-state"><i class="bi bi-file-earmark-arrow-up fs-1 text-secondary"></i></div>
                        <div id="selected-state" class="d-none"><i
                                class="bi bi-file-earmark-text-fill fs-1 text-primary"></i></div>
                        <p id="upload-prompt-text" class="text-muted mt-2">Unggah berkas revisi (.pdf, .docx, .pptx,
                            .zip)</p>
                    </label>
                    <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
                    <div class="text-center mt-2">
                        <p id="fileNameDisplay" class="fw-bold mb-0"></p>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-custom-primary" id="openConfirmModalBtn"
                            data-bs-toggle="modal" data-bs-target="#modalKonfirmasi" >Kirim</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <?php foreach ($catatan_list as $index => $catatan): ?>
        <div class="modal fade" id="modalDetail<?php echo $index; ?>" tabindex="-1"
            aria-labelledby="modalDetailLabel<?php echo $index; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title fs-5" id="modalDetailLabel<?php echo $index; ?>">Detail Catatan dari
                            <?php echo htmlspecialchars($catatan['nama_dosen']); ?>
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($catatan['catatan_sidang']); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalKonfirmasiLabel">Perhatian</h4>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda sudah yakin ingin mengupload dokumen revisi ini?</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-tolak me-3" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-kirim me-3" id="confirmSubmitBtn">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../assets/js/mPerbaikan.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (!empty($pesan)): ?>
        <script>
            Swal.fire({
                title: '<?php echo stripos($pesan, "sukses") !== false ? "Berhasil" : "Error"; ?>',
                text: '<?php echo addslashes(preg_replace('/^(Sukses|Error): /i', '', $pesan)); ?>',
                icon: '<?php echo stripos($pesan, "sukses") !== false ? "success" : "error"; ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            });
        </script>
    <?php endif; ?>
</body>

</html>