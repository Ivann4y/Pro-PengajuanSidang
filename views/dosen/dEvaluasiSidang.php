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
        /* Gaya Umum dan Body */
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

        /* Gaya Dasar Sidebar, Konten Utama, dan Kartu Info */
        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar navigasi */
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

        /* Area merek/logo di sidebar */
        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        /* Gambar logo di sidebar */
        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
            filter: brightness(0) invert(1); /* Membuat logo putih */
        }

        /* Daftar navigasi sidebar */
        .NavSide__sidebar-nav {
            width: 100%;
            padding-left: 0;
            padding-top: 0;
            list-style: none;
            flex-grow: 1;
        }

        /* Setiap item di daftar navigasi sidebar */
        .NavSide__sidebar-item {
            position: relative;
            display: block;
            width: 100%;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            margin-bottom: 15px;
        }

        /* Link dalam item navigasi sidebar */
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

        /* Judul teks dalam item navigasi sidebar */
        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
            color: white;
        }

        /* Item navigasi yang sedang aktif */
        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        /* Warna teks untuk item navigasi yang aktif */
        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4B68FB !important;
        }

        /* Efek melengkung di atas item aktif */
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

        /* Efek melengkung di bawah item aktif */
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

        /* Menampilkan efek melengkung saat item navigasi aktif */
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        /* Area konten utama halaman */
        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm);
            margin-left: 255px; /* Margin kiri konten utama */
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
        }

        /* Judul utama H2 */
        .NavSide__main-content h2 {
            margin-bottom: 0.9cm;
            font-weight: 700;
        }

        /* Sub-judul H3 */
        .NavSide__main-content h3 {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 0.2cm;
        }

        /* Badge status */
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

        /* Badge status 'approved' (disetujui) */
        .status-badge.approved {
            background-color: #4BFBAF;
        }

        /* Kartu informasi */
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
            margin-right: 45px;
        }

        /* Pseudo-element untuk efek overlay biru di info-card */
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

        /* Efek overlay biru membesar saat info-card dihover */
        .info-card:hover::after {
            width: 100%;
            border-radius: 20px;
        }

        /* Bagian (section) di dalam info-card */
        .info-card .section {
            flex: 0 0 48%;
            z-index: 1;
            color: #333;
            transition: color 0.4s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Warna teks section jadi putih saat info-card dihover */
        .info-card:hover .section {
            color: white;
        }

        /* Grup informasi dalam section */
        .info-card .section .info-group {
            margin-bottom: 1rem;
        }

        /* Grup informasi terakhir dalam section, hapus margin bawah */
        .info-card .section .info-group:last-child {
            margin-bottom: 0;
        }

        /* Baris label (ikon dan teks) dalam info-group */
        .info-card .section .label-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        /* Ikon dalam baris label */
        .info-card .section .label-row i {
            margin-right: 10px;
            color: #495057;
            font-weight: 900;
            transition: color 0.4s ease;
            width: 20px;
            text-align: center;
        }

        /* Warna ikon jadi putih saat info-card dihover */
        .info-card:hover .section .label-row i {
            color: white;
        }

        /* Teks tebal dalam baris label */
        .info-card .section .label-row .fw-bold {
            font-weight: 600;
            font-size: 1.05rem;
        }

        /* Baris nilai dalam info-group */
        .info-card .section .value-row {
            margin-left: 30px;
            line-height: 1.5;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* Tombol 'Kembali' */
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

        /* Efek hover tombol 'Kembali' */
        .btn-kembali:hover {
            position: relative;
            background-color: white;
            color: #4B68FB;
        }

        /* Lingkaran ikon di tombol 'Kembali' */
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

        /* Lingkaran ikon di tombol 'Kembali' saat dihover */
        .btn-kembali:hover .icon-circle {
            background-color: #4B68FB;
        }

        /* Ikon di tombol 'Kembali' */
        .btn-kembali .icon-circle i {
            color: #4B68FB;
        }

        /* Ikon di tombol 'Kembali' saat dihover */
        .btn-kembali:hover .icon-circle i {
            color: white;
        }

        /* Kartu untuk form input (nilai dan catatan) */
        .form-card {
            background:rgb(235, 238, 245);
            border-radius: 30px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 25px;
            margin-bottom: 1.2cm;
            margin-right: 40px;
        }

        /* Judul H4 di dalam form-card */
        .form-card h4 {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.8cm;
        }

        /* Gaya untuk baris input penilaian */
        .penilaian-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: center;
            margin-top: 0.5cm;
            margin-bottom: 1rem;
            margin-left: 0 !important; /* Override Bootstrap's default negative margins */
            margin-right: 0 !important;
            gap: 20px; /* Jarak antara kolom dan baris */
        }

        /* Kolom di dalam baris penilaian */
        .penilaian-row .col-3 {
            padding: 0 !important; /* Hapus padding default Bootstrap */
            flex: 0 0 auto;
            width: calc(25% - (20px * 3 / 4)); /* Hitung lebar untuk 4 kolom dengan jarak */
            box-sizing: border-box;
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        /* Label input penilaian */
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

        /* Karakter titik dua di label penilaian */
        .penilaian-row .colon {
            flex-shrink: 0;
            margin-right: 10px;
            color: #333;
        }

        /* Input nilai kustom */
        input.form-control-custom.input-nilai {
            width: 75px; /* Lebar tetap untuk input */
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

        /* Efek fokus pada input nilai */
        .form-card .form-control-custom.input-nilai:focus {
            border-color: #4B68FB;
            box-shadow: 0 0 0 0.25rem rgba(75, 104, 251, 0.25);
            outline: none;
        }

        /* Grup form kustom */
        .form-group-custom {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Label di grup form kustom */
        .form-group-custom label {
            flex: 0 0 180px;
            margin-right: 20px;
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }

        /* Kontrol form kustom (input teks, dll.) */
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

        /* Efek fokus pada kontrol form kustom */
        .form-group-custom .form-control-custom:focus {
            border-color: #4B68FB;
            box-shadow: 0 0 0 0.25rem rgba(75, 104, 251, 0.25);
            outline: none;
        }

        /* Textarea di grup form kustom */
        .form-group-custom textarea.form-control-custom {
            min-height: 200px;
            resize: vertical;
        }

        /* Grup tombol di bagian bawah */
        .button-group-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.2cm;
            margin-right: 40px;
        }

        /* Tombol 'Kirim' */
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

        /* Efek hover tombol 'Kirim' */
        .btn-kirim:hover {
            background-color: #3AB070;
            color: white;
        }

        /* Konten modal sukses */
        .success-modal-content {
            background-color: rgb(235, 238, 245);
            border-radius: 30px;
            border: none;
            padding: 20px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
        }

        /* Body modal sukses */
        .success-modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 30px 20px;
        }

        /* Ikon sukses di modal */
        .success-icon {
            width: 6rem;
            height: 6rem;
            margin-bottom: 20px;
        }

        /* Pesan sukses di modal */
        .success-message {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        /* Pesan error */
        .error-message {
            color: red;
            font-size: 0.9rem;
            font-weight: 500;
            display: none; /* Default disembunyikan */
            margin-top: 10px;
            margin-left: 0;
            text-align: left;
        }

        /* Modal konfirmasi */
        .modal-content.custom-modal-content {
            border-radius: 30px !important;
            background-color: #f8f9fa;
            border: none;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .modal-header.custom-modal-header {
            border-bottom: none;
            justify-content: center;
            padding: 0;
        }

        .modal-body.custom-modal-body {
            padding: 20px;
            text-align: center;
        }

        /* Tombol konfirmasi dalam modal */
        .btnKonfirmasi {
            border-radius: 20px;
            padding: 8px 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            height: 45px;
            border: none;
        }

        /* Tombol 'Tolak' dalam modal */
        .btn-tolak {
            background-color: #FD7D7D;
            color: white;
        }
        .btn-tolak:hover {
            background-color: #F85C5C;
            color: white;
        }

        /* Tombol 'Setujui' dalam modal */
        .btn-setujui {
            background-color: #4FD382;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 0 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            height: 45px;
        }

        /* Efek hover untuk tombol konfirmasi 'Kirim' */
        #btnKonfirmasiKirim:hover {
            background-color: #157347 !important;
            color: #fff !important;
        }

        /* Media query untuk perangkat dengan lebar layar <= 768px (tablet & mobile) */
        @media (max-width: 768px) {
            /* Baris input penilaian responsif */
            .penilaian-row {
                margin-left: 0 !important;
                margin-right: 0 !important;
                gap: 15px 0; /* Hanya jarak vertikal */
            }
            .penilaian-row .col-3 {
                width: 100%; /* Kolom bertumpuk */
                justify-content: flex-start;
                padding: 0 !important;
                margin-bottom: 0;
            }
            .penilaian-row label {
                min-width: unset;
                text-align: left;
            }
            .form-card .form-control-custom.input-nilai {
                width: 100%; /* Input lebar penuh */
            }

            /* Grup form kustom responsif */
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

            /* Modal responsif */
            .modal-dialog {
                margin: 1rem auto;
                max-width: 95% !important;
            }
            .custom-modal-content {
                padding: 15px;
            }
            .modal-body.custom-modal-body {
                padding: 20px;
            }
            .custom-modal-body .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            .custom-modal-body .d-flex .btnKonfirmasi {
                width: 100%;
            }
        }

        /* Media query untuk perangkat dengan lebar layar <= 700px (mobile) */
        @media (max-width: 700px) {
            /* Tombol toggle sidebar */
            .NavSide__toggle {
                position: fixed;
                top: 15px;
                left: 15px;
                width: 40px;
                height: 40px;
                z-index: 1100;
                transition: left 0.5s ease-in-out;
                cursor: pointer;
                border-radius: 5px;
                display: flex; /* Tampilkan di mobile */
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                background-color: transparent;
            }

            .NavSide__toggle i.bi {
                position: absolute;
                font-size: 28px;
                display: none;
            }

            .NavSide__toggle i.bi.open {
                color: #4B68FB;
            }

            .NavSide__toggle i.bi.close {
                color: #4B68FB;
            }

            .NavSide__toggle.NavSide__toggle--active i.bi.open {
                display: none;
            }

            .NavSide__toggle.NavSide__toggle--active i.bi.close {
                display: block;
            }

            /* Topbar mobile */
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
            }

            /* Sidebar di mobile */
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
            }

            /* Sidebar aktif di mobile */
            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            /* Konten utama di mobile */
            .NavSide__main-content {
                margin-left: 0;
                padding: 15px;
                padding-top: 75px; /* Sesuaikan dengan tinggi topbar dan padding */
            }

            /* Section di info-card mobile */
            .info-card .section {
                flex: 0 0 100%;
                margin-bottom: 1rem;
            }

            /* Section terakhir info-card mobile */
            .info-card .section:last-child {
                margin-bottom: 0;
            }

            /* Grup tombol bawah mobile */
            .button-group-bottom {
                flex-direction: column;
                gap: 1rem;
            }

            /* Tombol 'Kembali' dan 'Kirim' mobile */
            .btn-kembali, .btn-kirim {
                width: 100%;
            }

            /* Label form kustom mobile */
            .form-group-custom label {
                flex: 0 0 100%;
                margin-bottom: 0.5rem;
            }

            /* Kontrol form kustom mobile */
            .form-group-custom .form-control-custom {
                width: 100%;
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

        <!-- Tombol toggle sidebar untuk mobile -->
        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
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
                            <i class="fa-solid fa-door-open"></i>
                            <span class="fw-bold">Ruangan</span>
                        </div>
                        <div class="value-row">CB101 - RPL 1B</div>
                    </div>

                    <!-- Grup informasi Tanggal -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span class="fw-bold">Tanggal</span>
                        </div>
                        <div class="value-row">Selasa, 22 April 2025</div>
                    </div>

                    <!-- Grup informasi Jam -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-clock"></i>
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
                            maxlength="3"/> <!-- Atribut 'disabled' dihapus di sini -->
                    </div>
                    <div class="col-3 d-flex align-items-center">
                        <label for="materiPresentasi" class="text-black">Materi Presentasi</label>
                        <span class="colon">:</span>
                        <input
                            type="text"
                            class="form-control-custom input-nilai"
                            id="materiPresentasi"
                            placeholder=""
                            maxlength="3"/> <!-- Atribut 'disabled' dihapus di sini -->
                    </div>
                    <div class="col-3 d-flex align-items-center ">
                        <label for="nilaiPenyampaian" class="text-black">Penyampaian</label>
                        <span class="colon">:</span>
                        <input
                            type="text"
                            class="form-control-custom input-nilai"
                            id="nilaiPenyampaian"
                            placeholder=""
                            maxlength="3"/> <!-- Atribut 'disabled' dihapus di sini -->
                    </div>
                    <div class="col-3 d-flex align-items-center ">
                        <label for="nilaiProyek" class="text-black">Nilai Proyek</label>
                        <span class="colon">:</span>
                        <input
                            type="text"
                            class="form-control-custom input-nilai"
                            id="nilaiProyek"
                            placeholder=""
                            maxlength="3"/> <!-- Atribut 'disabled' dihapus di sini -->
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
                    <textarea id="catatanEvaluasi" class="form-control-custom"></textarea>
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
            <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin hendak mengirimkan evaluasi sidang?</p>
                    <div class="d-flex justify-content-between px-5">
                        <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="px-4 py-2 fw-semibold btn-setujui" id="btnKonfirmasiKirim">Kirimkan</button>
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