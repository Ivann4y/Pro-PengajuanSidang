<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Security & Role Check
if (!isset($_SESSION['is_logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login sebagai mahasiswa untuk mengakses halaman ini.';
    header("Location: ../../index.php");
    exit();
}

require_once '../../koneksi/koneksiAndrew.php';
$nim_mahasiswa_logged_in = $_SESSION['nim'];

// Ambil parameter dari URL
$nomor_kelompok = $_GET['nomor_kelompok'] ?? null;
$tahun_ajaran = $_GET['tahun_ajaran'] ?? null;
$jenis_sidang = $_GET['jenis_sidang'] ?? null;
$id_matkul = $_GET['id_matkul'] ?? null;
$error_message = '';
$success_message = '';

if (!$nomor_kelompok || !$tahun_ajaran || !$jenis_sidang || !$id_matkul) {
    die("Error: Parameter tidak lengkap.");
}

// Cari id_kelompok berdasarkan kombinasi dan nim login
$sql_id = "SELECT TOP 1 id_kelompok FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ? AND nim = ?";
$stmt_id = sqlsrv_query($conn, $sql_id, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul, $nim_mahasiswa_logged_in]);
$row_id = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC);
if (!$row_id) {
    die('Error: Anda tidak terdaftar sebagai anggota kelompok ini atau parameter tidak valid.');
}
$id_kelompok = $row_id['id_kelompok'];

// --- Ambil data utama pengajuan ($data) dan daftar anggota ($anggota_list) ---
// Always prioritize draft if exists
$sql_data = "SELECT TOP 1
    k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang,
    m.nama_matkul, k.id_matkul,
    s.id_sidang, s.judul, s.status_ajuan, s.dok_laporan
