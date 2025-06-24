<?php
// ... (KODE PHP ANDA TIDAK BERUBAH) ...
session_start();
require "../../koneksi/koneksiAndrew.php";
$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
}
if (!isset($_SESSION['selected_sidang_id']) || !is_numeric($_SESSION['selected_sidang_id'])) {
    die("Error: Tidak ada sidang yang dipilih.");
}
$id_sidang = (int) $_SESSION['selected_sidang_id'];
$nama_mahasiswa = '';
$status_revisi = '';
$status_pengajuan = 'Belum Disetujui';
$catatan_list = [];
$query_info = "SELECT TOP 1 ds.status_revisi, m.nama_mhs FROM Detail_Sidang ds JOIN Sidang s ON ds.id_sidang = s.id_sidang JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok JOIN Mahasiswa m ON km.nim = m.nim WHERE ds.id_sidang = ?";
$params_info = array($id_sidang);
$stmt_info = sqlsrv_query($conn, $query_info, $params_info);
if ($stmt_info === false) {
    die(print_r(sqlsrv_errors(), true));
}
$data_info = sqlsrv_fetch_array($stmt_info, SQLSRV_FETCH_ASSOC);
if (!$data_info) {
    die("Error: Data sidang tidak ditemukan.");
}
$nama_mahasiswa = $data_info['nama_mhs'];
$status_revisi = $data_info['status_revisi'];
if (empty(trim($status_revisi))) {
    $status_revisi = 'Belum Ada Revisi';
}
$query_catatan = "SELECT ds.catatan_sidang, d.nama_dosen FROM Detail_Sidang ds JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen WHERE ds.id_sidang = ? ORDER BY d.nama_dosen ASC";
$params_catatan = array($id_sidang);
$stmt_catatan = sqlsrv_query($conn, $query_catatan, $params_catatan);
if ($stmt_catatan === false) {
    die(print_r(sqlsrv_errors(), true));
}
while ($row = sqlsrv_fetch_array($stmt_catatan, SQLSRV_FETCH_ASSOC)) {
    $catatan_list[] = $row;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... Logika Upload File Anda ...
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
        /* CSS Lengkap dan Final (Tidak berubah dari versi terakhir) */
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #ffffff;
        }

        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .NavSide__sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #4b68fb;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.4s ease-in-out;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 25px 30px;
            margin-left: 280px;
            transition: margin-left 0.4s ease-in-out;
            position: relative;
            z-index: 2;
        }

        .NavSide__topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            align-items: center;
            padding: 0 20px;
        }

        .NavSide__toggle {
            font-size: 2rem;
            cursor: pointer;
            color: #4b68fb;
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

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4b68fb;
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: #fff;
            display: none;
        }

        .NavSide__sidebar-item b:nth-child(1)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-bottom-right-radius: 20px;
            background: #4b68fb;
        }

        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            bottom: -20px;
            height: 20px;
            width: 100%;
            background: #fff;
            display: none;
        }

        .NavSide__sidebar-item b:nth-child(2)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-top-right-radius: 20px;
            background: #4b68fb;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .page-content-header-wrapper h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
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
            color: #5d4a1a !important;
        }

        .status-disetujui {
            background-color: #A3E4D7;
            color: #0E6655 !important;
        }

        .status-belum-ada-revisi {
            background-color: #6c757d;
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

        .btn-tolak {
            background-color: #fd7d7d;
            border-radius: 50px;
            height: 40px;
            width: 120px;
        }
        .btn-tolak:hover {
            background-color: #fd7d7d;
            transform: translateY(-2px);
            color: white;
        }

        .btn-kirim {
            background-color: #4FD382;
            border-radius: 50px;
            height: 40px;
            width: 120px;
        }

        .btn-kirim:hover {
            background-color: #4FD382;
            transform: translateY(-2px);
            color: white;
        }

        @media (max-width: 992px) {
            .NavSide__sidebar {
                transform: translateX(-280px);
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
            }

            .NavSide__main-content {
                margin-left: 0;
                padding-top: 80px;
            }

            .NavSide__topbar {
                display: flex;
                z-index: 999;
            }

            .NavSide__toggle {
                position: relative;
                z-index: 1001;
                transition: transform 0.4s ease-in-out;
            }

            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px);
            }

            .NavSide__toggle .close {
                display: none;
            }

            .NavSide__toggle.NavSide__toggle--active .open {
                display: none;
            }

            .NavSide__toggle.NavSide__toggle--active .close {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .page-content-header-wrapper {
                flex-direction: column;
                align-items: stretch !important;
            }
        }
        
    </style>
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
                                    class="text-selengkapnya">Selengkapnya...</span></p>
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
                            data-bs-toggle="modal" data-bs-target="#modalKonfirmasi" disabled>Kirim</button>
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
                            <?php echo htmlspecialchars($catatan['nama_dosen']); ?></h4>
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

    <script>
        // SCRIPT JS FINAL (Tidak perlu diubah)
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");

            if (menuToggle && sidebar) {
                menuToggle.onclick = () => {
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
                        initialState.classList.add('d-none');
                        selectedState.classList.remove('d-none');
                        fileNameDisplay.textContent = this.files[0].name;
                        uploadPromptText.classList.add('d-none');
                        openConfirmModalBtn.disabled = false;
                    } else {
                        initialState.classList.remove('d-none');
                        selectedState.classList.add('d-none');
                        fileNameDisplay.textContent = '';
                        uploadPromptText.classList.remove('d-none');
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