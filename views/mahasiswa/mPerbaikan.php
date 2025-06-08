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
                $pesan = "Error: Format file tidak diizinkan (berdasarkan ekstensi). Hanya .pdf, .docx, .pptx, dan .zip yang boleh diunggah.";
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

                if (!$valid_mime) {
                    $pesan = "Error: Tipe file tidak valid atau tidak cocok dengan ekstensinya. Deteksi MIME: " . htmlspecialchars($tipe_mime_file_asli);
                }
            }
            
            if (empty($pesan) && $_FILES["fileInput"]["size"] > 5242880) { 
                $pesan = "Error: Ukuran file terlalu besar. Maksimal 5 MB.";
            }
            
            if (empty($pesan)) { 
                if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                    $_SESSION['pesan'] = "Sukses: File ". htmlspecialchars($file_asli) . " berhasil diunggah.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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

    body {
        margin: 0;
        background-color: #FFFFFF;
    }

    #NavSide {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    .NavSide__sidebar-brand {
        padding: 10% 5% 50% 5%;
        text-align: center;
    }

    .NavSide__sidebar-brand img {
        width: 90%;
        max-width: 180px;
        height: auto;
        display: inline-block;
    }

    .NavSide__sidebar {
        position: fixed;
        top: 0px;
        left: 0px;
        bottom: 0px;
        width: 280px;
        border-radius: 1px;
        box-sizing: border-box;
        border-left: 5px solid #4B68FB;
        background: #4B68FB;
        overflow-x: hidden;
        overflow-y: auto;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        transition: transform 0.5s ease-in-out, width 0.5s ease-in-out;
    }

    .NavSide__sidebar-nav {
        width: 100%;
        padding-left: 0;
        padding-top: 0;
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
    }

    .NavSide__sidebar-item a {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        text-decoration: none;
        color: rgb(252, 252, 252);
        padding: 5% 2%;
        height: 60px;
        box-sizing: border-box;
    }

    .NavSide__sidebar-title {
        white-space: normal;
        text-align: center;
        line-height: 1.5;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active {
        background: #FFFFFF;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active a {
        color: #4B68FB;
    }

    .NavSide__sidebar-item b:nth-child(1) {
        position: absolute;
        top: -20px;
        height: 20px;
        width: 100%;
        background: #FFFFFF;
        display: none;
        right: 0;
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
        display: block;
    }

    .NavSide__sidebar-item b:nth-child(2) {
        position: absolute;
        bottom: -20px;
        height: 20px;
        width: 100%;
        background: #FFFFFF;
        display: none;
        right: 0;
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
        display: block;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
    .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
        display: block;
    }

    .NavSide__topbar {
        display: flex;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        margin-left: 280px;
        height: 60px;
        background-color: #FFFFFF;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 999;
        padding: 0 15px;
        justify-content: flex-start;
        transition: margin-left 0.5s ease-in-out;
    }

    .NavSide__topbar .NavSide__toggle {
        width: 40px;
        height: 40px;
        cursor: pointer;
        border-radius: 5px;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .NavSide__topbar .NavSide__toggle i.bi {
        position: absolute;
        font-size: 24px;
        display: none;
        color: #4B68FB;
        transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
    }

    .NavSide__topbar .NavSide__toggle.NavSide__toggle--active i.bi.open {
        display: none;
    }

    .NavSide__topbar .NavSide__toggle.NavSide__toggle--active i.bi.close {
        display: block;
    }

    .NavSide__topbar .NavSide__toggle i.bi.open {
        display: block;
    }

    .NavSide__main-content {
        flex-grow: 1;
        padding: 2rem;
        margin-left: 280px;
        overflow-y: auto;
        transition: margin-left 0.5s ease-in-out;
        background-color: #FFFFFF;
        padding-top: calc(60px + 2rem);
    }

    .page-content-header-wrapper {
        margin-bottom: 2.5rem;
    }

    .main-page-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.75rem;
    }

    .page-content-header-wrapper .sub-header-line .section-subtitle {
        font-weight: 600;
        font-size: 1.75rem;
        color: #212529;
        margin-bottom: 0;
    }

    .text-primary {
        color: #4B68FB !important;
    }

    #fileNameDisplay {
        margin-top: 1rem;
        margin-bottom: 1rem;
        font-weight: 600;
        min-height: 1.5rem;
        color: #495057;
    }

    .badge-custom.status-belum-disetujui {
        background-color: #FFA3A3;
        color: white;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 20px;
    }

    .badge-custom.status-setuju {
        background-color: #4BFBAF;
        color: #1E4620;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 20px;
    }

    .card-comment {
        background-color: #cbcbcb;
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
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
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

    .upload-area-v2 #initial-state svg {
        width: 80px;
        height: 80px;
        fill: #ced4da;
    }

    .upload-area-v2 #selected-state svg {
        width: 80px;
        height: 80px;
        fill: #8d99ae;
    }

    .upload-area-v2 #upload-prompt-text {
        text-align: center;
        color: #6c757d;
        font-size: small;
        margin-top: 1rem;
    }

    .btn-custom-primary {
        background-color: #4B68FB;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
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

    .btn-custom-primary svg {
        margin-right: 0.5rem;
    }

    .custom-swal-popup {
        background-color: #F8F9FA !important;
        border-radius: 18px !important;
        padding: 30px 35px !important;
        width: auto !important;
        max-width: 480px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    .custom-swal-html-container {
        margin: 0 !important;
        padding: 0 !important;
    }

    .custom-swal-title {
        font-size: 1.8rem !important;
        font-weight: 600 !important;
        color: #343a40 !important;
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
    }

    .custom-swal-confirm-button:hover {
        background-color: #3FA970 !important;
    }

    .custom-swal-cancel-button {
        background-color: #FD7D7D !important;
        color: white !important;
    }

    .custom-swal-cancel-button:hover {
        background-color: #FA6868 !important;
    }

    .custom-swal-popup .swal2-icon {
        display: none !important;
    }

    .custom-swal-popup > .swal2-title,
    .custom-swal-popup > .swal2-content {
        display: none !important;
    }

    #modalDetail .modal-content {
        background-color: #FFFFFF !important;
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0px 8px 24px rgba(29, 36, 50, 0.15) !important;
        padding: 5px;
    }

    #modalDetail .modal-header {
        border-bottom: none !important;
        padding: 20px 25px 10px 25px;
        position: relative;
    }

    #modalDetail #modalDetailLabel {
        font-size: 1.6rem;
        font-weight: 600;
        color: #3A3A58;
        width: calc(100% - 40px);
        text-align: left;
    }

    #modalDetail .modal-header .btn-close {
        background-color: #e9ecef;
        border-radius: 50%;
        padding: 0.4em;
        opacity: 0.7;
        box-shadow: none !important;
        font-size: 0.8rem;
    }

    #modalDetail .modal-header .btn-close:hover {
        opacity: 1;
    }

    #modalDetail .modal-body {
        padding: 5px 25px 20px 25px;
        font-size: 0.9rem;
        color: #525278;
        line-height: 1.6;
    }

    #modalDetail .modal-footer {
        border-top: none !important;
        padding: 10px 25px 20px 25px;
        justify-content: flex-end !important;
    }

    #modalDetail .modal-footer .btn-custom-tutup-modal {
        background-color: #4B68FB !important;
        color: white !important;
        border: none !important;
        border-radius: 50px !important;
        padding: 8px 22px !important;
        font-size: 0.85rem;
        font-weight: 500;
    }

    #modalDetail .modal-footer .btn-custom-tutup-modal:hover {
        background-color: #3A58DB !important;
    }

    @media (max-width: 700px) {
        .NavSide__sidebar {
            width: 250px;
            transform: translateX(-100%);
            border-left-width: 0;
            z-index: 1040;
            padding-top: 60px;
        }

        .NavSide__sidebar.NavSide__sidebar--active-mobile {
            transform: translateX(0);
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
        }

        .NavSide__sidebar-brand {
            padding: 20px 10px 15px 10px;
        }

        .NavSide__sidebar-brand img {
            max-width: 140px;
        }

        .NavSide__sidebar-nav {
            padding-top: 10px;
        }

        .NavSide__sidebar-item a {
            padding: 15px 20px;
            height: auto;
        }

        .NavSide__topbar {
            display: flex;
            margin-left: 0;
            z-index: 1045;
        }

        .NavSide__topbar .NavSide__toggle {
            display: flex;
            position: relative;
            top: auto;
            left: auto;
            background-color: transparent;
            box-shadow: none;
        }

        .NavSide__topbar .NavSide__toggle i.bi.open {
            display: block;
        }

        .NavSide__main-content {
            margin-left: 0;
            padding: 1rem;
            padding-top: calc(60px + 1rem);
            width: 100%;
        }

        .main-page-title {
            font-size: 1.75rem;
        }

        .page-content-header-wrapper .sub-header-line .section-subtitle {
            font-size: 1.25rem;
        }

        .upload-area-v2 {
            padding: 1.5rem;
        }

        .upload-area-v2 #initial-state svg,
        .upload-area-v2 #selected-state svg {
            width: 60px;
            height: 60px;
        }

        .btn-custom-primary {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }

        .custom-swal-popup {
            padding: 20px 25px !important;
            max-width: calc(100% - 30px);
        }

        .custom-swal-title {
            font-size: 1.4rem !important;
        }

        .custom-swal-text {
            font-size: 0.9rem !important;
            margin-bottom: 1.25rem !important;
        }

        .custom-swal-actions {
            gap: 8px !important;
        }

        .swal2-styled.swal2-confirm,
        .swal2-styled.swal2-cancel {
            padding: 8px 20px !important;
            border-radius: 8px !important;
        }

        #modalDetail .modal-content {
            padding: 10px;
        }

        #modalDetail .modal-header {
            padding: 15px 20px 5px 20px;
        }

        #modalDetail #modalDetailLabel {
            font-size: 1.3rem;
        }

        #modalDetail .modal-header .btn-close {
            padding: 0.35em;
            font-size: 0.7rem;
        }

        #modalDetail .modal-body {
            padding: 5px 20px 15px 20px;
            font-size: 0.85rem;
        }

        #modalDetail .modal-footer {
            padding: 10px 20px 15px 20px;
        }

        #modalDetail .modal-footer .btn-custom-tutup-modal {
            padding: 7px 20px !important;
            font-size: 0.8rem;
        }
    }
    </style>
