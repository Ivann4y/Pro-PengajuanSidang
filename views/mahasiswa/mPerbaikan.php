<?php
session_start();

$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
}

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
            $file_unik = uniqid('revisi_', true) . '.' . $ekstensi_file;
            $path_target = $folder_target . $file_unik;

            $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");
            $mime_diizinkan = array(
                "pdf" => "application/pdf",
                "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
                "zip" => "application/zip"
            );
            $mime_alternatif_zip = "application/x-zip-compressed";

            if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
                $pesan = "Error: Format file tidak diizinkan. Hanya .pdf, .docx, .pptx, dan .zip yang boleh diunggah.";
            } else {
                $tipe_mime_file_asli = "";
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $tipe_mime_file_asli = finfo_file($finfo, $_FILES["fileInput"]["tmp_name"]);
                    finfo_close($finfo);
                }

                $valid_mime = false;
                if (!empty($tipe_mime_file_asli) && isset($mime_diizinkan[$ekstensi_file])) {
                    if ($mime_diizinkan[$ekstensi_file] === $tipe_mime_file_asli) {
                        $valid_mime = true;
                    } elseif ($ekstensi_file === "zip" && $tipe_mime_file_asli === $mime_alternatif_zip) {
                        $valid_mime = true;
                    }
                }

                if (!$valid_mime && !empty($tipe_mime_file_asli)) {
                    $pesan = "Error: Tipe file tidak valid atau tidak cocok dengan ekstensinya.";
                } else if (empty($tipe_mime_file_asli)) {
                    $pesan = "Error: Tidak dapat mendeteksi tipe MIME file. Pastikan ekstensi fileinfo diaktifkan di server Anda.";
                }
            }

            if (empty($pesan) && $_FILES["fileInput"]["size"] > 5242880) { // 5 MB
                $pesan = "Error: Ukuran file terlalu besar. Maksimal 5 MB.";
            }

            if (empty($pesan)) {
                if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                    $_SESSION['pesan'] = "Sukses: File " . htmlspecialchars($file_asli) . " berhasil diunggah.";
                    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
                    exit();
                } else {
                    $pesan = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
                }
            }
        }
    } elseif (isset($_FILES["fileInput"])) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #FFFFFF;
        }

        #NavSide {
            display: flex;
            min-height: 100vh;
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

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4B68FB;
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
            background: #4B68FB;
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
            background: #4B68FB;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
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

        .NavSide__toggle.NavSide__toggle--active .open {
            display: none;
        }

        .NavSide__toggle.NavSide__toggle--active .close {
            display: block;
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
        }

        @media (max-width: 992px) {
            .NavSide__sidebar {
                transform: translateX(-280px);
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
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

            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px);
            }

            .NavSide__main-content {
                padding-top: 60px;
            }
        }

        .badge-custom.status-belum-disetujui {
            background-color: #FFA3A3;
            color: white;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 20px;
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

        .card-comment:hover strong {
            color: #fff;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
            background-color: #4B68FB;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 15px rgba(75, 104, 251, 0.3);
            transition: all 0.2s ease-in-out;
        }

        .btn-custom-primary:hover {
            background-color: #3A58DB;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(75, 104, 251, 0.4);
        }

        .btn-custom-primary:disabled {
            background-color: #e2e8f0;
            color: #94a3b8;
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        .btn-kembali {
            background-color: #4B68FB;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            display: inline-flex;
            align-items: center;
            margin-top: 1.2cm;
        }

        .btn-kembali:hover {
            position: relative;
            background-color: white;
            color: #4B68FB;
        }

        .btn-kembali .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-kembali:hover .icon-circle {
            background-color: #4B68FB;
        }

        .btn-kembali .icon-circle i {
            color: #4B68FB;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .btn-kembali:hover .icon-circle i {
            color: white;
        }

        .custom-swal-popup {
            background-color: #F8F9FA !important;
            border-radius: 18px !important;
            padding: 30px 35px !important;
            width: auto !important;
            max-width: 480px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .custom-swal-html-container {
            margin: 0 !important;
            padding: 0 !important;
        }

        .custom-swal-title {
            font-size: 1.8rem !important;
            font-weight: 600 !important;
            color: rgb(177, 180, 183) !important;
            margin-top: 0 !important;
            margin-bottom: 12px !important;
            padding: 0 !important;
            text-align: center;
        }

        .custom-swal-text {
            font-size: 1.05rem !important;
            color: #495057 !important;
            margin-bottom: 25px !important;
            padding: 0 !important;
            line-height: 1.65;
            text-align: center;
        }

        .custom-swal-actions {
            margin-top: 20px !important;
            gap: 15px !important;
            display: flex !important;
            justify-content: center !important;
        }

        .swal2-styled.swal2-confirm,
        .swal2-styled.swal2-cancel {
            font-size: 0.95rem !important;
            padding: 12px 28px !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease-in-out;
            flex-grow: 0;
            min-width: 130px;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            text-transform: capitalize;
        }

        .swal2-styled.swal2-confirm:hover,
        .swal2-styled.swal2-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .custom-swal-confirm-button {
            background-color: #4FD382 !important;
            color: white !important;
            border-radius: 35px;
            width: 95px;
            height: 30px;
        }

        .custom-swal-confirm-button:hover {
            background-color: #3FA970 !important;
        }

        .custom-swal-cancel-button {
            background-color: #FD7D7D !important;
            color: white !important;
            border-radius: 35px;
            width: 95px;
            height: 30px;
        }

        .custom-swal-cancel-button:hover {
            background-color: #FA6868 !important;
        }

        .custom-swal-popup .swal2-icon {
            display: none !important;
        }
    </style>
</head>

<body>

    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="ASTRAtech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mdetailSidang.php"><span
                            class="fw-semibold">Detail Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span
                            class="fw-semibold">Perbaikan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mNilaiakhir.php"><span
                            class="fw-semibold">Nilai Akhir</span></a></li>
            </ul>
        </div>

        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>

        <div id="page-content-wrapper">
            <main class="NavSide__main-content">
                <div class="page-content-header-wrapper">
                    <h1 class="fs-3 fw-bold">Detail Sidang - Sistem Pengajuan Sidang</h1>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h2 class="fs-5 fw-semibold mb-0">Catatan Perbaikan</h2><span
                            class="badge-custom status-belum-disetujui">Status Revisi : Belum Disetujui</span>
                    </div>
                </div>
                <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail"><strong>Timotius
                        Victory, S.Kom, M.Kom - Penguji</strong>
                    <p class="mt-2 mb-0 text-truncate-2">Pastikan seluruh bagian dokumen mengikuti format penulisan...
                    </p>
                </div>
                <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail"><strong>Yosep Setiawan,
                        S.Kom, M.Kom - Penguji</strong>
                    <p class="mt-2 mb-0 text-truncate-2">Pastikan seluruh bagian dokumen mengikuti format penulisan...
                    </p>
                </div>
                <div class="revision-card">
                    <h5 class="fw-bold" style="color:#4B68FB;">Dokumen Revisi</h5>
                    <form id="revisionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                        enctype="multipart/form-data"><label for="fileInput" class="upload-area-v2 mt-3"
                            id="uploadArea">
                            <div id="initial-state"><i class="bi bi-file-earmark-arrow-up fs-1 text-secondary"></i>
                            </div>
                            <div id="selected-state" class="d-none"><i
                                    class="bi bi-file-earmark-text-fill fs-1 text-primary"></i></div>
                            <p id="upload-prompt-text" class="text-muted mt-2">Unggah berkas revisi (.pdf, .docx, .pptx,
                                .zip)</p>
                        </label><input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip"
                            hidden />
                        <div class="text-center mt-2">
                            <p id="fileNameDisplay" class="fw-bold mb-0"></p>
                        </div>
                        <div class="d-flex justify-content-end mt-4"><button type="submit"
                                class="btn btn-custom-primary" id="submitBtn" disabled>Kirim</button></div>
                    </form>
                </div>
                <div class="mt-4"><button class="btn-kembali" onclick="location.href='mSidang.php'"><span
                            class="icon-circle"><i class="fa-solid fa-arrow-left"></i></span> Kembali</button></div>
                <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title fs-5" id="modalDetailLabel">Detail Catatan Perbaikan</h4><button
                                    type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <p>Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh
                                panduan akademik,
                                termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali
                                penggunaan bahasa.
                                Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa
                                ilmiah yang baku dan konsisten.
                                Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh
                                panduan akademik,
                                termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali
                                penggunaan bahasa.
                                Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa
                                ilmiah yang baku dan konsisten.
</p>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Tutup</button></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    if (!empty($pesan)):
        $isSuccess = strpos(strtolower($pesan), 'sukses') !== false;
        $icon = $isSuccess ? 'success' : 'error';
        $title = $isSuccess ? 'Berhasil!' : 'Oops...';
        ?>
        <script>
            Swal.fire({
                title: '<?php echo $title; ?>',
                text: '<?php echo addslashes(preg_replace("/\r|\n/", "\\n", $pesan)); ?>',
                icon: '<?php echo $icon; ?>',
                confirmButtonColor: '<?php echo $isSuccess ? "#4FD382" : "#FD7D7D"; ?>'
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

            const listItems = document.querySelectorAll(".NavSide__sidebar-item");
            listItems.forEach(item => {
                item.addEventListener('click', function (event) {
                    const link = this.querySelector('a');
                    if (link && !link.hasAttribute('data-bs-toggle')) {
                        listItems.forEach(li => li.classList.remove("NavSide__sidebar-item--active"));
                        this.classList.add("NavSide__sidebar-item--active");
                        if (link.getAttribute('href') && link.getAttribute('href') !== '#') {
                            window.location.href = link.href;
                        }
                    }
                });
            });

            const fileInput = document.getElementById('fileInput');
            const submitBtn = document.getElementById('submitBtn');
            const revisionForm = document.getElementById('revisionForm');
            const initialState = document.getElementById('initial-state');
            const selectedState = document.getElementById('selected-state');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const uploadPromptText = document.getElementById('upload-prompt-text');

            if (fileInput && submitBtn) {
                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        if (initialState) initialState.classList.add('d-none');
                        if (selectedState) selectedState.classList.remove('d-none');
                        if (fileNameDisplay) fileNameDisplay.textContent = this.files[0].name;
                        if (uploadPromptText) uploadPromptText.classList.add('d-none');
                        submitBtn.disabled = false;
                    } else {
                        if (initialState) initialState.classList.remove('d-none');
                        if (selectedState) selectedState.classList.add('d-none');
                        if (fileNameDisplay) fileNameDisplay.textContent = '';
                        if (uploadPromptText) uploadPromptText.classList.remove('d-none');
                        submitBtn.disabled = true;
                    }
                });
            }

            // Logika form submit dengan modal kustom
            if (revisionForm) {
                revisionForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    Swal.fire({
                        html: `
                    <div class="custom-swal-title">Perhatian</div>
                    <div class="custom-swal-text">Apakah anda sudah yakin ingin mengupload dokumen?</div>
                `,
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batalkan',
                        showConfirmButton: true,
                        showCloseButton: false,
                        buttonsStyling: false,
                        customClass: {
                            popup: 'custom-swal-popup',
                            htmlContainer: 'custom-swal-html-container',
                            actions: 'custom-swal-actions',
                            confirmButton: 'custom-swal-confirm-button',
                            cancelButton: 'custom-swal-cancel-button'
                        },
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            revisionForm.submit();
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>