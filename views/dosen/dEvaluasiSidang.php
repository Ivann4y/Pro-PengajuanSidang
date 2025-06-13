<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"> <!-- Pengaturan karakter untuk dukungan universal -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Konfigurasi responsivitas di berbagai perangkat -->
    <title>Evaluasi Sidang</title> <!-- Judul halaman di tab browser -->

    <!-- CSS Frameworks dan Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> <!-- Ikon Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> <!-- Font Poppins -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../../css/button-styles.css"> <!-- Custom CSS untuk gaya tombol -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 untuk notifikasi pop-up -->

    <style>
        /* --- General and Body Styles --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #ffffff;
        }

        /* --- Base Sidebar, Main Content, and Info Card Styles --- */
        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
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

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
            filter: brightness(0) invert(1);
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
            margin-bottom: 15px;
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
            color: white;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4B68FB !important;
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
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
            display: block;
        }

        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            bottom: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
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
            display: block;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm);
            margin-left: 280px; /* Margin kiri konten utama untuk desktop */
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            padding-top: calc(20px); /* Padding atas untuk desktop */
        }

        .NavSide__main-content h2 {
            margin-bottom: 0.9cm;
            font-weight: 700;
        }

        .NavSide__main-content h3 {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 0.2cm;
        }

        .status-badge {
            background-color: #FFA3A3;
            color: black;
            border-radius: 20px;
            padding: 8px 18px;
            display: inline-block;
            font-size: 0.875rem;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.08);
            font-weight: bold;
            margin-bottom: 1.2cm;
        }

        .status-badge.approved {
            background-color: #4BFBAF;
        }

        .info-card {
            position: relative;
            background: rgb(235, 238, 245);
            border-radius: 30px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            overflow: hidden;
            transition: background-color 0.4s ease;
            margin-bottom: 1.2cm;
            margin-right: 40px; 
        }

        .info-card::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 100%;
            background-color: #4B68FB;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            transition: width 0.4s ease;
            z-index: 0;
        }

        .info-card:hover::after {
            width: 100%;
            border-radius: 20px;
        }

        .info-card .section {
            flex: 0 0 48%;
            z-index: 1;
            color: #333;
            transition: color 0.4s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .info-card:hover .section {
            color: white;
        }

        .info-card .section .info-group {
            margin-bottom: 1rem;
        }

        .info-card .section .info-group:last-child {
            margin-bottom: 0;
        }

        .info-card .section .label-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .info-card .section .label-row i {
            margin-right: 10px;
            color: #495057;
            font-weight: 900;
            transition: color 0.4s ease;
            width: 20px;
            text-align: center;
        }

        .info-card:hover .section .label-row i {
            color: white;
        }

        .info-card .section .label-row .fw-bold {
            font-weight: 600;
            font-size: 1.05rem;
        }

        .info-card .section .value-row {
            margin-left: 30px;
            line-height: 1.5;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .btn-kembali {
            background-color: #4B68FB;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 0 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
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
        }

        .btn-kembali:hover .icon-circle i {
            color: white;
        }

        .form-card {
            background:rgb(235, 238, 245);
            border-radius: 30px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 25px;
            margin-bottom: 1.2cm;
            margin-right: 40px;
        }

        .form-card h4 {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.8cm;
        }

        .penilaian-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: center;
            margin-top: 0.5cm;
            margin-bottom: 1rem;
            margin-left: 0 !important;
            margin-right: 0 !important;
            gap: 20px;
        }

        .penilaian-row .col-3 {
            padding: 0 !important;
            flex: 0 0 auto;
            width: calc(25% - (20px * 3 / 4));
            box-sizing: border-box;
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .penilaian-row label {
            flex-shrink: 0;
            white-space: nowrap;
            margin-right: 10px;
            font-weight: 550;
            margin-top: 0;
            color: #333;
            min-width: unset;
            text-align: left;
        }

        .penilaian-row .colon {
            flex-shrink: 0;
            margin-right: 10px;
            color: #333;
        }

        input.form-control-custom.input-nilai {
            width: 75px;
            font-size: 1rem;
            height: 40px;
            padding: 5px 10px;
            text-align: center;
            flex-grow: 0;
            min-width: unset;
            background-color: #ffffff !important;
            border: 1px solid #F2F2F2;
            border-radius: 10px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-card .form-control-custom.input-nilai:focus {
            border-color: #4B68FB;
            box-shadow: 0 0 0 0.25rem rgba(75, 104, 251, 0.25);
            outline: none;
        }

        .form-group-custom {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .form-group-custom label {
            flex: 0 0 180px;
            margin-right: 20px;
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }

        .form-group-custom .form-control-custom {
            flex: 1;
            min-width: 200px;
            background-color: white !important;
            border: 1px solid #F2F2F2;
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 0.95rem;
            height: 45px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group-custom .form-control-custom:focus {
            border-color: #4B68FB;
            box-shadow: 0 0 0 0.25rem rgba(75, 104, 251, 0.25);
            outline: none;
        }

        .form-group-custom textarea.form-control-custom {
            min-height: 200px;
            resize: vertical;
        }

        .button-group-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.2cm;
            margin-right: 40px;
        }

        .btn-kirim {
            background-color: #4FD382;
            color: #FFFFFF;
            border: none;
            border-radius: 20px;
            padding: 0 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
        }

        .btn-kirim:hover {
            background-color: #3AB070;
            color: white;
        }

        .success-modal-content {
            background-color: rgb(235, 238, 245);
            border-radius: 30px;
            border: none;
            padding: 20px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
        }

        .success-modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 30px 20px;
        }

        .success-icon {
            width: 6rem;
            height: 6rem;
            margin-bottom: 20px;
        }

        .success-message {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            font-weight: 500;
            display: none;
            margin-top: 10px;
            margin-left: 0;
            text-align: left;
        }

        .modal-content.custom-modal-content {
            border-radius: 30px !important;
            background-color: #f8f9fa;
            border: none;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
        }

        .modal-header.custom-modal-header {
            border-bottom: none;
            justify-content: center;
            padding: 0;
        }

        .modal-body.custom-modal-body {
            text-align: center;
        }

        /* --- START PERBAIKAN: Gaya Tombol Modal Konsisten --- */
        .btn-tolak,
        .btn-setujui {
            border-radius: 20px; /* Radius yang sama dengan tombol Kembali/Kirim utama */
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            height: 45px; /* Tinggi yang sama dengan tombol Kembali/Kirim utama */
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 25px; /* Padding yang sama dengan tombol Kembali/Kirim utama */
        }
        /* --- END PERBAIKAN --- */

        .btn-tolak {
            background-color: #FD7D7D;
            color: white;
        }
        .btn-tolak:hover {
            background-color: #F85C5C;
            color: white;
        }

        .btn-setujui {
            background-color: #4FD382;
            color: white;
        }
        .btn-setujui:hover {
            background-color: #3AB070;
            color: white;
        }

        #catatanEvaluasi::placeholder {
            color: #888 !important;
            opacity: 60% !important;
        }

        #btnKonfirmasiKirim:hover {
            background-color: #157347 !important;
            color: #fff !important;
        }

        .desktop-toggle-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            cursor: default;
            pointer-events: none;
        }
        .desktop-toggle-icon i.bi {
            font-size: 28px;
            color: #4B68FB;
        }

        /* Media query untuk perangkat dengan lebar layar <= 768px (tablet & mobile) */
        @media (max-width: 768px) {
            .penilaian-row {
                margin-left: 0 !important;
                margin-right: 0 !important;
                gap: 15px 0;
            }
            .penilaian-row .col-3 {
                width: 100%;
                justify-content: flex-start;
                padding: 0 !important;
                margin-bottom: 0;
            }
            .penilaian-row label {
                min-width: unset;
                text-align: left;
                margin-right: 10px;
            }
            .form-card .form-control-custom.input-nilai {
                flex-grow: 1;
                width: auto;
            }

            .form-group-custom {
                flex-direction: column;
                align-items: flex-start;
            }
            .form-group-custom label {
                flex: none;
                width: 100%;
                margin-bottom: 0.5rem;
                margin-right: 0;
            }
            .form-group-custom .form-control-custom {
                width: 100%;
                min-width: unset;
            }

            .modal-dialog {
                margin: 1rem auto;
                max-width: 95% !important;
            }
            .modal-content.custom-modal-content {
                padding: 15px !important;
            }
            .modal-body.custom-modal-body {
                padding: 0 !important;
                text-align: center;
            }
            
            .modal-body.custom-modal-body > .d-flex.justify-content-between.px-5 {
                padding-left: 20px !important;
                padding-right: 20px !important;
                gap: 15px;
                width: 100%;
                flex-wrap: nowrap;
            }

            /* --- START PERBAIKAN: Gaya Tombol Modal Mobile Konsisten --- */
            .modal-body.custom-modal-body .btn-tolak,
            .modal-body.custom-modal-body .btn-setujui {
                flex-grow: 1;
                flex-shrink: 0;
                flex-basis: auto;
                width: auto; 
                height: 40px; /* Sesuai dengan tombol Kembali/Kirim di mobile */
                font-size: 0.9rem;
                padding: 0 10px !important; /* Sesuai dengan tombol Kembali/Kirim di mobile */
                border-radius: 18px; /* Sesuai dengan tombol Kembali/Kirim di mobile */
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }
            /* --- END PERBAIKAN --- */
        }

        /* Media query untuk perangkat dengan lebar layar <= 700px (mobile) */
        @media (max-width: 700px) {
            .NavSide__topbar {
                display: flex;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 60px;
                background-color: #ffffff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                z-index: 999;
                align-items: center;
                padding: 0 15px;
                justify-content: space-between;
                margin-left: 0;
            }

            .NavSide__toggle {
                position: static;
                top: auto;
                left: auto;
                width: 40px;
                height: 40px;
                z-index: 1100;
                cursor: pointer;
                border-radius: 5px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: none;
                background-color: transparent;
                margin-right: 0;
            }

            .NavSide__toggle i.bi {
                position: static;
                font-size: 28px;
            }

            .NavSide__toggle i.bi.open {
                color: #4B68FB;
                display: block;
            }

            .NavSide__toggle i.bi.close {
                color: #4B68FB;
                display: none;
            }

            .NavSide__toggle.NavSide__toggle--active i.bi.open {
                display: none;
            }

            .NavSide__toggle.NavSide__toggle--active i.bi.close {
                display: block;
            }

            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
                padding-top: 60px;
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            .NavSide__main-content {
                margin-left: 0;
                padding: 15px;
                padding-top: 75px;
            }

            .info-card .section {
                flex: 0 0 100%;
                margin-bottom: 1rem;
            }

            .info-card .section:last-child {
                margin-bottom: 0;
            }

            .button-group-bottom {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-top: 1.2cm;
                margin-left: 0;
                margin-right: 0;
            }

            .btn-kembali, .btn-kirim {
                width: auto;
                flex: none;
                margin: 0;
                
                height: 40px;
                padding: 0 10px;
                font-size: 0.85rem;
                border-radius: 18px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }

            .btn-kembali .icon-circle {
                width: 25px;
                height: 25px;
                margin-right: 8px;
            }
            .btn-kembali .icon-circle i {
                font-size: 1.1rem;
            }

            .form-card {
                margin-right: 0;
                padding: 15px;
            }

            .desktop-toggle-icon {
                display: none;
            }

            .info-card {
                margin-right: 0;
            }

            .penilaian-row label {
                width: 150px;
                flex-shrink: 0;
                white-space: nowrap;
                margin-right: 10px;
                text-align: left;
            }

            input.form-control-custom.input-nilai {
                flex-grow: 1;
                width: auto;
                min-width: unset;
            }
        }
    </style>