</head>
<body>

<div id="main-sidebar" class="NavSide__sidebar">
    <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="ASTRAtech Logo"> 
    </div>
    <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item"> 
            <b></b><b></b>
            <a href="mdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active"> <b></b><b></b>
            <a href="#"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a>
        </li>
        <li class="NavSide__sidebar-item">
            <b></b><b></b>
            <a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
    </ul>
</div>

    <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"> 
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            </div>

        <main class="NavSide__main-content">
            <div class="page-content-header-wrapper">
                <h1 class="main-page-title">Detail Sidang - Sistem Pengajuan Sidang</h1>
                <div class="d-flex justify-content-between align-items-center sub-header-line mt-3">
                    <h2 class="section-subtitle mb-0">Catatan Perbaikan</h2>
                    <span class="badge-custom status-belum-disetujui">Status Revisi : Belum Disetujui</span>
                    </div>
            </div>
            
            <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail">
                <strong>Timotius Victory, S.Kom, M.Kom - Penguji</strong>
                <p class="mt-2 mb-0 text-truncate-2">
                    Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
                </p>
            </div>

            <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail">
                <strong>Yosep Setiawan, S.Kom, M.Kom - Penguji</strong>
                <p class="mt-2 mb-0 text-truncate-2">
                    Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
                </p>
            </div>

            <div class="revision-card">
                <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
                <form id="revisionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <label for="fileInput" class="upload-area-v2" id="uploadArea">
                        <div id="initial-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#ced4da" class="bi bi-file-earmark-arrow-up" viewBox="0 0 16 16"><path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707V11.5z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 1.5v2A1.5 1.5 0 0 0 11 5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v1.5z"/></svg>
                        </div>
                        <div id="selected-state" class="d-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8d99ae" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.707 0H9.293zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 10.5a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 12a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>
                        </div>
                        <p id="upload-prompt-text">Unggah berkas revisi dengan format pdf, docx, pptx, dan zip</p>
                    </label>
                    <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
                    <div class="text-center mt-3"><p id="fileNameDisplay" class="fw-bold mb-0"></p></div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-custom-primary" id="submitBtn" disabled>Kirim</button>
                    </div>
                </form>
            </div>

            <div class="mt-4">
                <button type="button" id="btnKembali" class="btn btn-custom-primary" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
                    Kembali
                </button>
            </div>

            <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content"> 
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalDetailLabel">Detail Catatan Perbaikan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik,
                                termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali penggunaan bahasa.
                                Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa ilmiah yang baku dan konsisten.
                                Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik,
                                termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali penggunaan bahasa.
                                Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa ilmiah yang baku dan konsisten.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-custom-tutup-modal" data-bs-dismiss="modal">Tutup</button>
                        </div>
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
    $confirmButtonColor = $isSuccess ? '#4FD382' : '#FD7D7D'; 
