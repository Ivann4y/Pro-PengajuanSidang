<?php
session_start();
// 1. PASTIKAN ANDA MEMBUAT FILE INI DAN MENGISINYA DENGAN KONEKSI DATABASE ANDA
require "../../koneksi.php";

$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
}

// 2. HALAMAN INI HARUS DIAKSES DENGAN ID SIDANG DI URL (contoh: mPerbaikan.php?id_sidang=101)
if (!isset($_GET['id_sidang']) || !is_numeric($_GET['id_sidang'])) {
    die("Error: ID Sidang tidak valid atau tidak ditemukan di URL.");
}
$id_sidang = (int) $_GET['id_sidang'];

// === LOGIKA FETCH DATA DARI DATABASE (DISESUAIKAN DENGAN SKEMA ANDA) ===
$nama_mahasiswa = '';
$status_revisi = '';
$catatan_list = [];

// Query untuk mengambil info dasar (nama mahasiswa & status)
// ===================================================================================
// PERUBAHAN UTAMA DI SINI: Ganti 'TOP 1' (SQL Server) menjadi 'LIMIT 1' (MySQL)
// ===================================================================================
$query_info = "
    SELECT ds.status_revisi, m.nama_mhs
    FROM Detail_Sidang ds
    JOIN Mahasiswa m ON ds.nim = m.nim
    WHERE ds.id_sidang = ?
    LIMIT 1 
";
// Note: LIMIT 1 diletakkan di akhir query.

$stmt_info = mysqli_prepare($koneksi, $query_info);
mysqli_stmt_bind_param($stmt_info, "i", $id_sidang);
mysqli_stmt_execute($stmt_info);
$result_info = mysqli_stmt_get_result($stmt_info);
$data_info = mysqli_fetch_assoc($result_info);

if (!$data_info) {
    die("Error: Data sidang dengan ID " . htmlspecialchars($id_sidang) . " tidak ditemukan. Pastikan ID dan data di tabel Detail_Sidang sudah benar, dan kolom 'nim' sudah terisi.");
}
$nama_mahasiswa = $data_info['nama_mhs'];
$status_revisi = $data_info['status_revisi'];

// Query untuk mengambil semua catatan revisi dari para penguji (Query ini sudah benar)
$query_catatan = "
    SELECT ds.catatan_sidang, d.nama_dosen
    FROM Detail_Sidang ds
    JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
    WHERE ds.id_sidang = ?
    ORDER BY d.nama_dosen ASC
";
$stmt_catatan = mysqli_prepare($koneksi, $query_catatan);
mysqli_stmt_bind_param($stmt_catatan, "i", $id_sidang);
mysqli_stmt_execute($stmt_catatan);
$result_catatan = mysqli_stmt_get_result($stmt_catatan);
while ($row = mysqli_fetch_assoc($result_catatan)) {
    $catatan_list[] = $row;
}

