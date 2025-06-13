<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* CSS Anda tidak perlu diubah, jadi saya singkat di sini agar tidak terlalu panjang */
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #FFFFFF;
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

        .NavSide__main-content {
            flex-grow: 1;
            padding: 25px 30px;
            margin-left: 280px;
            transition: margin-left 0.4s ease-in-out;
        }

        .NavSide__topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            z-index: 900;
            background: #FFFFFF;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .NavSide__toggle {
            font-size: 2rem;
            cursor: pointer;
            color: #4B68FB;
            z-index: 1002;
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

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .header-left-panel {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-container .filter-label {
            font-weight: 600;
            font-size: 1rem;
            color: #333;
        }

        .main-title {
            color: rgb(0, 0, 0);
            font-weight: 700;
            font-size: 2.1rem;
            margin-bottom: 1rem;
        }

        .header-right-panel {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.75rem;
        }

        .search-input-group {
            background-color: #F3F4F6;
            border-radius: 0.5rem;
            overflow: hidden;
            max-width: 250px;
            width: 100%;
        }

        .search-input-group input.form-control {
            background-color: transparent;
            border: none;
            box-shadow: none;
        }

        .search-input-group .input-group-text {
            background-color: transparent;
            border: none;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-icons>a {
            font-size: 1.4rem;
            color: #5a5a5a;
            transition: color 0.2s ease;
        }

        .header-icons>a:hover {
            color: #4B68FB;
        }

        .header-right-panel .profile-icon,
        .NavSide__topbar .profile-icon {
            background-color:#333;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s ease;
        }

        .header-right-panel .profile-icon:hover,
        .NavSide__topbar .profile-icon:hover {
            transform: scale(1.1);
        }

        .header-right-panel .profile-icon a,
        .NavSide__topbar .profile-icon a {
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #ddAdminSidangTypeButton {
            background-color: #4B68FB;
            border-color: #4B68FB;
            border-radius: 20px;
            padding: 6px 16px;
            font-weight: 500;
            box-shadow: none !important;
        }

        .table-admin-custom {
            border-spacing: 0 10px;
            border-collapse: separate;
            width: 100%;
        }

        .table-admin-custom thead th {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        .table-admin-custom tbody tr.isiTabel {
            background-color: #F5F5F5;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .table-admin-custom .isiTabel td {
            padding: 15px 18px;
            vertical-align: middle;
        }

        .table-admin-custom .isiTabel td:first-child {
            border-radius: 10px 0 0 10px;
        }

        .table-admin-custom .isiTabel td:last-child {
            border-radius: 0 10px 10px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .table-admin-custom tbody tr.isiTabel:hover {
            background-color: #4B68FB;
            color: #FFFFFF;
        }

        .detail-btn {
            border: none !important;
            background-color: transparent !important;
            color: #4B68FB;
            padding: 0.25rem 0.5rem;
        }

        .detail-btn:hover {
            opacity: 0.7;
        }

        .table-admin-custom tbody tr.isiTabel:hover .detail-btn {
            color: #FFFFFF;
            opacity: 1;
        }

        .pagination-container {
            margin-top: 2rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #4B68FB;
            border-color: #4B68FB;
            color: white;
            z-index: 2;
        }

        .pagination .page-link {
            color: #4B68FB;
        }

        .pagination .page-link:hover {
            color: #2c45c9;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
        }

        .modal-header-custom {
            background-color: #4B68FB;
            color: white;
        }

        .modal-footer .btn-danger {
            background-color: #FD7D7D;
            border-color: #FD7D7D;
        }

        .modal-footer .btn-success {
            background-color: #4FD382;
            border-color: #4FD382;
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
            }

            .NavSide__toggle {
                position: relative;
                top: auto;
                left: auto;
            }

            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px);
            }

            .search-input-group {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5rem;
            }

            .main-title {
                font-size: 1.8rem;
            }

            .table-responsive {
                overflow-x: hidden;
            }

            .table-admin-custom thead {
                display: none;
            }

            .table-admin-custom tbody,
            .table-admin-custom tr,
            .table-admin-custom td {
                display: block;
                width: 100%;
            }

            .table-admin-custom tr.isiTabel {
                margin-bottom: 20px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .table-admin-custom td {
                display: flex;
                justify-content: space-between;
                padding: 12px 15px;
                text-align: right;
                border-bottom: 1px solid #e9e9e9;
                align-items: center;
            }

            .table-admin-custom tr.isiTabel:hover,
            .table-admin-custom tr.isiTabel:hover .detail-btn {
                background-color: #F5F5F5;
                color: #000;
            }

            .table-admin-custom tr.isiTabel:hover .detail-btn {
                color: #4B68FB;
            }

            .table-admin-custom td:first-child,
            .table-admin-custom td:last-child {
                border-radius: 0;
            }

            .table-admin-custom td:last-child {
                border-bottom: none;
                justify-content: center;
            }

            .table-admin-custom td::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                margin-right: 10px;
                color: #333;
            }
        }
    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo Admin">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aBeranda.php"><span
                            class="fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aPenjadwalan.php"><span
                            class="fw-semibold">Penjadwalan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span
                            class="fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal"
                        data-bs-target="#logABeranda"><span class="fw-semibold">Keluar</span></a></li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div id="mobile-icons-container"></div>
        </div>

        <main class="NavSide__main-content" id="adminDaftarSidangContent">
            <div class="main-header">
                <div class="header-left-panel">
                    <h1 class="main-title">Daftar Pengajuan Sidang</h1>
                    <div class="filter-container">
                        <span class="filter-label fw-semibold">Filter:</span>
                        <div class="dropdown" id="switcherDropdownContainer">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton"
                                data-bs-toggle="dropdown" aria-expanded="false">Semua</button>
                            <ul class="dropdown-menu" id="dynamicDropdownMenu"></ul>
                        </div>
                    </div>
                </div>
                <div class="header-right-panel">
                    <div id="desktop-icons-container">
                        <div class="header-icons">
                            <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                            <div class="profile-icon"><a href="aProfil.php" title="Profil"><i
                                        class="bi bi-person-fill"></i></a></div>
                        </div>
                    </div>
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari"
                            id="searchInput">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-admin-custom">
                    <thead>
                        <tr>
                            <th scope="col">Nomor</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col" id="thDynamicHeader">Judul/Mata Kuliah</th>
                            <th scope="col">Pembimbing</th>
                            <th scope="col" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="adminSidangContent">
                        <tr class="isiTabel" data-id="TA001" data-type="ta">
                            <td data-label="Nomor">TA001</td>
                            <td data-label="NIM">0920240053</td>
                            <td data-label="Nama">Nayaka Ivanna</td>
                            <td data-label="Judul/MK">Sistem Pengajuan Sidang</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA002" data-type="ta">
                            <td data-label="Nomor">TA002</td>
                            <td data-label="NIM">0920240054</td>
                            <td data-label="Nama">Zahrah Imelda</td>
                            <td data-label="Judul/MK">Pengembangan Aplikasi Mobile Edukasi</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA003" data-type="ta">
                            <td data-label="Nomor">TA003</td>
                            <td data-label="NIM">0920240055</td>
                            <td data-label="Nama">Doni Firmansyah</td>
                            <td data-label="Judul/MK">Analisis Big Data E-commerce</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA004" data-type="ta">
                            <td data-label="Nomor">TA004</td>
                            <td data-label="NIM">0920240056</td>
                            <td data-label="Nama">Eka Putri</td>
                            <td data-label="Judul/MK">Machine Learning untuk Prediksi</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA005" data-type="ta">
                            <td data-label="Nomor">TA005</td>
                            <td data-label="NIM">0920240057</td>
                            <td data-label="Nama">Hadi Wijaya</td>
                            <td data-label="Judul/MK">IoT untuk Smart Home</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA006" data-type="ta">
                            <td data-label="Nomor">TA006</td>
                            <td data-label="NIM">0920240058</td>
                            <td data-label="Nama">Indah Permata</td>
                            <td data-label="Judul/MK">Keamanan Siber</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA007" data-type="ta">
                            <td data-label="Nomor">TA007</td>
                            <td data-label="NIM">0920240059</td>
                            <td data-label="Nama">Lia Ananda</td>
                            <td data-label="Judul/MK">Game Development 2D</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA008" data-type="ta">
                            <td data-label="Nomor">TA008</td>
                            <td data-label="NIM">0920240060</td>
                            <td data-label="Nama">Mega Chandra</td>
                            <td data-label="Judul/MK">Virtual Reality untuk Terapi</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA009" data-type="ta">
                            <td data-label="Nomor">TA009</td>
                            <td data-label="NIM">0920240061</td>
                            <td data-label="Nama">Rian Ardiansyah</td>
                            <td data-label="Judul/MK">Cloud Computing Service</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA010" data-type="ta">
                            <td data-label="Nomor">TA010</td>
                            <td data-label="NIM">0920240062</td>
                            <td data-label="Nama">Siska Hartati</td>
                            <td data-label="Judul/MK">Augmented Reality pada Pemasaran</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA011" data-type="ta">
                            <td data-label="Nomor">TA011</td>
                            <td data-label="NIM">0920240063</td>
                            <td data-label="Nama">Umar Bakri</td>
                            <td data-label="Judul/MK">Sistem Rekomendasi Film</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA012" data-type="ta">
                            <td data-label="Nomor">TA012</td>
                            <td data-label="NIM">0920240064</td>
                            <td data-label="Nama">Vina Panduwinata</td>
                            <td data-label="Judul/MK">Analisis Sentimen Media Sosial</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA013" data-type="ta">
                            <td data-label="Nomor">TA013</td>
                            <td data-label="NIM">0920240065</td>
                            <td data-label="Nama">Yoga Pratama</td>
                            <td data-label="Judul/MK">Deteksi Objek Real-time</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA014" data-type="ta">
                            <td data-label="Nomor">TA014</td>
                            <td data-label="NIM">0920240066</td>
                            <td data-label="Nama">Zaskia Adya</td>
                            <td data-label="Judul/MK">Perancangan UI/UX Aplikasi Kesehatan</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA015" data-type="ta">
                            <td data-label="Nomor">TA015</td>
                            <td data-label="NIM">0920240067</td>
                            <td data-label="Nama">Abdul Ghofur</td>
                            <td data-label="Judul/MK">Robotika Cerdas</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA016" data-type="ta">
                            <td data-label="Nomor">TA016</td>
                            <td data-label="NIM">0920240068</td>
                            <td data-label="Nama">Bella Saphira</td>
                            <td data-label="Judul/MK">Implementasi Blockchain</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA017" data-type="ta">
                            <td data-label="Nomor">TA017</td>
                            <td data-label="NIM">0920240069</td>
                            <td data-label="Nama">Candra Darusman</td>
                            <td data-label="Judul/MK">Data Mining pada Retail</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA018" data-type="ta">
                            <td data-label="Nomor">TA018</td>
                            <td data-label="NIM">0920240070</td>
                            <td data-label="Nama">Diana Prince</td>
                            <td data-label="Judul/MK">Computer Vision untuk Medis</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA019" data-type="ta">
                            <td data-label="Nomor">TA019</td>
                            <td data-label="NIM">0920240071</td>
                            <td data-label="Nama">Farhan Jijima</td>
                            <td data-label="Judul/MK">Pengolahan Bahasa Alami</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA020" data-type="ta">
                            <td data-label="Nomor">TA020</td>
                            <td data-label="NIM">0920240072</td>
                            <td data-label="Nama">Genta Kiswara</td>
                            <td data-label="Judul/MK">Sistem Informasi Geografis</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA021" data-type="ta">
                            <td data-label="Nomor">TA021</td>
                            <td data-label="NIM">0920240073</td>
                            <td data-label="Nama">Hana Malasan</td>
                            <td data-label="Judul/MK">Deep Learning untuk Audio</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA022" data-type="ta">
                            <td data-label="Nomor">TA022</td>
                            <td data-label="NIM">0920240074</td>
                            <td data-label="Nama">Irfan Hakim</td>
                            <td data-label="Judul/MK">Jaringan Syaraf Tiruan</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA023" data-type="ta">
                            <td data-label="Nomor">TA023</td>
                            <td data-label="NIM">0920240075</td>
                            <td data-label="Nama">Jihan Audy</td>
                            <td data-label="Judul/MK">Kriptografi Modern</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA024" data-type="ta">
                            <td data-label="Nomor">TA024</td>
                            <td data-label="NIM">0920240076</td>
                            <td data-label="Nama">Kris Dayanti</td>
                            <td data-label="Judul/MK">Manajemen Proyek IT</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA025" data-type="ta">
                            <td data-label="Nomor">TA025</td>
                            <td data-label="NIM">0920240077</td>
                            <td data-label="Nama">Laura Basuki</td>
                            <td data-label="Judul/MK">Sistem Pendukung Keputusan</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM001" data-type="semester">
                            <td data-label="Nomor">SEM001</td>
                            <td data-label="NIM">0920230053</td>
                            <td data-label="Nama">Budi Santoso</td>
                            <td data-label="Judul/MK">Basis Data 1</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM002" data-type="semester">
                            <td data-label="Nomor">SEM002</td>
                            <td data-label="NIM">0920230054</td>
                            <td data-label="Nama">Citra Lestari</td>
                            <td data-label="Judul/MK">Pemrograman 2</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM003" data-type="semester">
                            <td data-label="Nomor">SEM003</td>
                            <td data-label="NIM">0920230055</td>
                            <td data-label="Nama">Fajar Nugroho</td>
                            <td data-label="Judul/MK">Jaringan Komputer</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM004" data-type="semester">
                            <td data-label="Nomor">SEM004</td>
                            <td data-label="NIM">0920230056</td>
                            <td data-label="Nama">Gita Amelia</td>
                            <td data-label="Judul/MK">Sistem Operasi</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM005" data-type="semester">
                            <td data-label="Nomor">SEM005</td>
                            <td data-label="NIM">0920230057</td>
                            <td data-label="Nama">Joko Susilo</td>
                            <td data-label="Judul/MK">Kalkulus Lanjut</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM006" data-type="semester">
                            <td data-label="Nomor">SEM006</td>
                            <td data-label="NIM">0920230058</td>
                            <td data-label="Nama">Kartika Sari</td>
                            <td data-label="Judul/MK">Struktur Data</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM007" data-type="semester">
                            <td data-label="Nomor">SEM007</td>
                            <td data-label="NIM">0920230059</td>
                            <td data-label="Nama">Nadia Putri</td>
                            <td data-label="Judul/MK">Algoritma & Pemrograman</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM008" data-type="semester">
                            <td data-label="Nomor">SEM008</td>
                            <td data-label="NIM">0920230060</td>
                            <td data-label="Nama">Putra Bangsa</td>
                            <td data-label="Judul/MK">Rekayasa Perangkat Lunak</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM009" data-type="semester">
                            <td data-label="Nomor">SEM009</td>
                            <td data-label="NIM">0920230061</td>
                            <td data-label="Nama">Tono Martono</td>
                            <td data-label="Judul/MK">Kecerdasan Buatan</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM010" data-type="semester">
                            <td data-label="Nomor">SEM010</td>
                            <td data-label="NIM">0920230062</td>
                            <td data-label="Nama">Wati Kurnia</td>
                            <td data-label="Judul/MK">Interaksi Manusia & Komputer</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM011" data-type="semester">
                            <td data-label="Nomor">SEM011</td>
                            <td data-label="NIM">0920230063</td>
                            <td data-label="Nama">Xavier Daniels</td>
                            <td data-label="Judul/MK">Teori Bahasa & Automata</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM012" data-type="semester">
                            <td data-label="Nomor">SEM012</td>
                            <td data-label="NIM">0920230064</td>
                            <td data-label="Nama">Yasmine Al-Rashid</td>
                            <td data-label="Judul/MK">Manajemen Basis Data</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM013" data-type="semester">
                            <td data-label="Nomor">SEM013</td>
                            <td data-label="NIM">0920230065</td>
                            <td data-label="Nama">Zainal Abidin</td>
                            <td data-label="Judul/MK">Pemrograman Web Lanjut</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM014" data-type="semester">
                            <td data-label="Nomor">SEM014</td>
                            <td data-label="NIM">0920230066</td>
                            <td data-label="Nama">Alya Rohali</td>
                            <td data-label="Judul/MK">Metodologi Penelitian</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM015" data-type="semester">
                            <td data-label="Nomor">SEM015</td>
                            <td data-label="NIM">0920230067</td>
                            <td data-label="Nama">Ben Kasyafani</td>
                            <td data-label="Judul/MK">Analisis & Desain Sistem</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM016" data-type="semester">
                            <td data-label="Nomor">SEM016</td>
                            <td data-label="NIM">0920230068</td>
                            <td data-label="Nama">Desta Mahendra</td>
                            <td data-label="Judul/MK">Sistem Terdistribusi</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM017" data-type="semester">
                            <td data-label="Nomor">SEM017</td>
                            <td data-label="NIM">0920230069</td>
                            <td data-label="Nama">Enzy Storia</td>
                            <td data-label="Judul/MK">Pemrosesan Sinyal Digital</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM018" data-type="semester">
                            <td data-label="Nomor">SEM018</td>
                            <td data-label="NIM">0920230070</td>
                            <td data-label="Nama">Febby Rastanty</td>
                            <td data-label="Judul/MK">Fisika Dasar</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM019" data-type="semester">
                            <td data-label="Nomor">SEM019</td>
                            <td data-label="NIM">0920230071</td>
                            <td data-label="Nama">Gilang Dirga</td>
                            <td data-label="Judul/MK">Matematika Diskrit</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM020" data-type="semester">
                            <td data-label="Nomor">SEM020</td>
                            <td data-label="NIM">0920230072</td>
                            <td data-label="Nama">Herjunot Ali</td>
                            <td data-label="Judul/MK">Logika Informatika</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM021" data-type="semester">
                            <td data-label="Nomor">SEM021</td>
                            <td data-label="NIM">0920230073</td>
                            <td data-label="Nama">Indra Herlambang</td>
                            <td data-label="Judul/MK">Arsitektur Komputer</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM022" data-type="semester">
                            <td data-label="Nomor">SEM022</td>
                            <td data-label="NIM">0920230074</td>
                            <td data-label="Nama">Jessica Mila</td>
                            <td data-label="Judul/MK">Grafika Komputer</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM023" data-type="semester">
                            <td data-label="Nomor">SEM023</td>
                            <td data-label="NIM">0920230075</td>
                            <td data-label="Nama">Kevin Julio</td>
                            <td data-label="Judul/MK">Proyek Perangkat Lunak</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM024" data-type="semester">
                            <td data-label="Nomor">SEM024</td>
                            <td data-label="NIM">0920230076</td>
                            <td data-label="Nama">Luna Maya</td>
                            <td data-label="Judul/MK">Etika Profesi</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM025" data-type="semester">
                            <td data-label="Nomor">SEM025</td>
                            <td data-label="NIM">0920230077</td>
                            <td data-label="Nama">Morgan Oey</td>
                            <td data-label="Judul/MK">Kewirausahaan</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i
                                        class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                </nav>
            </div>

        </main>
    </div>

    <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h1 class="modal-title mx-auto fs-5" id="modalLogoutLabel">Perhatian!</h1>
                </div>
                <div class="modal-body text-center py-3">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success"
                        onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentPage = 1;
            const rowsPerPage = 10;
            let activeRows = [];
            let currentTypeFilter = 'All';

            const paginationControls = document.getElementById('pagination-controls');
            const allTableRows = Array.from(document.querySelectorAll('#adminSidangContent tr.isiTabel'));
            const searchInput = document.getElementById('searchInput');

            function displayPage(page) {
                currentPage = page;
                activeRows.forEach(row => row.style.display = 'none');
                const startIndex = (page - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedRows = activeRows.slice(startIndex, endIndex);
                paginatedRows.forEach(row => {
                    row.style.display = '';
                });
                updatePaginationButtons();
            }

            function setupPagination() {
                paginationControls.innerHTML = '';
                const pageCount = Math.ceil(activeRows.length / rowsPerPage);
                if (pageCount <= 1) return;

                const prevButton = document.createElement('li');
                prevButton.className = 'page-item';
                prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage > 1) displayPage(currentPage - 1);
                });
                paginationControls.appendChild(prevButton);

                for (let i = 1; i <= pageCount; i++) {
                    const pageButton = document.createElement('li');
                    pageButton.className = 'page-item';
                    pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        displayPage(i);
                    });
                    paginationControls.appendChild(pageButton);
                }

                const nextButton = document.createElement('li');
                nextButton.className = 'page-item';
                nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    const totalPages = Math.ceil(activeRows.length / rowsPerPage);
                    if (currentPage < totalPages) displayPage(currentPage + 1);
                });
                paginationControls.appendChild(nextButton);
                updatePaginationButtons();
            }

            function updatePaginationButtons() {
                const pageCount = Math.ceil(activeRows.length / rowsPerPage);
                const pageItems = paginationControls.querySelectorAll('.page-item');
                if (pageItems.length === 0) return;
                pageItems.forEach((item, index) => {
                    item.classList.remove('active', 'disabled');
                    if (index === 0) {
                        if (currentPage === 1) item.classList.add('disabled');
                    } else if (index === pageItems.length - 1) {
                        if (currentPage === pageCount) item.classList.add('disabled');
                    } else {
                        if (index === currentPage) {
                            item.classList.add('active');
                        }
                    }
                });
            }

            function updateTableDisplay() {
                const searchTerm = searchInput.value.toLowerCase();
                let filteredRows = allTableRows;

                if (currentTypeFilter !== 'All') {
                    filteredRows = filteredRows.filter(row => row.dataset.type === currentTypeFilter);
                }

                if (searchTerm) {
                    filteredRows = filteredRows.filter(row => {
                        const rowText = row.textContent.toLowerCase();
                        return rowText.includes(searchTerm);
                    });
                }

                activeRows = filteredRows;
                allTableRows.forEach(row => row.style.display = 'none');
                setupPagination();
                displayPage(1);
            }

            window.switchAdminSidangView = function (viewType) {
                const dynamicHeader = document.getElementById("thDynamicHeader");
                const dropdownMenu = document.getElementById("dynamicDropdownMenu");
                const ddButton = document.getElementById("ddAdminSidangTypeButton");
                const dynamicMKHeader = document.querySelectorAll('[data-label="Judul/MK"], [data-label="Judul Sidang"], [data-label="Mata Kuliah"]');

                currentTypeFilter = viewType;

                dropdownMenu.innerHTML = '';
                let options = '';
                let mobileLabel = "Judul/Mata Kuliah";

                if (viewType === 'All') {
                    ddButton.textContent = "Semua";
                    dynamicHeader.textContent = "Judul/Mata Kuliah";
                    options = `<li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('ta')">Sidang TA</a></li>
                               <li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('semester')">Sidang Semester</a></li>`;
                } else if (viewType === 'ta') {
                    ddButton.textContent = "Sidang TA";
                    dynamicHeader.textContent = "Judul Sidang";
                    mobileLabel = "Judul Sidang";
                    options = `<li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('All')">Semua</a></li>
                               <li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('semester')">Sidang Semester</a></li>`;
                } else if (viewType === 'semester') {
                    ddButton.textContent = "Sidang Semester";
                    dynamicHeader.textContent = "Mata Kuliah";
                    mobileLabel = "Mata Kuliah";
                    options = `<li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('All')">Semua</a></li>
                               <li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('ta')">Sidang TA</a></li>`;
                }

                dynamicMKHeader.forEach(th => th.setAttribute('data-label', mobileLabel));
                dropdownMenu.insertAdjacentHTML('beforeend', options);

                updateTableDisplay();
            }

            const listItems = document.querySelectorAll(".NavSide__sidebar-item");
            listItems.forEach(item => {
                item.addEventListener('click', function (event) {
                    const link = this.querySelector('a');
                    if (link && !link.hasAttribute('data-bs-toggle')) {
                        window.location.href = link.href;
                    }
                });
            });

            allTableRows.forEach(row => {
                row.addEventListener('click', function (event) {
                    const detailButton = event.target.closest('.detail-btn');
                    if (detailButton) {
                        const sidangId = this.dataset.id;
                        const sidangType = this.dataset.type;
                        if (sidangId && sidangType) {
                            if (sidangType === 'ta') {
                                window.location.href = `aDetailSidangTA.php?type=<span class="math-inline">\{sidangType\}&id\=</span>{sidangId}`;
                            } else if (sidangType === 'semester') {
                                window.location.href = `aDetailSidangSem.php?type=<span class="math-inline">\{sidangType\}&id\=</span>{sidangId}`;
                            }
                        }
                    }
                });
            });

            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            const desktopIconsContainer = document.getElementById('desktop-icons-container');
            const mobileIconsContainer = document.getElementById('mobile-icons-container');
            if (desktopIconsContainer) {
                const headerIcons = desktopIconsContainer.querySelector('.header-icons');
                function handleIconPlacement() {
                    if (window.innerWidth <= 992) {
                        if (!mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
                    } else {
                        if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons);
                    }
                }
                if (menuToggle && sidebar) {
                    menuToggle.onclick = () => {
                        menuToggle.classList.toggle("NavSide__toggle--active");
                        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                    };
                }
                handleIconPlacement();
                window.addEventListener('resize', handleIconPlacement);
            }

            searchInput.addEventListener('keyup', updateTableDisplay);

           switchAdminSidangView('All');
        });
    </script>
</body>

</html>