FROM Kelompok k
JOIN MataKuliah m ON k.id_matkul = m.id_matkul
LEFT JOIN Sidang s ON k.id_kelompok = s.id_kelompok
WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?
ORDER BY CASE WHEN s.status_ajuan = 'Draft' THEN 0 ELSE 1 END, s.id_sidang DESC";
$stmt_data = sqlsrv_query($conn, $sql_data, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
if ($stmt_data === false) {
    die('SQL Error (Data): ' . print_r(sqlsrv_errors(), true));
}
$data = sqlsrv_fetch_array($stmt_data, SQLSRV_FETCH_ASSOC);

if (!$data) {
    die("Error: Data kelompok tidak ditemukan.");
}

// Fetch group members
$anggota_list = [];
$sql_anggota = "SELECT m.nim, m.nama_mhs FROM Kelompok k JOIN Mahasiswa m ON k.nim = m.nim WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?";
$params_anggota = [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul];
$stmt_anggota = sqlsrv_query($conn, $sql_anggota, $params_anggota);
if ($stmt_anggota === false) {
    die('SQL Error (Anggota): ' . print_r(sqlsrv_errors(), true));
}
while ($row_anggota = sqlsrv_fetch_array($stmt_anggota, SQLSRV_FETCH_ASSOC)) {
    $anggota_list[] = $row_anggota;
}

// --- Baru proses POST jika ada ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sidang_existing = $_POST['id_sidang'] ?? null;
    $judul = trim($_POST['judul']);
    $status_ajuan = isset($_POST['submit_final']) ? 'Pending' : 'Draft';

    // --- File upload logic (match mTambahPengajuan/mEditPengajuan) ---
    $allowedTypes = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/msword',
    ];
    $dok_laporan_path = $data['dok_laporan'] ?? null; // default to old path if editing
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['file_laporan'];
        $fileSize = $file['size'];
        $fileType = mime_content_type($file['tmp_name']);
        if ($fileSize > 10 * 1024 * 1024) {
            $error_message = "Ukuran file tidak boleh melebihi 10MB.";
        } elseif (!in_array($fileType, $allowedTypes)) {
            $error_message = "Tipe file tidak diizinkan. Gunakan PDF, DOCX, atau ZIP.";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $unique_name = 'laporan_' . uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/../../uploads/' . $unique_name;
            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $error_message = "Gagal menyimpan file ke server.";
            } else {
                $dok_laporan_path = 'uploads/' . $unique_name;
            }
        }
    } elseif (!$id_sidang_existing && empty($dok_laporan_path)) {
        $error_message = "File laporan wajib diunggah saat membuat pengajuan baru.";
    }

    if (empty($judul)) {
        $error_message = "Judul tidak boleh kosong.";
    }

    // --- DRAFT LOGIC FIX: Always update existing draft if present ---
    if (empty($id_sidang_existing)) {
        // Check if a draft already exists for this group
        $sql_check = "SELECT TOP 1 id_sidang FROM Sidang WHERE id_kelompok = ? AND status_ajuan = 'Draft'";
        $stmt_check = sqlsrv_query($conn, $sql_check, [$id_kelompok]);
        if ($stmt_check && ($row_check = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC))) {
            $id_sidang_existing = $row_check['id_sidang'];
        }
    }

    if (empty($error_message)) {
        sqlsrv_begin_transaction($conn);
        try {
            // Decide whether to INSERT or UPDATE
            if ($id_sidang_existing) { // UPDATE existing draft
                $sql_update = "UPDATE Sidang SET judul = ?, status_ajuan = ?" .
                    ($dok_laporan_path ? ", dok_laporan = ?" : "") .
                    " WHERE id_sidang = ? AND id_kelompok = ?";
                $params = [$judul, $status_ajuan];
                if ($dok_laporan_path) $params[] = $dok_laporan_path;
                $params[] = $id_sidang_existing;
                $params[] = $id_kelompok;
                $stmt = sqlsrv_query($conn, $sql_update, $params);
            } else { // INSERT new submission
                // --- Get next id_sidang (refer to mTambahPengajuan.php) ---
                $next_id_sidang = 1;
                $sql_id = "SELECT MAX(id_sidang) AS max_id FROM Sidang";
                $stmt_id = sqlsrv_query($conn, $sql_id);
                if ($stmt_id && ($row_id = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC))) {
                    $next_id_sidang = $row_id['max_id'] !== null ? $row_id['max_id'] + 1 : 1;
                }
                // --- Insert with id_sidang ---
                $sql_insert = "INSERT INTO Sidang (id_sidang, judul, waktu_pengumpulan, dok_laporan, status_ajuan, id_kelompok) VALUES (?, ?, GETDATE(), ?, ?, ?)";
                $params = [
                    $next_id_sidang,
                    $judul,
                    $dok_laporan_path,
                    $status_ajuan,
                    $id_kelompok
                ];
                $stmt = sqlsrv_query($conn, $sql_insert, $params);
            }

            if ($stmt === false) {
                throw new Exception(print_r(sqlsrv_errors(), true));
            }

            sqlsrv_commit($conn);
            $_SESSION['flash_message'] = "Pengajuan berhasil di " . ($status_ajuan == 'Pending' ? 'submit' : 'simpan sebagai draft') . ".";

            // Kirim notifikasi ke dosen pembimbing atau pengampu
            require_once __DIR__ . '/../../control/kirimNotifikasi.php';
            $nama_mahasiswa = $_SESSION['user_data']['nama_mhs'] ?? $nim_mahasiswa_logged_in;
            $nomor_dosen_pembimbing = null;
            $jenis_sidang_kelompok = $data['jenis_sidang'] ?? null;
            if ($jenis_sidang_kelompok === 'Tugas Akhir') {
                $sql_dosen = "SELECT nomor_dosen FROM Bimbingan WHERE id_kelompok = ? AND isPembimbing = 0x01";
                $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$id_kelompok]);
                if ($stmt_dosen && ($row_dosen = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
                    $nomor_dosen_pembimbing = $row_dosen['nomor_dosen'];
                }
            } else {
                $sql_dosen = "SELECT TOP 1 nomor_dosen FROM Pengampu_Kelas WHERE id_matkul = ?";
                $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$id_matkul]);
                if ($stmt_dosen && ($row_dosen = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
                    $nomor_dosen_pembimbing = $row_dosen['nomor_dosen'];
                }
            }
            if ($nomor_dosen_pembimbing) {
                $pesan = "Mahasiswa $nama_mahasiswa ($nim_mahasiswa_logged_in) telah mengajukan sidang baru. Silakan cek pengajuan di sistem.";
                kirimNotifikasi($nomor_dosen_pembimbing, $pesan, $nim_mahasiswa_logged_in, $conn);
            }

            header("Location: mPengajuan.php");
            exit();
        } catch (Exception $e) {
            sqlsrv_rollback($conn);
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }

        // --- Always re-fetch latest data after POST if not redirecting ---
        if (!headers_sent() && empty($_SESSION['flash_message'])) {
            // Re-query Sidang data, prioritize draft
            $sql_data = "SELECT TOP 1
                k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang,
                m.nama_matkul, k.id_matkul,
                s.id_sidang, s.judul, s.status_ajuan, s.dok_laporan
            FROM Kelompok k
            JOIN MataKuliah m ON k.id_matkul = m.id_matkul
            LEFT JOIN Sidang s ON k.id_kelompok = s.id_kelompok
            WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?
            ORDER BY CASE WHEN s.status_ajuan = 'Draft' THEN 0 ELSE 1 END, s.id_sidang DESC";
            $stmt_data = sqlsrv_query($conn, $sql_data, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
            $data = sqlsrv_fetch_array($stmt_data, SQLSRV_FETCH_ASSOC);

            // Re-query anggota
            $anggota_list = [];
            $sql_anggota = "SELECT m.nim, m.nama_mhs FROM Kelompok k JOIN Mahasiswa m ON k.nim = m.nim WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?";
            $params_anggota = [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul];
            $stmt_anggota = sqlsrv_query($conn, $sql_anggota, $params_anggota);
            if ($stmt_anggota !== false) {
                while ($row_anggota = sqlsrv_fetch_array($stmt_anggota, SQLSRV_FETCH_ASSOC)) {
                    $anggota_list[] = $row_anggota;
                }
            }
        }
    }
}