// === LOGIKA FILE UPLOAD (DISESUAIKAN DENGAN SKEMA ANDA) ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        $folder_target = "uploads/";
        if (!file_exists($folder_target)) {
            if (!mkdir($folder_target, 0755, true)) {
                $pesan = "Error: Gagal membuat folder uploads.";
            }
        }

        if (empty($pesan)) {
            $file_asli = basename($_FILES["fileInput"]["name"]);
            $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
            // Membuat nama file unik untuk menghindari tumpang tindih
            $file_unik = 'revisi_' . $id_sidang . '_' . time() . '.' . $ekstensi_file;
            $path_target = $folder_target . $file_unik;

            $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");
            if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
                $pesan = "Error: Format file tidak diizinkan. Hanya .pdf, .docx, .pptx, dan .zip yang boleh diunggah.";
            } elseif ($_FILES["fileInput"]["size"] > 5242880) { // 5 MB
                $pesan = "Error: Ukuran file terlalu besar. Maksimal 5 MB.";
            }

            if (empty($pesan)) {
                if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                    // Logika disesuaikan: UPDATE kolom, bukan INSERT baris baru.
                    $query_update_dokumen = "
                        UPDATE Detail_Sidang 
                        SET dok_revisi = ?, status_revisi = 'Menunggu Persetujuan' 
                        WHERE id_sidang = ?
                    ";
                    $stmt_update = mysqli_prepare($koneksi, $query_update_dokumen);
                    mysqli_stmt_bind_param($stmt_update, "si", $path_target, $id_sidang);

                    if (mysqli_stmt_execute($stmt_update)) {
                        $_SESSION['pesan'] = "Sukses: File revisi '" . htmlspecialchars($file_asli) . "' berhasil diunggah.";
                    } else {
                        unlink($path_target); // Hapus file yang sudah ter-upload jika gagal update DB
                        $_SESSION['pesan'] = "Error: Gagal menyimpan informasi file ke database.";
                    }

                    // Redirect untuk mencegah re-submit form
                    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?id_sidang=" . $id_sidang);
                    exit();
                } else {
                    $pesan = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
                }
            }
        }
    } elseif (isset($_FILES["fileInput"])) {
        // Logika untuk menangani error upload bawaan PHP
        switch ($_FILES["fileInput"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $pesan = "Error: Ukuran file melebihi batas yang diizinkan server.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $pesan = "Error: Tidak ada file yang dipilih.";
                break;
            default:
                $pesan = "Error: Terjadi kesalahan saat mengunggah file (Kode: " . $_FILES["fileInput"]["error"] . ").";
                break;
        }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        html {
            overflow-y: scroll;
        }

        body {
            background-color: #FFFFFF;
        }

        #NavSide {
            display: flex;
            min-height: 100vh;
        }

        #page-content-wrapper {
            flex-grow: 1;
            margin-left: 280px;
            transition: margin-left 0.4s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .NavSide__main-content {
            padding: 2rem;
            margin-left: 30px;
            margin-right: 40px;
            flex-grow: 1;
            overflow-y: auto;
            background-color: #ffffff !important;
        }

        .NavSide__sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #4B68FB;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.4s ease-in-out;
        }

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
        }

        .NavSide__sidebar-nav {
            width: 100%;
            padding: 0;
            list-style: none;
            flex-grow: 1;
        }

        .NavSide__sidebar-item {
            position: relative;
            display: block;
            width: 100%;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .NavSide__sidebar-item a {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-decoration: none;
            color: #fff;
            padding: 5% 2%;
            height: 60px;
            box-sizing: border-box;
        }

        .NavSide_sidebar-item.NavSide_sidebar-item--active {
            background: #ffffff;
        }

        .NavSide_sidebar-item.NavSide_sidebar-item--active a {
            color: #4B68FB;
        }

        .NavSide__sidebar-item b:nth-child(1),
        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            height: 20px;
            width: 100%;
            background: #fff;
            display: none;
        }

        .NavSide__sidebar-item b:nth-child(1) {
            top: -20px;
        }

        .NavSide__sidebar-item b:nth-child(2) {
            bottom: -20px;
        }

        .NavSide__sidebar-item b:nth-child(1)::before,
        .NavSide__sidebar-item b:nth-child(2)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #4B68FB;
        }

        .NavSide__sidebar-item b:nth-child(1)::before {
            border-bottom-right-radius: 20px;
        }

        .NavSide__sidebar-item b:nth-child(2)::before {
            border-top-right-radius: 20px;
        }

        .NavSide_sidebar-item.NavSide_sidebar-item--active b:nth-child(1),
        .NavSide_sidebar-item.NavSide_sidebar-item--active b:nth-child(2) {
            display: block;
        }

        @media (max-width: 992px) {
            .NavSide__sidebar {
                transform: translateX(-280px);
            }

            .NavSide_sidebar.NavSide_sidebar--active-mobile {
                transform: translateX(0);
            }

            #page-content-wrapper {
                margin-left: 0;
            }

            .NavSide__toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 20px;
                z-index: 1001;
                transition: transform 0.4s ease-in-out;
            }

            .NavSide_toggle.NavSide_toggle--active {
                transform: translateX(280px);
            }

            .NavSide__main-content {
                padding-top: 60px;
                margin-left: 0;
                margin-right: 0;
                padding: 60px 1rem 1rem 1rem;
            }
        }

        .NavSide__toggle {
            display: none;
            font-size: 2rem;
            cursor: pointer;
            color: #4B68FB;
        }

        .NavSide__toggle .close {
            display: none;
        }

        .NavSide_toggle.NavSide_toggle--active .open {
            display: none;
        }

        .NavSide_toggle.NavSide_toggle--active .close {
            display: block;
        }

        .page-content-header-wrapper h1 {
            font-size: 2rem;
        }

        .badge-custom {
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
            color: white;
        }

        .status-belum-disetujui {
            background-color: #FFA3A3;
        }

        .status-menunggu-persetujuan {
            background-color: #FFD56F;
            color: #5d4a1a;
        }

        .status-disetujui {
            background-color: #A3E4D7;
            color: #0E6655;
        }

        .card-comment {
            background-color: #f1f3f5;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            border: none;
        }

        .card-comment:hover {
            background-color: #4B68FB;
            color: #fff;
        }

        .card-comment:hover strong,
        .card-comment:hover .text-selengkapnya {
            color: #fff;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .text-selengkapnya {
            font-weight: 600;
            color: #4B68FB;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-left: 4px;
        }

        .revision-card {
            background-color: white;
            border-radius: 1.5rem;
            padding: 2rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .upload-area-v2 {
            background-color: #f8f9fa;
            border: 2px dashed #e0e0e0;
            border-radius: 1rem;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            min-height: 180px;
        }

        .upload-area-v2:hover {
            background-color: #f1f3f5;
        }

        .btn-custom-primary {
            background-color: #4FD382;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 15px rgba(75, 254, 159, 0.87);
            transition: all 0.2s ease-in-out;
        }

        .btn-custom-primary:hover {
            background-color: #3FA970;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(75, 254, 159, 0.87);
            color: white;
        }

        .btn-custom-primary:disabled {
            background-color: #e2e8f0;
            color: #94a3b8;
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        #modalKonfirmasi .modal-content {
            background-color: #ffffff;
            border-radius: 24px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            text-align: center;
        }

        #modalKonfirmasi .modal-header {
            border-bottom: none;
            padding-bottom: 0;
            justify-content: center;
        }

        #modalKonfirmasi .modal-title {
            font-weight: 700;
            font-size: 1.75rem;
            color: #333;
            width: 100%;
        }

        #modalKonfirmasi .modal-body p {
            font-size: 1rem;
            color: #555;
            margin-top: 0.5rem;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        #modalKonfirmasi .modal-body .d-flex {
            justify-content: center !important;
            gap: 1rem;
        }

        #modalKonfirmasi .modal-body button {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border: none;
            color: white;
            min-width: 140px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        #modalKonfirmasi .modal-body button:hover {
            transform: translateY(-3px);
        }

        #modalKonfirmasi .btn-tolak {
            background-color: #FF7171;
        }

        #modalKonfirmasi .btn-tolak:hover {
            box-shadow: 0 6px 25px rgba(255, 113, 113, 0.6);
        }

        #modalKonfirmasi #confirmSubmitBtn {
            background-color: #4FD382;
        }

        #modalKonfirmasi #confirmSubmitBtn:hover {
            box-shadow: 0 6px 25px rgba(79, 211, 130, 0.6);
        }
    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img
                    src="https://astra.ac.id/wp-content/uploads/2023/10/logo-Astra-Digital-white-1.png"
                    alt="ASTRAtech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a
                        href="mdetailSidang.php?id_sidang=<?php echo $id_sidang; ?>"><span class="fw-semibold">Detail
                            Pengajuan</span></a></li>
                <li class="NavSide_sidebar-item NavSide_sidebar-item--active"><b></b><b></b><a
                        href="mPerbaikan.php?id_sidang=<?php echo $id_sidang; ?>"><span
                            class="fw-semibold">Perbaikan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a
                        href="mNilaiakhir.php?id_sidang=<?php echo $id_sidang; ?>"><span class="fw-semibold">Nilai
                            Akhir</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mSidang.php"><span class="fw-semibold">Kembali
                            ke Daftar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        <div id="page-content-wrapper">
            <main class="NavSide__main-content">
                <div class="page-content-header-wrapper">
                    <h1 class="fs-2 fw-bold">Detail Sidang - Sistem Pengajuan Sidang</h1>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h2 class="fs-5 fw-semibold mb-0">Catatan Perbaikan -
                            <?php echo htmlspecialchars($nama_mahasiswa); ?></h2>
                        <span
                            class="badge-custom status-<?php echo strtolower(str_replace(' ', '-', $status_revisi)); ?>">Status
                            Revisi : <?php echo htmlspecialchars($status_revisi); ?></span>
                    </div>
                </div>

                <?php foreach ($catatan_list as $index => $catatan): ?>
                    <div class="card-comment mt-4" data-bs-toggle="modal"
                        data-bs-target="#modalDetail<?php echo $index; ?>">
                        <strong><?php echo htmlspecialchars($catatan['nama_dosen']); ?> - Penguji</strong>
                        <p class="mt-2 mb-0 text-truncate-2">
                            <?php echo htmlspecialchars($catatan['catatan_sidang']); ?>
                            <span class="text-selengkapnya">Selengkapnya...</span>
                        </p>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($catatan_list)): ?>
                    <div class="alert alert-info mt-4">Belum ada catatan perbaikan untuk sidang ini.</div>
                <?php endif; ?>

                <div class="revision-card mt-4">
                    <h5 class="fw-bold" style="color:#4B68FB;">Dokumen Revisi</h5>
                    <form id="revisionForm"
                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id_sidang=' . $id_sidang; ?>"
                        method="POST" enctype="multipart/form-data">
                        <label for="fileInput" class="upload-area-v2 mt-3" id="uploadArea">
                            <div id="initial-state"><i class="bi bi-file-earmark-arrow-up fs-1 text-secondary"></i>
                            </div>
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
                                data-bs-toggle="modal" data-bs-target="#modalKonfirmasi" disabled>Kirim</button>
                        </div>
                    </form>
                </div>

                <?php foreach ($catatan_list as $index => $catatan): ?>
                    <div class="modal fade" id="modalDetail<?php echo $index; ?>" tabindex="-1"
                        aria-labelledby="modalDetailLabel<?php echo $index; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title fs-5" id="modalDetailLabel<?php echo $index; ?>">Detail Catatan
                                        dari <?php echo htmlspecialchars($catatan['nama_dosen']); ?></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <p style="white-space: pre-wrap;">
                                        <?php echo htmlspecialchars($catatan['catatan_sidang']); ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </main>
        </div>
    </div>

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
                        <button type="button" class="btn" id="confirmSubmitBtn"
                            style="background-color: #4FD382; color:white;">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    if (!empty($pesan)):
        $isSuccess = stripos($pesan, 'sukses') !== false;
        $cleanPesan = preg_replace('/^(Sukses|Error): /i', '', $pesan);
        ?>
        <script>
            Swal.fire({
                title: '<?php echo $isSuccess ? "Berhasil" : "Error"; ?>',
                text: '<?php echo addslashes($cleanPesan); ?>',
                icon: '<?php echo $isSuccess ? "success" : "error"; ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            });
        </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            if (menuToggle && sidebar) {
                menuToggle.onclick = function () {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }
            const fileInput = document.getElementById('fileInput');
            const openConfirmModalBtn = document.getElementById('openConfirmModalBtn');
            const initialState = document.getElementById('initial-state');
            const selectedState = document.getElementById('selected-state');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const uploadPromptText = document.getElementById('upload-prompt-text');
            if (fileInput && openConfirmModalBtn) {
                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        if (initialState) initialState.classList.add('d-none');
                        if (selectedState) selectedState.classList.remove('d-none');
                        if (fileNameDisplay) fileNameDisplay.textContent = this.files[0].name;
                        if (uploadPromptText) uploadPromptText.classList.add('d-none');
                        openConfirmModalBtn.disabled = false;
                    } else {
                        if (initialState) initialState.classList.remove('d-none');
                        if (selectedState) selectedState.classList.add('d-none');
                        if (fileNameDisplay) fileNameDisplay.textContent = '';
                        if (uploadPromptText) uploadPromptText.classList.remove('d-none');
                        openConfirmModalBtn.disabled = true;
                    }
                });
            }
            const revisionForm = document.getElementById('revisionForm');
            const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
            if (revisionForm && confirmSubmitBtn) {
                confirmSubmitBtn.addEventListener('click', function () {
                    revisionForm.submit();
                });
            }
        });
    </script>
</body>

</html>