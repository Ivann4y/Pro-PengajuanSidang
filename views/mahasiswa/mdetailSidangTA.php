<!DOCTYPE html> <!-- ZAFKI ADIPRATAMA PUTRA -->
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sidang</title>

    <!-- Memuat stylesheet Bootstrap untuk styling dasar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat ikon Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Memuat font Poppins dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Memuat ikon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Reset default browser styles dan atur font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        /* Gaya dasar untuk body */
        body {
            min-height: 100vh;
            background-color: #ffffff;
        }

        /* Kontainer utama untuk sidebar dan konten utama */
        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Area logo di sidebar */
        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        /* Gaya gambar logo di sidebar */
        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
        }

        /* Gaya sidebar navigasi */
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

        /* Daftar menu navigasi di sidebar */
        .NavSide__sidebar-nav {
            width: 100%;
            padding-left: 0;
            padding-top: 0;
            list-style: none;
            flex-grow: 1;
        }

        /* Setiap item di menu sidebar */
        .NavSide__sidebar-item {
            position: relative;
            display: block;
            width: 100%;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            margin-bottom: 10px;
        }

        /* Gaya link di item menu sidebar */
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

        /* Warna teks untuk item menu yang tidak aktif */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a {
            color: rgb(252, 252, 252);
        }

        /* Warna teks saat hover untuk item menu yang tidak aktif */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a:hover {
            color: rgb(252, 252, 252);
        }

        /* Gaya judul teks di item menu */
        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
        }

        /* Gaya untuk item menu yang sedang aktif */
        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        /* Warna teks untuk item menu yang aktif */
        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4B68FB;
        }

        /* Elemen untuk efek lengkungan di atas item aktif */
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
        /* Elemen untuk efek lengkungan di bawah item aktif */
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
        /* Menampilkan efek lengkungan saat item menu aktif */
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        /* Gaya topbar (biasanya untuk mobile) */
        .NavSide__topbar {
            display: none;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            margin-left: 280px;
            height: 60px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }
        /* Tombol toggle (menu burger) di topbar */
        .NavSide__topbar .NavSide__toggle {
            width: 40px;
            height: 40px;
            cursor: pointer;
            border-radius: 5px;
            display: none;
            align-items: center;
            justify-content: center;
            padding:0;
        }
        /* Ikon di tombol toggle */
        .NavSide__topbar .NavSide__toggle i.bi {
            font-size: 24px;
            display: none;
            color: #4B68FB;
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
        /* Menampilkan ikon 'list' secara default */
        .NavSide__topbar .NavSide__toggle i.bi.open {
            display: block;
        }
        /* Menyembunyikan ikon 'list' saat tombol toggle aktif */
        .NavSide__topbar .NavSide__toggle.NavSide__toggle--active i.bi.open {
            display: none;
        }
        /* Menampilkan ikon 'x' saat tombol toggle aktif */
        .NavSide__topbar .NavSide__toggle.NavSide__toggle--active i.bi.close {
            display: block;
        }

        /* Gaya untuk konten utama halaman */
       .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm);
            margin-left: 280px;
            margin-right: 40px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            padding-top: calc(20px);
        }

        /* Gaya untuk judul utama H2 */
        .NavSide__main-content h2 {
            margin-bottom: 0.9cm;
            font-weight: 700;
        }
        /* Gaya untuk badge status pengajuan */
        .status-badge {
            margin-bottom: 0.9cm;
            background-color: #FFA3A3;
            color: #464869;
            border-radius: 20px;
            padding: 8px 18px;
            display: inline-block;
            font-size: 0.875rem;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.08);
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        /* Gaya untuk badge status 'Disetujui' */
        .status-badge.approved {
            background-color: #4BFBAF;
            color: black;
        }

        /* Gaya untuk kartu informasi (info-card) */
        .info-card {
            margin-bottom: 1.2cm;
        }

        /* Gaya untuk sub-judul H5 */
        .NavSide__main-content h5 {
            margin-top: 0.9cm;
            margin-bottom: 0.5cm;
            font-weight: 700;
        }

        /* Kontainer untuk tombol-tombol file */
        .file-buttons-container {
            margin-bottom: 1.2cm;
        }

        /* Gaya untuk tombol 'Kembali' */
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
        /* Gaya tombol 'Kembali' saat dihover */
        .btn-kembali:hover {
            position: relative;
            background-color: white;
            color: #4B68FB;
        }

        /* Gaya lingkaran ikon di tombol 'Kembali' */
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

        /* Gaya lingkaran ikon di tombol 'Kembali' saat dihover */
        .btn-kembali:hover .icon-circle {
            background-color: #4B68FB;
        }

        /* Gaya ikon di tombol 'Kembali' */
        .btn-kembali .icon-circle i {
            color: #4B68FB;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        /* Gaya ikon di tombol 'Kembali' saat dihover */
        .btn-kembali:hover .icon-circle i {
            color: white;
        }

        /* Media query untuk layar <= 700px (mobile) */
        @media (max-width: 700px) {
            /* Sidebar di mobile */
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
                z-index: 1040;
                padding-top: 60px;
            }

            /* Sidebar aktif di mobile */
            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            /* Logo sidebar di mobile */
            .NavSide__sidebar-brand {
                padding: 20px 10px 30px 10px;
            }
            .NavSide__sidebar-brand img {
                width: 90%;
            }

            /* Navigasi sidebar di mobile */
            .NavSide__sidebar-nav {
                padding-top: 20%;
            }
            .NavSide__sidebar-item a {
                padding: 12% 10%;
                height: 2vh;
            }

            /* Topbar di mobile */
            .NavSide__topbar {
                display: flex;
                margin-left: 0;
            }
            /* Tombol toggle topbar di mobile */
            .NavSide__topbar .NavSide__toggle {
                display: flex;
                position: static;
                top: auto;
                left: auto;
                background-color: transparent;
                box-shadow: none;
            }
            /* Ikon toggle topbar di mobile */
            .NavSide__topbar .NavSide__toggle i.bi {
                 font-size: 28px;
            }

            /* Konten utama di mobile */
            .NavSide__main-content {
                margin-left: 0;
                padding: 20px;
                padding-top: calc(60px + 20px);
            }

            /* Penyesuaian margin vertikal di mobile */
            .NavSide__main-content h2 {
                margin-bottom: 0.5cm;
            }
            .status-badge {
                margin-bottom: 0.5cm;
            }
            .info-card {
                margin-bottom: 0.5cm;
                flex-direction: column;
                background-color: #4B68FB;
                color: white;
                transition: none;
                box-shadow: none;
            }
            .NavSide__main-content h5 {
                margin-top: 0.5cm;
                margin-bottom: 0.5cm;
            }
            .file-buttons-container {
                margin-bottom: 0.5cm;
            }
            .btn-kembali {
                margin-top: 0.5cm;
            }

            /* Info card di mobile */
            .info-card::after {
                content: none;
            }
            .info-card .section {
                z-index: 1;
                margin-bottom: 1rem;
                transition: none;
            }
            .info-card .section:last-child {
                margin-bottom: 0;
            }
            .info-card .section .label-row i {
                color: white;
                transition: none;
            }
            /* Gaya info card saat hover di mobile (tetap sama karena sudah biru) */
            .info-card:hover {
                background-color: #4B68FB;
                color: white;
            }
            .info-card:hover::after {
                content: none;
            }
            .info-card:hover .section {
                color: white;
            }
            .info-card:hover .section .label-row i {
                color: white;
            }
        }

        /* Gaya umum untuk kartu informasi (info-card) */
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
        }

        /* Efek overlay biru di kanan info-card */
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

        /* Bagian-bagian (section) di dalam info-card (kiri & kanan) */
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

        /* Baris label di dalam info-card (misal: "Judul Sidang") */
        .info-card .section .label-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        /* Ikon di dalam label-row */
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

        /* Teks tebal di label-row */
        .info-card .section .label-row .fw-bold {
            font-weight: 600;
            font-size: 1.05rem;
        }

        /* Baris nilai di dalam info-card */
        .info-card .section .value-row {
            margin-left: 30px;
            line-height: 1.5;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* Gaya untuk tombol file (berkas) */
        .file-button {
            display: inline-flex;
            align-items: center;
            background-color: rgb(235, 238, 245);
            border-radius: 20px;
            padding: 12px 20px;
            margin-right: 15px;
            margin-bottom: 15px;
            text-decoration: none;
            color: #4B68FB;
            font-weight: 500;
            font-size: 1rem;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Gaya tombol file saat dihover */
        .file-button:hover {
            background-color: #4B68FB;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Ikon di tombol file */
        .file-button i {
            font-size: 1.25rem;
            margin-right: 10px;
            color: #4B68FB;
            transition: color 0.2s ease;
        }

        /* Ikon di tombol file saat dihover */
        .file-button:hover i {
            color: white;
        }

        /* Media query untuk tombol file di layar kecil (mobile) */
        @media (max-width: 576px) {
            .file-button {
                flex-basis: 100;
                width: 100%;
                display: flex;
                margin-right: 0;
            }
        }

        /* Grup informasi di dalam info-card */
        .info-card .section .info-group {
            margin-bottom: 1rem;
        }
        /* Grup informasi terakhir, hapus margin bawahnya */
        .info-card .section .info-group:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a onclick="location.href='mdetailSidangTA.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a onclick="location.href='mPerbaikan.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Perbaikan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a onclick="location.href='mNilaiakhir.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Kontainer untuk topbar dan konten utama -->
        <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
            <div class="NavSide__topbar">
                <div class="NavSide__toggle">
                    <i class="bi bi-list open"></i>
                    <i class="bi bi-x-lg close"></i>
                </div>
            </div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
                <div class="status-badge" id="statusBadge">Status Pengajuan : Belum Disetujui</div>

                <div class="info-card">
                    <div class="section">
                        <!-- Detail Judul Sidang -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-book"></i>
                                <span class="fw-bold">Judul Sidang</span>
                            </div>
                            <div class="value-row">Sistem Pengajuan Sidang</div>
                        </div>

                        <!-- Detail Dosen Pembimbing -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user"></i>
                                <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row">
                                Dr. Rida Indah Fariani, S.Kom, M.Kom
                            </div>
                        </div>

                        <!-- Detail Dosen Penguji -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-group"></i>
                                <span class="fw-bold">Dosen Penguji</span>
                            </div>
                            <div class="value-row">
                                Timotius Victory, S.Kom, M.Kom<br>
                                Ning Ratwasturi, S.Kom, M.Kom
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <!-- Detail Ruangan -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-door-open"></i>
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">CB101 - RPL 1B</div>
                        </div>

                        <!-- Detail Tanggal -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">Selasa, 22 April 2025</div>
                        </div>

                        <!-- Detail Jam -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-clock"></i>
                                <span class="fw-bold">Jam</span>
                            </div>
                            <div class="value-row">09.00 - 10.00</div>
                        </div>
                    </div>
                </div>

                <h5>Dokumen Sidang</h5>
                <div class="file-buttons-container d-flex flex-wrap">
                    <a href="#" class="file-button">
                        <i class="fa-solid fa-file-pdf"></i>
                        file_laporan_kel-1.pdf
                    </a>
                    <a href="#" class="file-button">
                        <i class="fa-solid fa-file-zipper"></i>
                        dokumen_pendukung_kel-1.zip
                    </a>
                </div>

                <button class="btn-kembali" onclick="location.href='mSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>

            </main>
        </div>
    </div>

    <!-- Memuat library jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Memuat script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
    // Skrip untuk fungsionalitas sidebar
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // Skrip untuk menandai item menu sidebar yang aktif
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    if (listItems.length > 0) {
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function (event) {
                for (let j = 0; j < listItems.length; j++) {
                    listItems[j].classList.remove("NavSide__sidebar-item--active");
                }
                this.classList.add("NavSide__sidebar-item--active");
            };
        }
    }

    // Skrip untuk mengubah status badge saat diklik
    const statusBadge = document.getElementById('statusBadge');

    if (statusBadge) {
        statusBadge.addEventListener('click', function() {
            if (this.textContent.includes('Belum Disetujui')) {
                this.textContent = 'Status Pengajuan : Disetujui';
                this.classList.add('approved');
            } else {
                this.textContent = 'Status Pengajuan : Belum Disetujui';
                this.classList.remove('approved');
            }
        });
    }
    </script>
</body>
</html>