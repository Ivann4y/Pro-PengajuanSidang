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

// --- 1. GET THE URL PARAMETERS ---
$nomor_kelompok = $_GET['nomor_kelompok'] ?? null;
$tahun_ajaran = $_GET['tahun_ajaran'] ?? null;
$jenis_sidang = $_GET['jenis_sidang'] ?? null;
$id_matkul = $_GET['id_matkul'] ?? null;
$error_message = '';

if (!$nomor_kelompok || !$tahun_ajaran || !$jenis_sidang || !$id_matkul) {
    die("Error: Parameter URL tidak lengkap.");
}

// --- 2. FETCH DATA FOR DISPLAY (based on URL params for the whole group) ---

// a) Fetch main group info and the most relevant Sidang record
$sql_data = "
    SELECT TOP 1
        k_base.nomor_kelompok, k_base.tahun_ajaran, k_base.jenis_sidang,
        m.nama_matkul, k_base.id_matkul,
        s.id_sidang, s.judul, s.status_ajuan, s.dok_laporan
    FROM (
        SELECT TOP 1 * FROM Kelompok
        WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?
    ) AS k_base
    JOIN MataKuliah m ON k_base.id_matkul = m.id_matkul
    OUTER APPLY (
        SELECT TOP 1 s_inner.*
        FROM Kelompok k_inner
        JOIN Sidang s_inner ON k_inner.id_kelompok = s_inner.id_kelompok
        WHERE k_inner.nomor_kelompok = k_base.nomor_kelompok
          AND k_inner.tahun_ajaran = k_base.tahun_ajaran
          AND k_inner.jenis_sidang = k_base.jenis_sidang
          AND k_inner.id_matkul = k_base.id_matkul
        ORDER BY
            CASE s_inner.status_ajuan
                WHEN 'Draft' THEN 1
                WHEN 'Rejected' THEN 2
                ELSE 3
            END,
            s_inner.id_sidang DESC
    ) s
";
$params_data = [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul];
$stmt_data = sqlsrv_query($conn, $sql_data, $params_data);

if ($stmt_data === false) die('SQL Error (Data): ' . print_r(sqlsrv_errors(), true));
$data = sqlsrv_fetch_array($stmt_data, SQLSRV_FETCH_ASSOC);

if (!$data) die("Error: Data kelompok tidak ditemukan atau parameter tidak valid.");

// b) Fetch all members of this logical group
$anggota_list = [];
$sql_anggota = "SELECT m.nim, m.nama_mhs FROM Kelompok k JOIN Mahasiswa m ON k.nim = m.nim WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?";
$params_anggota = [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul];
$stmt_anggota = sqlsrv_query($conn, $sql_anggota, $params_anggota);
if ($stmt_anggota === false) die('SQL Error (Anggota): ' . print_r(sqlsrv_errors(), true));
while ($row_anggota = sqlsrv_fetch_array($stmt_anggota, SQLSRV_FETCH_ASSOC)) {
    $anggota_list[] = $row_anggota;
}

// c) Find the specific id_kelompok for the logged-in user (needed for POST actions)
$sql_id_kelompok = "SELECT id_kelompok FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ? AND nim = ?";
$params_id_kelompok = [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul, $nim_mahasiswa_logged_in];
$stmt_id_kelompok = sqlsrv_query($conn, $sql_id_kelompok, $params_id_kelompok);
$row_id = sqlsrv_fetch_array($stmt_id_kelompok, SQLSRV_FETCH_ASSOC);
if (!$row_id) die('Error: Anda tidak terdaftar sebagai anggota kelompok ini.');
$id_kelompok = $row_id['id_kelompok'];