?>
<script>
    Swal.fire({
        title: '<?php echo $title; ?>',
        text: '<?php echo addslashes(preg_replace("/\r|\n/", "\\n", $pesan)); ?>',
        icon: '<?php echo $icon; ?>',
        confirmButtonColor: '<?php echo $confirmButtonColor; ?>' 
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const submitBtn = document.getElementById('submitBtn');
    const revisionForm = document.getElementById('revisionForm');
    const btnKembali = document.getElementById('btnKembali');
    
    const initialState = document.getElementById('initial-state');
    const selectedState = document.getElementById('selected-state');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const uploadPromptText = document.getElementById('upload-prompt-text'); 
    
    if (fileInput && submitBtn && revisionForm && initialState && selectedState && fileNameDisplay && uploadPromptText) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                initialState.classList.add('d-none');
                selectedState.classList.remove('d-none');
                fileNameDisplay.textContent = this.files[0].name;
                uploadPromptText.classList.add('d-none'); 
                submitBtn.disabled = false;
            } else {
                initialState.classList.remove('d-none');
                selectedState.classList.add('d-none');
                fileNameDisplay.textContent = '';
                uploadPromptText.classList.remove('d-none'); 
                submitBtn.disabled = true;
            }
        });

        revisionForm.addEventListener('submit', function(event) {
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
    } else {
        console.warn("Peringatan: Salah satu elemen HTML untuk fungsionalitas upload tidak ditemukan.");
    }

    if (btnKembali) {
        btnKembali.addEventListener('click', function() {
            window.location.href = 'mSidang.php'; // MODIFIKASI DI SINI
        });
    }

    const sidebarToggleBtn = document.querySelector('.NavSide__topbar .NavSide__toggle'); 
    const mainSidebar = document.getElementById('main-sidebar'); 

    if (sidebarToggleBtn && mainSidebar) {
        sidebarToggleBtn.addEventListener('click', function() {
            mainSidebar.classList.toggle('NavSide__sidebar--active-mobile');
            this.classList.toggle('NavSide__toggle--active'); 
        });
    } else {
        console.warn("Peringatan: Elemen sidebar atau tombol toggle tidak ditemukan untuk fungsionalitas mobile.");
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth > 700) { 
            if (mainSidebar && mainSidebar.classList.contains('NavSide__sidebar--active-mobile')) {
                mainSidebar.classList.remove('NavSide__sidebar--active-mobile');
            }
            if (sidebarToggleBtn && sidebarToggleBtn.classList.contains('NavSide__toggle--active')) {
                sidebarToggleBtn.classList.remove('NavSide__toggle--active');
            }
        }
    });
});
</script>
</body>
</html>