</head>
<body>
    <!-- Kontainer utama untuk sidebar navigasi dan konten utama -->
    <div id="NavSide">
        <!-- Sidebar navigasi -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <!-- Area merek/logo di sidebar -->
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <!-- Daftar menu navigasi sidebar -->
            <ul class="NavSide__sidebar-nav">
                <!-- Item menu 'Evaluasi' (ditandai aktif) -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php">
                        <span class="fw-semibold">Evaluasi</span>
                    </a>
                </li>
                <!-- Item menu 'Dokumen' -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php">
                        <span class="fw-semibold">Dokumen</span>
                    </a>
                </li>
                <!-- Item menu 'Nilai Akhir' -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php">
                        <span class="fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Kontainer untuk topbar (header responsif) dan area konten utama -->
        <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
            <!-- Ikon Hamburger Desktop (Hanya untuk Desktop) -->
            <div class="desktop-toggle-icon">
                <i class="bi bi-list"></i>
            </div>

            <!-- Topbar (header yang muncul di perangkat mobile) -->
            <div class="NavSide__topbar">
                <!-- Tombol toggle sidebar untuk mobile (dipindahkan ke sini) -->
                <div class="NavSide__toggle">
                    <i class="bi bi-list open"></i>
                    <i class="bi bi-x-lg close"></i>
                </div>
                <!-- Judul atau elemen lain di topbar bisa ditambahkan di sini jika perlu -->
            </div>

            <!-- Area konten utama halaman -->
            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>

                <!-- Kartu informasi detail sidang -->
                <div class="info-card">
                    <div class="section">
                        <!-- Grup informasi Judul Sidang -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span class="fw-bold">Judul Sidang</span>
                            </div>
                            <div class="value-row">Struktur Data</div>
                        </div>

                        <!-- Grup informasi Dosen Pembimbing -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-tie"></i>
                                <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row">Dr. Rida Indah Fariani, S.Si, M.T.I</div>
                        </div>

                        <!-- Grup informasi Dosen Penguji -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-group"></i>
                                <span class="fw-bold">Dosen Penguji</span>
                            </div>
                            <div class="value-row">
                                Timotius Victory, S.Kom, M.Kom<br>
                                Ning Ratwasturi, S.T, M.Eng
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <!-- Grup informasi Ruangan -->
                        <div class="info-group">
                            <div class="label-row">
                                <!-- START PERBAIKAN: class dan ikon -->
                                <i class="fa-solid fa-door-open"></i> 
                                <!-- END PERBAIKAN -->
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">CB101 - RPL 1B</div>
                        </div>

                        <!-- Grup informasi Tanggal -->
                        <div class="info-group">
                            <div class="label-row">
                                <!-- START PERBAIKAN: class -->
                                <i class="fa-solid fa-calendar-days"></i>
                                <!-- END PERBAIKAN -->
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">Selasa, 22 April 2025</div>
                        </div>

                        <!-- Grup informasi Jam -->
                        <div class="info-group">
                            <div class="label-row">
                                <!-- START PERBAIKAN: class -->
                                <i class="fa-solid fa-clock"></i>
                                <!-- END PERBAIKAN -->
                                <span class="fw-bold">Jam</span>
                            </div>
                            <div class="value-row">09.00 - 10.00</div>
                        </div>
                    </div>
                </div>

                <h3>Nilai Sidang (Sementara)</h3>
                <div class="form-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
                    </div>
                    <div class="row penilaian-row">
                        <div class="col-3 d-flex align-items-center">
                            <label for="nilaiLaporan" class="text-black">Nilai laporan</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="nilaiLaporan"
                                placeholder=""
                                maxlength="3"/>
                        </div>
                        <div class="col-3 d-flex align-items-center">
                            <label for="materiPresentasi" class="text-black">Materi Presentasi</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="materiPresentasi"
                                placeholder=""
                                maxlength="3"/>
                        </div>
                        <div class="col-3 d-flex align-items-center ">
                            <label for="nilaiPenyampaian" class="text-black">Penyampaian</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="nilaiPenyampaian"
                                placeholder=""
                                maxlength="3"/>
                        </div>
                        <div class="col-3 d-flex align-items-center ">
                            <label for="nilaiProyek" class="text-black">Nilai Proyek</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="nilaiProyek"
                                placeholder=""
                                maxlength="3"/>
                        </div>
                    </div>
                    <!-- Pesan error untuk input nilai sidang -->
                    <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                </div>

                <h3>Catatan Evaluasi Sidang</h3>
                <div class="form-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                    </div>
                    <div class="form-group-custom">
                        <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                        <textarea id="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.."></textarea>
                    </div>
                    <!-- Pesan error untuk catatan evaluasi -->
                    <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                </div>

                <!-- Grup tombol di bagian bawah halaman -->
                <div class="button-group-bottom">
                    <!-- Tombol 'Kembali' -->
                    <button class="btn btn-kembali" onclick="location.href='dDaftarSidang.php'">
                        <span class="icon-circle">
                            <i class="fa-solid fa-arrow-left"></i>
                        </span>
                        Kembali
                    </button>
                    <!-- Tombol 'Kirim' -->
                    <button class="btn-kirim" id="btnKirim">
                        Kirim
                    </button>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Sukses (muncul setelah berhasil mengirim evaluasi) -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content success-modal-content">
                <div class="modal-body success-modal-body">
                    <img src="../../assets/img/centang.svg" alt="Success Checkmark" class="success-icon">
                    <p class="success-message">Evaluasi Sidang Berhasil Dikirim!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Kirim (muncul sebelum mengirim evaluasi) -->
    <div class="modal fade" id="confirmationKirimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationKirimModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin hendak mengirimkan evaluasi sidang?</p>
                    <div class="d-flex justify-content-between px-5"> 
                        <!-- START PERBAIKAN: Ubah teks, hapus kelas padding Bootstrap -->
                        <button type="button" class="btn custom-batal fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn fw-semibold btn-setujui" id="btnKonfirmasiKirim">Kirimkan</button>
                        <!-- END PERBAIKAN -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Skrip untuk mengontrol sidebar (buka/tutup) dan efek aktif pada item menu
            let menuToggle = document.querySelector(".NavSide__toggle");
            let sidebar = document.getElementById("main-sidebar");

            if (menuToggle && sidebar) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            // Skrip untuk mengatur item menu sidebar yang ditandai sebagai 'aktif'
            let listItems = document.querySelectorAll(".NavSide__sidebar-item");
            if (listItems.length > 0) {
                for (let i = 0; i < listItems.length; i++) {
                    listItems[i].onclick = function() {
                        if (!this.classList.contains("NavSide__sidebar-item--active")) {
                            for (let j = 0; j < listItems.length; j++) {
                                listItems[j].classList.remove("NavSide__sidebar-item--active");
                            }
                            this.classList.add("NavSide__sidebar-item--active");
                        }
                    };
                }
            }

            // Variabel elemen DOM untuk form dan modal
            const btnKirim = document.getElementById('btnKirim');
            const nilaiLaporan = document.getElementById('nilaiLaporan');
            const materiPresentasi = document.getElementById('materiPresentasi');
            const nilaiPenyampaian = document.getElementById('nilaiPenyampaian');
            const nilaiProyek = document.getElementById('nilaiProyek');
            const catatanEvaluasi = document.getElementById('catatanEvaluasi');

            const nilaiSidangError = document.getElementById('nilaiSidangErrorMessage'); // Pesan error untuk input nilai sidang
            const catatanEvaluasiError = document.getElementById('catatanEvaluasiErrorMessage'); // Pesan error untuk catatan evaluasi

            const successModalElement = document.getElementById('successModal'); // Modal sukses
            const confirmationKirimModalElement = document.getElementById('confirmationKirimModal'); // Modal konfirmasi pengiriman
            const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim'); // Tombol 'Lanjutkan' di modal konfirmasi

            // Event listener saat modal sukses disembunyikan
            if (successModalElement) {
                successModalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'dDaftarSidang.php'; // Redirect ke halaman daftar sidang
                });
            }

            // Logika validasi input hanya angka dan batas 0-100
            document.querySelectorAll('.input-nilai').forEach(function(input){
                input.addEventListener('input', function() {
                    // Hapus karakter non-angka
                    this.value = this.value.replace(/[^0-9]/g, '');
                    // Hapus nol di depan jika lebih dari satu digit (misal '05' jadi '5')
                    if (this.value.length > 1 && this.value.startsWith('0')) {
                        this.value = this.value.replace(/^0+/, '');
                    }
                    // Batasi nilai maksimal 100
                    if (parseInt(this.value) > 100) {
                        this.value = '100';
                    }
                });
            });

            // Event listener untuk tombol 'Kirim'
            btnKirim.addEventListener('click', function(event) {
                let isValid = true;

                // Sembunyikan pesan error sebelumnya
                nilaiSidangError.style.display = 'none';
                catatanEvaluasiError.style.display = 'none';

                // Validasi kolom "Nilai Sidang (Sementara)"
                if (nilaiLaporan.value.trim() === '' ||
                    materiPresentasi.value.trim() === '' ||
                    nilaiPenyampaian.value.trim() === '' ||
                    nilaiProyek.value.trim() === '') {
                    nilaiSidangError.style.display = 'block';
                    isValid = false;
                }

                // Validasi kolom "Catatan Evaluasi Sidang"
                if (catatanEvaluasi.value.trim() === '') {
                    catatanEvaluasiError.style.display = 'block';
                    isValid = false;
                }

                // Jika ada validasi yang gagal (field kosong)
                if (!isValid) {
                    event.preventDefault(); // Hentikan tindakan default
                    Swal.fire({
                        title: 'Harap mengisi kolom nilai dan catatan terlebih dahulu sebelum mengirim!',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4B68FB'
                    });
                } else {
                    // Jika validasi sukses, tampilkan modal konfirmasi pengiriman
                    const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                    confirmationKirimModal.show();
                }
            });

            // Event listener untuk tombol 'Lanjutkan' pada modal konfirmasi pengiriman
            btnKonfirmasiKirim.addEventListener('click', function() {
                // Sembunyikan modal konfirmasi pengiriman
                const confirmationKirimModalInstance = bootstrap.Modal.getInstance(confirmationKirimModalElement);
                if (confirmationKirimModalInstance) {
                    confirmationKirimModalInstance.hide();
                }

                // Tampilkan modal sukses
                const successModal = new bootstrap.Modal(successModalElement);
                successModal.show();
            });
        });
    </script>
</body>
</html>