// --- 3. HANDLE POST REQUEST (form submission) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sidang_existing = $data['id_sidang']; // Use the ID we already fetched
    $judul = trim($_POST['judul']);
    $status_ajuan = isset($_POST['submit_final']) ? 'Pending' : 'Draft';

    // File upload logic
    $allowedTypes = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/msword',
    ];
    $file_content = null;
    $file_mime_type = null;
    $dok_laporan_filename = $data['dok_laporan'] ?? null; // default to old filename if editing
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['file_laporan'];
        if ($file['size'] > 10 * 1024 * 1024) { // 10MB
            $error_message = "Ukuran file tidak boleh melebihi 10MB.";
        } elseif (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
            $error_message = "Tipe file tidak diizinkan. Gunakan PDF, DOCX, atau ZIP.";
        } else {
            $file_content = file_get_contents($file['tmp_name']);
            $file_mime_type = mime_content_type($file['tmp_name']);
            $dok_laporan_filename = basename($file['name']);
        }
    } elseif (!$id_sidang_existing && is_null($file_content)) {
        $error_message = "File laporan wajib diunggah saat membuat pengajuan baru.";
    }

    if (empty($judul)) $error_message = "Judul tidak boleh kosong.";

    if (empty($error_message)) {
        sqlsrv_begin_transaction($conn);
        try {
            if ($id_sidang_existing) { // UPDATE existing draft/rejected submission
                $sql_update = "UPDATE Sidang SET judul = ?, status_ajuan = ?";
                $params = [$judul, $status_ajuan];

                if ($file_content !== null) {
                    $sql_update .= ", dok_laporan = ?, dok_laporan_content = ?, dok_laporan_type = ?";
                    $params[] = $dok_laporan_filename;
                    $params[] = $file_content;
                    $params[] = $file_mime_type;
                }

                $sql_update .= " WHERE id_sidang = ?";
                $params[] = $id_sidang_existing;

                $stmt = sqlsrv_query($conn, $sql_update, $params);
            } else { // INSERT new submission
                $next_id_sidang = 1;
                $sql_max_id = "SELECT MAX(id_sidang) AS max_id FROM Sidang";
                $stmt_max_id = sqlsrv_query($conn, $sql_max_id);
                if ($stmt_max_id && ($row_max_id = sqlsrv_fetch_array($stmt_max_id, SQLSRV_FETCH_ASSOC))) {
                    $next_id_sidang = ($row_max_id['max_id'] ?? 0) + 1;
                }

                $sql_insert = "INSERT INTO Sidang (id_sidang, judul, waktu_pengumpulan, dok_laporan, dok_laporan_content, dok_laporan_type, status_ajuan, id_kelompok) VALUES (?, ?, GETDATE(), ?, ?, ?, ?, ?)";
                $params = [$next_id_sidang, $judul, $dok_laporan_filename, $file_content, $file_mime_type, $status_ajuan, $id_kelompok];
                $stmt = sqlsrv_query($conn, $sql_insert, $params);
            }

            if ($stmt === false) throw new Exception(print_r(sqlsrv_errors(), true));

            sqlsrv_commit($conn);
            $_SESSION['flash_message'] = "Pengajuan berhasil di " . ($status_ajuan == 'Pending' ? 'submit' : 'simpan sebagai draft') . ".";
            
            // Notification logic...
            if ($status_ajuan == 'Pending') {
                require_once __DIR__ . '/../../control/kirimNotifikasi.php';
                $nama_mahasiswa = $_SESSION['user_data']['nama_mhs'] ?? $nim_mahasiswa_logged_in;
                $nomor_dosen_pembimbing = null;
                $jenis_sidang_kelompok = $data['jenis_sidang'] ?? null;
                if ($jenis_sidang_kelompok === 'Tugas Akhir') {
                    $sql_dosen = "SELECT d.nomor_dosen FROM Bimbingan b JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
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
            }
            
            header("Location: mPengajuan.php");
            exit();

        } catch (Exception $e) {
            sqlsrv_rollback($conn);
            $error_message = "Terjadi kesalahan pada database: " . $e->getMessage();
        }
    }
}