// 3. GET Request Data Fetching (Displaying the form)
$is_edit_mode = !is_null($data['id_sidang']);
$status_ajuan = $data['status_ajuan'] ?? null;
$page_title = $is_edit_mode ? "Edit Pengajuan Sidang" : "Buat Pengajuan Baru";
$is_editable = (is_null($status_ajuan) || $status_ajuan === 'Draft');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/mKelolaPengajuan.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>
    <script>
        console.log('nomor_kelompok:', <?= json_encode($nomor_kelompok) ?>);
        console.log('tahun_ajaran:', <?= json_encode($tahun_ajaran) ?>);
        console.log('jenis_sidang:', <?= json_encode($jenis_sidang) ?>);
        console.log('id_matkul:', <?= json_encode($id_matkul) ?>);
        console.log('id_kelompok:', <?= json_encode($id_kelompok) ?>);
        console.log('data:', <?= json_encode($data) ?>);
        console.log('anggota_list:', <?= json_encode($anggota_list) ?>);
    </script>
    <div id="NavSide"> <!-- AWAL PEMBUNGKUS UTAMA -->

        <!-- 1. Sidebar -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>

        <!-- 2. Topbar untuk Mobile -->
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        </div>

        <!-- 3. KONTEN UTAMA (SEKARANG DI DALAM #NavSide) -->
        <main class="NavSide__main-content" id="mKelolaPengajuan">
            <div class="container-fluid">
                <div class="dashboard-header">
                    <h2 class="text-heading" style="color:black;"><?= $page_title ?></h2>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Informasi Kelompok</h5>
                    </div>
                    <div class="card-body">
                        <!-- ... Konten Informasi Kelompok ... -->
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <p><strong>Nomor Kelompok:</strong> <?= htmlspecialchars($data['nomor_kelompok']) ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><strong>Tahun Ajaran:</strong> <?= htmlspecialchars($data['tahun_ajaran']) ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><strong>Mata Kuliah:</strong> <?= htmlspecialchars($data['nama_matkul']) ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><strong>Jenis Sidang:</strong> <?= htmlspecialchars($data['jenis_sidang']) ?></p>
                            </div>
                        </div>
                        <hr>
                        <h6>Anggota Kelompok:</h6>
                        <ul class="list-unstyled">
                            <?php foreach ($anggota_list as $anggota): ?>
                                <li><i class="fas fa-user fa-fw me-2"></i><?= htmlspecialchars($anggota['nama_mhs']) ?> (<?= htmlspecialchars($anggota['nim']) ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <form id="pengajuan-form" method="POST" enctype="multipart/form-data">
                            <!-- ... Konten Form ... -->
                            <input type="hidden" name="id_kelompok" value="<?= htmlspecialchars($id_kelompok) ?>">
                            <?php if ($is_edit_mode): ?>
                                <input type="hidden" name="id_sidang" value="<?= htmlspecialchars($data['id_sidang']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($data['judul'] ?? '') ?>" <?= $is_editable ? 'required' : 'readonly' ?>>
                            </div>

                            <div class="mb-4">
                                <label for="file_laporan" class="form-label">
                                    File Laporan (ZIP, PDF, DOCX, maks 10MB)
                                    <?php if (!$is_edit_mode): ?><span class="text-danger">*</span><?php endif; ?>
                                </label>
                                <?php if (!empty($data['dok_laporan'])): ?>
                                    <div class="mb-2">
                                        <a href="../../<?= htmlspecialchars($data['dok_laporan']) ?>" target="_blank">
                                            Lihat file yang sudah diupload: <?= htmlspecialchars(basename($data['dok_laporan'])) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <label class="upload-box w-100 text-center" id="upload-box-label">
                                    <input type="file" id="file_laporan" name="file_laporan" accept=".zip,.pdf,.docx" <?= $is_editable ? ($is_edit_mode ? '' : 'required') : 'disabled' ?> hidden>
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-secondary" id="upload-icon"></i>
                                        <p class="mt-2 text-muted" id="upload-text">Klik untuk memilih file</p>
                                        <p class="file-name mt-2" id="file-name-display"></p>
                                    </div>
                                </label>
                                <?php if ($is_edit_mode): ?>
                                    <small class="form-text text-muted">Unggah file baru untuk menggantikan yang lama. Kosongkan jika tidak ingin mengubah.</small>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">

                                <!-- SISI KIRI -->
                                <a href="mPengajuan.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>

                                <!-- SISI KANAN -->
                                <div>
                                    <?php if ($is_editable): ?>
                                        <!-- Gunakan btn-group untuk menggabungkan tombol -->
                                        <div class="btn-group" role="group">
                                            <button type="submit" name="save_draft" class="btn btn-info"><i class="fas fa-save me-2"></i>Simpan Draft</button>
                                            <button type="submit" name="submit_final" class="btn btn-success" id="btn-submit-final"><i class="fas fa-paper-plane me-2"></i>Submit Final</button>
                                        </div>
                                    <?php elseif ($status_ajuan === 'Pending'): ?>
                                        <div class="alert alert-warning mb-0 p-2">
                                            <i class="fas fa-lock me-2"></i>Pengajuan ini telah disubmit dan tidak dapat diubah.
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </main>

    </div> <!-- AKHIR PEMBUNGKUS UTAMA -->

    <!-- Modal Logout -->
    <!-- ... (copy from mPengajuan.php) ... -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle Logic
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            if (menuToggle) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            // File Upload UI Logic
            const fileInput = document.getElementById('file_laporan');
            const uploadBox = document.getElementById('upload-box-label');
            const fileNameDisplay = document.getElementById('file-name-display');
            const uploadIcon = document.getElementById('upload-icon');
            const uploadText = document.getElementById('upload-text');

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileNameDisplay.textContent = this.files[0].name;
                    uploadIcon.style.display = 'none';
                    uploadText.style.display = 'none';
                    uploadBox.classList.add('file-selected');
                } else {
                    fileNameDisplay.textContent = '';
                    uploadIcon.style.display = 'block';
                    uploadText.style.display = 'block';
                    uploadBox.classList.remove('file-selected');
                }
            });

            // Submit Confirmation
            const submitBtn = document.getElementById('btn-submit-final');
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin submit pengajuan ini? Setelah submit, pengajuan tidak dapat diedit lagi.')) {
                        e.preventDefault(); // Cancel the form submission
                    }
                });
            }
        });
    </script>
</body>

</html>