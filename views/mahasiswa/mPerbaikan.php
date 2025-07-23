<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

// 2. Cek jika role pengguna BUKAN 'mahasiswa'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

include '../../koneksi/koneksiJoin.php';
require_once __DIR__ . '/../../control/kirimNotifikasi.php';
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
// === LOGIKA FETCH DATA ===
$nama_mahasiswa = '';
$status_revisi = '';
$status_pengajuan = 'Belum Disetujui'; // Default value
$catatan_list = [];

// === FIX: Query untuk mengambil informasi dasar ===
// Menggunakan JOIN yang benar: Sidang -> Kelompok -> Mahasiswa
$query_info = "
    SELECT TOP 1 ds.status_revisi, m.nama_mhs, s.status_ajuan, m.nim
    FROM Sidang s
    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    JOIN Mahasiswa m ON k.nim = m.nim
    WHERE s.id_sidang = ?
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
$nim_mahasiswa = $data_info['nim'];




// === FIX: Menggunakan nilai varchar dari DB untuk status revisi ===
$status_revisi_from_db = $data_info['status_revisi'];
if (empty($status_revisi_from_db)) {
    $status_revisi = 'Belum Ada Revisi';
} else {
    $status_revisi = $status_revisi_from_db; // e.g., 'Menunggu Persetujuan', 'Disetujui'
}

// Query untuk mengambil catatan perbaikan (sudah benar)
$query_catatan = "
    SELECT ds.catatan_sidang, d.nama_dosen
    FROM Detail_Sidang ds
    JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
    WHERE ds.id_sidang = ? AND ds.catatan_sidang IS NOT NULL AND ds.catatan_sidang <> ''
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

// Ambil id_kelompok dari nim
$sql_k = "SELECT id_kelompok FROM Kelompok WHERE nim = ?";
$params_k = [$nim_mahasiswa];
$stmt_k = sqlsrv_query($conn, $sql_k, $params_k);

if ($stmt_k === false) {
    die("Query id_kelompok gagal: " . print_r(sqlsrv_errors(), true));
}

$data_k = sqlsrv_fetch_array($stmt_k, SQLSRV_FETCH_ASSOC);



$id_kelompok = $data_k['id_kelompok'] ?? null;

if (!$id_kelompok) {
    $_SESSION['pesan'] = "Error: ID kelompok tidak ditemukan untuk NIM ini.";
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}


// Ambil semua dosen penguji/pembimbing dari Penjadwalan (tanpa filter peran)
$daftar_dosen = [];
$sql_dosen = "SELECT nomor_dosen FROM Penjadwalan WHERE id_sidang = ?";
$stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$id_sidang]);

if ($stmt_dosen) {
    while ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC)) {
        $daftar_dosen[] = $row['nomor_dosen'];
    }
} else {
    die("Gagal mengambil data dosen: " . print_r(sqlsrv_errors(), true));
}


// === LOGIKA FILE UPLOAD ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        $file_asli = basename($_FILES["fileInput"]["name"]);
        $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
        $file_unik = 'revisi_' . $id_sidang . '_' . time() . '.' . $ekstensi_file;

        $folder_target = __DIR__ . "/uploads/";
        if (!file_exists($folder_target)) {
            mkdir($folder_target, 0755, true);
        }

        $path_target = $folder_target . $file_unik;
        $path_relatif = "views/mahasiswa/uploads/" . $file_unik;
        $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");

        if (!in_array($ekstensi_file, $ekstensi_diizinkan) || $_FILES["fileInput"]["size"] > 5242880) {
            $_SESSION['pesan'] = "Error: Format atau ukuran file tidak sesuai.";
        } else {
            if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {

                // Ambil id_matkul dari Kelompok
                $sql_matkul = "SELECT mk.id_matkul 
               FROM Sidang s
               JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
               JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
               WHERE s.id_sidang = ?";
                $stmt_matkul = sqlsrv_query($conn, $sql_matkul, [$id_sidang]);
                $id_matkul = null;
                if ($stmt_matkul && ($row = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC))) {
                    $id_matkul = $row['id_matkul'];
                }


                // Loop setiap dosen
                error_log("Jumlah dosen yang ditemukan: " . count($daftar_dosen));
                error_log("Daftar dosen: " . print_r($daftar_dosen, true));

                // Ambil semua nomor_dosen dari Detail_Sidang untuk id_sidang ini
                $sql_dosen_sidang = "SELECT nomor_dosen FROM Detail_Sidang WHERE id_sidang = ?";
                $stmt_dosen_sidang = sqlsrv_query($conn, $sql_dosen_sidang, [$id_sidang]);
                $daftar_dosen = [];

                while ($row = sqlsrv_fetch_array($stmt_dosen_sidang, SQLSRV_FETCH_ASSOC)) {
                    $daftar_dosen[] = $row['nomor_dosen'];
                }

                foreach ($daftar_dosen as $nomor_dosen) {
                    // Update dokumen revisi ke semua dosen yang sudah punya baris Detail_Sidang
                    $update_sql = "UPDATE Detail_Sidang 
                   SET dok_revisi = ?, nama_file = ?, status_revisi = 'Pending'
                   WHERE id_sidang = ? AND nomor_dosen = ?";
                    $update_params = [$path_relatif, $file_asli, $id_sidang, $nomor_dosen];
                    $stmt_update = sqlsrv_query($conn, $update_sql, $update_params);

                    if (!$stmt_update) {
                        error_log("Gagal update Detail_Sidang untuk dosen $nomor_dosen: " . print_r(sqlsrv_errors(), true));
                    } else {
                        // Kirim notifikasi
                        $nama_mahasiswa = $nama_mahasiswa ?? ($data_info['nama_mhs'] ?? $nim_mahasiswa);
                        $pesan_notif = "Mahasiswa $nama_mahasiswa ($nim_mahasiswa) telah mengunggah revisi dokumen sidang. Silakan cek di sistem.";
                        kirimNotifikasi($nomor_dosen, $pesan_notif, $nim_mahasiswa, $conn);
                    }
                }


                $_SESSION['pesan'] = "Sukses: File revisi '" . htmlspecialchars($file_asli) . "' berhasil diunggah.";
            } else {
                $_SESSION['pesan'] = "Error: Gagal memindahkan file.";
            }
        }

        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}