// --- 4. PREPARE VARIABLES FOR VIEW ---
$is_edit_mode = !is_null($data['id_sidang']);
$status_ajuan = $data['status_ajuan'] ?? null;
$page_title = ($is_edit_mode) ? "Edit Pengajuan Sidang" : "Buat Pengajuan Baru";
if ($status_ajuan === 'Rejected') {
    $page_title = "Edit Pengajuan (Ditolak)";
}
$is_editable = (is_null($status_ajuan) || in_array($status_ajuan, ['Draft', 'Rejected']));
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/breadcrumb.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                <li class="NavSide__sidebar-item"><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>

        <!-- 2. Topbar untuk Mobile -->
         <div class="NavSide__topbar">
                <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
                <div class="header-icons">
                    <a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <a href="mProfil.php" title="Profil" class="profile-icon">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>

        <!-- 3. KONTEN UTAMA (SEKARANG DI DALAM #NavSide) -->
        <main class="NavSide__main-content" id="mKelolaPengajuan">
            <?php 
            // Include the function file
            require_once '../../control/function.php'; 
            // Generate breadcrumb
            echo generateBreadcrumb(getPageTitle('mKelolaPengajuan'), 'mahasiswa', [
                ['url' => 'mPengajuan.php', 'text' => 'Pengajuan']
            ]); 
            ?>
            <div class="container-fluid">
                <div class="dashboard-header">
                    <h2 class="text-heading" style="color:black;"><?= $page_title ?></h2>
                </div>

                    <div class="row">
                        <!-- Kolom Kiri: Informasi Kelompok -->
                        <div class="col-lg-5 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Informasi Kelompok</h5>
                                </div>
                                <div class="card-body">
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
                        </div>

                        <!-- Kolom Kanan: Detail Pengajuan -->
                        <div class="col-lg-7 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Detail Pengajuan</h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <?php if ($status_ajuan === 'Rejected'): ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-info-circle me-2"></i><strong>Pengajuan Ditolak.</strong> Silakan perbaiki detail di bawah dan submit ulang.
                                        </div>
                                    <?php endif; ?>

                                    <form id="pengajuan-form" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-grow-1">
                                        <div class="flex-grow-1">
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
                                                        <p><i class="fas fa-file-alt me-1"></i> File yang sudah diupload: <?= htmlspecialchars(basename($data['dok_laporan'])) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($is_editable): ?>
                                                    <label class="upload-box w-100 text-center" id="upload-box-label">
                                                        <input type="file" id="file_laporan" name="file_laporan" accept=".zip,.pdf,.docx" <?= $is_edit_mode ? '' : 'required' ?> hidden>
                                                        <div class="upload-content">
                                                            <i class="fas fa-cloud-upload-alt fa-3x text-secondary" id="upload-icon"></i>
                                                            <p class="mt-2 text-muted" id="upload-text">Klik untuk memilih file baru</p>
                                                            <p class="file-name mt-2" id="file-name-display"></p>
                                                        </div>
                                                    </label>
                                                    <?php if ($is_edit_mode): ?>
                                                        <small class="form-text text-muted">Unggah file baru hanya jika ingin menggantikan yang lama.</small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <a href="mPengajuan.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                                            <div>
                                                <?php if ($is_editable): ?>
                                                    <div class="btn-group" role="group">
                                                        <button type="submit" name="save_draft" class="btn btn-info"><i class="fas fa-save me-2"></i>Simpan Draft</button>
                                                        <button type="button" name="submit_final" class="btn btn-success" id="btn-submit-final"><i class="fas fa-paper-plane me-2"></i>Submit Final</button>
                                                    </div>
                                                <?php elseif (in_array($status_ajuan, ['Pending', 'Approved'])): ?>
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
                    </div>
                </div>
            </main>

    </div> <!-- AKHIR PEMBUNGKUS UTAMA -->

    <!-- Modal Logout -->
    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/mKelolaPengajuan.js"></script>
</body>

<script>
    <?php if (!empty($error_message)): ?>
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: '<?= addslashes(htmlspecialchars($error_message)) ?>',
        confirmButtonColor: '#4b68fb'
    });
    <?php endif; ?>
</script>
</html>