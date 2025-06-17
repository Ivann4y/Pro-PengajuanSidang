<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Pengajuan Sidang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/aDaftarSidang.css">
        <integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    

    <style>
        body {
            background-color: #ffffff;
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
                    <h1 class="main-title">Daftar Sidang</h1>
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
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA004" data-type="ta">
                            <td data-label="Nomor">TA004</td>
                            <td data-label="NIM">0920240056</td>
                            <td data-label="Nama">Eka Putri</td>
                            <td data-label="Judul/MK">Machine Learning untuk Prediksi</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA005" data-type="ta">
                            <td data-label="Nomor">TA005</td>
                            <td data-label="NIM">0920240057</td>
                            <td data-label="Nama">Hadi Wijaya</td>
                            <td data-label="Judul/MK">IoT untuk Smart Home</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA006" data-type="ta">
                            <td data-label="Nomor">TA006</td>
                            <td data-label="NIM">0920240058</td>
                            <td data-label="Nama">Indah Permata</td>
                            <td data-label="Judul/MK">Keamanan Siber</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA007" data-type="ta">
                            <td data-label="Nomor">TA007</td>
                            <td data-label="NIM">0920240059</td>
                            <td data-label="Nama">Lia Ananda</td>
                            <td data-label="Judul/MK">Game Development 2D</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA008" data-type="ta">
                            <td data-label="Nomor">TA008</td>
                            <td data-label="NIM">0920240060</td>
                            <td data-label="Nama">Mega Chandra</td>
                            <td data-label="Judul/MK">Virtual Reality untuk Terapi</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA009" data-type="ta">
                            <td data-label="Nomor">TA009</td>
                            <td data-label="NIM">0920240061</td>
                            <td data-label="Nama">Rian Ardiansyah</td>
                            <td data-label="Judul/MK">Cloud Computing Service</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA010" data-type="ta">
                            <td data-label="Nomor">TA010</td>
                            <td data-label="NIM">0920240062</td>
                            <td data-label="Nama">Siska Hartati</td>
                            <td data-label="Judul/MK">Augmented Reality pada Pemasaran</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA011" data-type="ta">
                            <td data-label="Nomor">TA011</td>
                            <td data-label="NIM">0920240063</td>
                            <td data-label="Nama">Umar Bakri</td>
                            <td data-label="Judul/MK">Sistem Rekomendasi Film</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA012" data-type="ta">
                            <td data-label="Nomor">TA012</td>
                            <td data-label="NIM">0920240064</td>
                            <td data-label="Nama">Vina Panduwinata</td>
                            <td data-label="Judul/MK">Analisis Sentimen Media Sosial</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA013" data-type="ta">
                            <td data-label="Nomor">TA013</td>
                            <td data-label="NIM">0920240065</td>
                            <td data-label="Nama">Yoga Pratama</td>
                            <td data-label="Judul/MK">Deteksi Objek Real-time</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA014" data-type="ta">
                            <td data-label="Nomor">TA014</td>
                            <td data-label="NIM">0920240066</td>
                            <td data-label="Nama">Zaskia Adya</td>
                            <td data-label="Judul/MK">Perancangan UI/UX Aplikasi Kesehatan</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA015" data-type="ta">
                            <td data-label="Nomor">TA015</td>
                            <td data-label="NIM">0920240067</td>
                            <td data-label="Nama">Abdul Ghofur</td>
                            <td data-label="Judul/MK">Robotika Cerdas</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA016" data-type="ta">
                            <td data-label="Nomor">TA016</td>
                            <td data-label="NIM">0920240068</td>
                            <td data-label="Nama">Bella Saphira</td>
                            <td data-label="Judul/MK">Implementasi Blockchain</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA017" data-type="ta">
                            <td data-label="Nomor">TA017</td>
                            <td data-label="NIM">0920240069</td>
                            <td data-label="Nama">Candra Darusman</td>
                            <td data-label="Judul/MK">Data Mining pada Retail</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA018" data-type="ta">
                            <td data-label="Nomor">TA018</td>
                            <td data-label="NIM">0920240070</td>
                            <td data-label="Nama">Diana Prince</td>
                            <td data-label="Judul/MK">Computer Vision untuk Medis</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA019" data-type="ta">
                            <td data-label="Nomor">TA019</td>
                            <td data-label="NIM">0920240071</td>
                            <td data-label="Nama">Farhan Jijima</td>
                            <td data-label="Judul/MK">Pengolahan Bahasa Alami</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA020" data-type="ta">
                            <td data-label="Nomor">TA020</td>
                            <td data-label="NIM">0920240072</td>
                            <td data-label="Nama">Genta Kiswara</td>
                            <td data-label="Judul/MK">Sistem Informasi Geografis</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA021" data-type="ta">
                            <td data-label="Nomor">TA021</td>
                            <td data-label="NIM">0920240073</td>
                            <td data-label="Nama">Hana Malasan</td>
                            <td data-label="Judul/MK">Deep Learning untuk Audio</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA022" data-type="ta">
                            <td data-label="Nomor">TA022</td>
                            <td data-label="NIM">0920240074</td>
                            <td data-label="Nama">Irfan Hakim</td>
                            <td data-label="Judul/MK">Jaringan Syaraf Tiruan</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA023" data-type="ta">
                            <td data-label="Nomor">TA023</td>
                            <td data-label="NIM">0920240075</td>
                            <td data-label="Nama">Jihan Audy</td>
                            <td data-label="Judul/MK">Kriptografi Modern</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA024" data-type="ta">
                            <td data-label="Nomor">TA024</td>
                            <td data-label="NIM">0920240076</td>
                            <td data-label="Nama">Kris Dayanti</td>
                            <td data-label="Judul/MK">Manajemen Proyek IT</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="TA025" data-type="ta">
                            <td data-label="Nomor">TA025</td>
                            <td data-label="NIM">0920240077</td>
                            <td data-label="Nama">Laura Basuki</td>
                            <td data-label="Judul/MK">Sistem Pendukung Keputusan</td>
                            <td data-label="Pembimbing">Dr. Ahmad Khoirul</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM001" data-type="semester">
                            <td data-label="Nomor">SEM001</td>
                            <td data-label="NIM">0920230053</td>
                            <td data-label="Nama">Budi Santoso</td>
                            <td data-label="Judul/MK">Basis Data 1</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM002" data-type="semester">
                            <td data-label="Nomor">SEM002</td>
                            <td data-label="NIM">0920230054</td>
                            <td data-label="Nama">Citra Lestari</td>
                            <td data-label="Judul/MK">Pemrograman 2</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM003" data-type="semester">
                            <td data-label="Nomor">SEM003</td>
                            <td data-label="NIM">0920230055</td>
                            <td data-label="Nama">Fajar Nugroho</td>
                            <td data-label="Judul/MK">Jaringan Komputer</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM004" data-type="semester">
                            <td data-label="Nomor">SEM004</td>
                            <td data-label="NIM">0920230056</td>
                            <td data-label="Nama">Gita Amelia</td>
                            <td data-label="Judul/MK">Sistem Operasi</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM005" data-type="semester">
                            <td data-label="Nomor">SEM005</td>
                            <td data-label="NIM">0920230057</td>
                            <td data-label="Nama">Joko Susilo</td>
                            <td data-label="Judul/MK">Kalkulus Lanjut</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM006" data-type="semester">
                            <td data-label="Nomor">SEM006</td>
                            <td data-label="NIM">0920230058</td>
                            <td data-label="Nama">Kartika Sari</td>
                            <td data-label="Judul/MK">Struktur Data</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM007" data-type="semester">
                            <td data-label="Nomor">SEM007</td>
                            <td data-label="NIM">0920230059</td>
                            <td data-label="Nama">Nadia Putri</td>
                            <td data-label="Judul/MK">Algoritma & Pemrograman</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM008" data-type="semester">
                            <td data-label="Nomor">SEM008</td>
                            <td data-label="NIM">0920230060</td>
                            <td data-label="Nama">Putra Bangsa</td>
                            <td data-label="Judul/MK">Rekayasa Perangkat Lunak</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM009" data-type="semester">
                            <td data-label="Nomor">SEM009</td>
                            <td data-label="NIM">0920230061</td>
                            <td data-label="Nama">Tono Martono</td>
                            <td data-label="Judul/MK">Kecerdasan Buatan</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM010" data-type="semester">
                            <td data-label="Nomor">SEM010</td>
                            <td data-label="NIM">0920230062</td>
                            <td data-label="Nama">Wati Kurnia</td>
                            <td data-label="Judul/MK">Interaksi Manusia & Komputer</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM011" data-type="semester">
                            <td data-label="Nomor">SEM011</td>
                            <td data-label="NIM">0920230063</td>
                            <td data-label="Nama">Xavier Daniels</td>
                            <td data-label="Judul/MK">Teori Bahasa & Automata</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM012" data-type="semester">
                            <td data-label="Nomor">SEM012</td>
                            <td data-label="NIM">0920230064</td>
                            <td data-label="Nama">Yasmine Al-Rashid</td>
                            <td data-label="Judul/MK">Manajemen Basis Data</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM013" data-type="semester">
                            <td data-label="Nomor">SEM013</td>
                            <td data-label="NIM">0920230065</td>
                            <td data-label="Nama">Zainal Abidin</td>
                            <td data-label="Judul/MK">Pemrograman Web Lanjut</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM014" data-type="semester">
                            <td data-label="Nomor">SEM014</td>
                            <td data-label="NIM">0920230066</td>
                            <td data-label="Nama">Alya Rohali</td>
                            <td data-label="Judul/MK">Metodologi Penelitian</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM015" data-type="semester">
                            <td data-label="Nomor">SEM015</td>
                            <td data-label="NIM">0920230067</td>
                            <td data-label="Nama">Ben Kasyafani</td>
                            <td data-label="Judul/MK">Analisis & Desain Sistem</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM016" data-type="semester">
                            <td data-label="Nomor">SEM016</td>
                            <td data-label="NIM">0920230068</td>
                            <td data-label="Nama">Desta Mahendra</td>
                            <td data-label="Judul/MK">Sistem Terdistribusi</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM017" data-type="semester">
                            <td data-label="Nomor">SEM017</td>
                            <td data-label="NIM">0920230069</td>
                            <td data-label="Nama">Enzy Storia</td>
                            <td data-label="Judul/MK">Pemrosesan Sinyal Digital</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM018" data-type="semester">
                            <td data-label="Nomor">SEM018</td>
                            <td data-label="NIM">0920230070</td>
                            <td data-label="Nama">Febby Rastanty</td>
                            <td data-label="Judul/MK">Fisika Dasar</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM019" data-type="semester">
                            <td data-label="Nomor">SEM019</td>
                            <td data-label="NIM">0920230071</td>
                            <td data-label="Nama">Gilang Dirga</td>
                            <td data-label="Judul/MK">Matematika Diskrit</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM020" data-type="semester">
                            <td data-label="Nomor">SEM020</td>
                            <td data-label="NIM">0920230072</td>
                            <td data-label="Nama">Herjunot Ali</td>
                            <td data-label="Judul/MK">Logika Informatika</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM021" data-type="semester">
                            <td data-label="Nomor">SEM021</td>
                            <td data-label="NIM">0920230073</td>
                            <td data-label="Nama">Indra Herlambang</td>
                            <td data-label="Judul/MK">Arsitektur Komputer</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM022" data-type="semester">
                            <td data-label="Nomor">SEM022</td>
                            <td data-label="NIM">0920230074</td>
                            <td data-label="Nama">Jessica Mila</td>
                            <td data-label="Judul/MK">Grafika Komputer</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM023" data-type="semester">
                            <td data-label="Nomor">SEM023</td>
                            <td data-label="NIM">0920230075</td>
                            <td data-label="Nama">Kevin Julio</td>
                            <td data-label="Judul/MK">Proyek Perangkat Lunak</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM024" data-type="semester">
                            <td data-label="Nomor">SEM024</td>
                            <td data-label="NIM">0920230076</td>
                            <td data-label="Nama">Luna Maya</td>
                            <td data-label="Judul/MK">Etika Profesi</td>
                            <td data-label="Pembimbing">Dr. Siti Aisyah</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM025" data-type="semester">
                            <td data-label="Nomor">SEM025</td>
                            <td data-label="NIM">0920230077</td>
                            <td data-label="Nama">Morgan Oey</td>
                            <td data-label="Judul/MK">Kewirausahaan</td>
                            <td data-label="Pembimbing">Prof. Dr. Ir. Benyamin</td>
                            <td data-label="Aksi"><button type="button" class="btn detail-btn"><i class="fa-solid fa-file-signature"></i></button></td>
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
    
     <script src="../../assets/js/aDaftarSidang.js"></script>
</body>

</html>