// Tambahan: Query detail sidang agar $data_sidang tidak undefined
$data_sidang = null;
$data_matkul = null;
$sql_utama = "SELECT s.id_sidang, s.judul, CAST(k.jenis_sidang AS VARCHAR(20)) AS jenis_sidang, s.id_kelompok, s.dok_laporan, s.status_ajuan, k.nomor_kelompok FROM Sidang s JOIN Kelompok k ON s.id_kelompok = k.id_kelompok WHERE s.id_sidang = ?";
$stmt_utama = sqlsrv_prepare($conn, $sql_utama, array(&$id_sidang));
if ($stmt_utama && sqlsrv_execute($stmt_utama)) {
    $data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);
    if ($data_sidang && $data_sidang['jenis_sidang'] === 'Semester') {
        $sql_matkul = "SELECT TOP 1 mk.nama_matkul FROM MataKuliah mk JOIN Kelompok k ON mk.id_matkul = k.id_matkul JOIN Sidang AS s ON k.id_kelompok = s.id_kelompok WHERE s.id_sidang = ?";
        $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
        if ($stmt_matkul) {
            $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
        }
    }
    // Ambil nomor kelompok setelah $data_sidang berhasil diisi
    $nomor_kelompok = isset($data_sidang['nomor_kelompok']) ? $data_sidang['nomor_kelompok'] : '-';
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
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mdetailSidang.php"><span class="fw-semibold">Detail Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span class="fw-semibold">Perbaikan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mNilaiakhir.php"><span class="fw-semibold">Nilai Akhir</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mSidang.php"><span class="fw-semibold">Kembali</span></a></li>
            </ul>
        </div>

        <div id="page-content-wrapper">
            <div class="NavSide__topbar">
                <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            </div>
            <main class="NavSide__main-content" id="mPerbaikan">
                <?php 
                require_once '../../control/function.php'; 
                echo generateBreadcrumb(getPageTitle('mPerbaikan'), 'mahasiswa', [
                    ['url' => 'mSidang.php', 'text' => 'Sidang']
                ]); 
                ?>
                <div
                    class="page-content-header-wrapper d-flex flex-column flex-md-row justify-content-md-between align-items-md-start">
                    <h2>Detail Sidang -
                        <?php
                        if (isset($data_sidang['jenis_sidang']) && $data_sidang['jenis_sidang'] === 'Tugas Akhir') {
                            echo !empty($data_sidang['judul']) ? htmlspecialchars($data_sidang['judul']) : 'Tugas Akhir';
                        } elseif (isset($data_sidang['jenis_sidang']) && $data_sidang['jenis_sidang'] === 'Semester' && !empty($data_matkul)) {
                            echo htmlspecialchars($data_matkul['nama_matkul']);
                        }
                        ?>
                    </h2>
                    <div class="d-flex flex-column align-items-start align-items-md-end">
                        <span
                            class="badge-custom status-<?php echo strtolower(str_replace(' ', '-', $status_revisi)); ?>">Status
                            Revisi : <?php echo htmlspecialchars($status_revisi); ?></span>
                    </div>
                </div>
                <h1 class="fs-4 fw-semibold mb-3">Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($nomor_kelompok); ?></h1>
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
                                data-bs-toggle="modal" data-bs-target="#modalKonfirmasi">Kirim</button>
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

        <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalKonfirmasiLabel">Konfirmasi Unggah</h4>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin mengunggah file revisi ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-tolak" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-kirim" id="confirmSubmitBtn">Kirim</button>
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