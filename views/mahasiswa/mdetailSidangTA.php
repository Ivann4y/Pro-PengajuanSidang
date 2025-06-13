<?php //ZAFKI ADIPRATAMA PUTRA 

    // Mengambil parameter dari URL
    $nim = isset($_GET['nim']) ? $_GET['nim'] : 'N/A';
    $tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'N/A';

    $mahasiswa = [];

    if ($tipe === 'TA') {
        $mahasiswa = [
            'nama'        => 'M. Haaris Nur S.',
            'nim'         => '0920240033',
            'mata_kuliah' => 'Tugas Akhir',
        ];
    } elseif ($tipe === 'Semester') {
        $mahasiswa = [
            'nama'         => 'M. Harris Nur S.',
            'nim'          => '0920240033',
            'mata_kuliah'  => 'Pemrograman 2',
            'judul_sidang' => 'Sistem Pengajuan Sidang'
        ];
    } else {
        // Data default jika tipe tidak dikenali
        $mahasiswa = [
            'nama'          => 'Data Tidak Ditemukan',
            'nim'           => 'N/A',
            'mata_kuliah'   => 'N/A',
            'file_laporan'  => '#',
            'file_pendukung' => '#'
        ];
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
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

        /* Memastikan warna teks putih untuk item navigasi yang TIDAK aktif */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a {
            color: rgb(252, 252, 252); 
        }

        /* Memastikan warna teks putih saat hover untuk item navigasi yang TIDAK aktif */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a:hover {
            color: rgb(252, 252, 252);
        }

        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
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

    .NavSide__topbar {
        display: none;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 999;
    }

        @media (max-width: 700px) {
            .NavSide__topbar {
                display: flex; /* Show only on mobile */
                margin-left: 0; /* Reset margin on mobile */
                padding: 0 15px; /* Added padding for consistency */
                justify-content: space-between; /* Added for consistency */
            }
        }

        .NavSide__topbar .NavSide__toggle { /* Styles for toggle INSIDE topbar */
            width: 40px;
            height: 40px;
            cursor: pointer;
            border-radius: 5px;
            /* Removed display: none; for open icon as it's always visible */
            align-items: center;
            justify-content: center;
            padding:0;
            position: static; /* Added from new topbar style */
            top: auto; /* Added from new topbar style */
            left: auto; /* Added from new topbar style */
            background-color: transparent; /* Added from new topbar style */
            box-shadow: none; /* Added from new topbar style */
        }
        .NavSide__topbar .NavSide__toggle i.bi {
            position: static; /* Changed from absolute */
            font-size: 28px; /* Changed from 24px */
            /* Removed display: none; for initial state */
            color: #4B68FB; /* Changed from rgb(67, 54, 240) */
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
        /* Removed rules for .NavSide__topbar .NavSide__toggle i.bi.open as it will always be visible */
        /* Removed rules for .NavSide__topbar .NavSide__toggle i.bi.close as the icon is removed */
        /* Removed .NavSide__topbar .NavSide__toggle.NavSide__toggle--active i.bi.open */
        /* Removed .NavSide__topbar .NavSide__toggle.NavSide__toggle--active i.bi.close */


        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm); 
            margin-left: 280px;
            margin-right: 45px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            /* Adjust padding-top to account for fixed topbar on desktop */
            padding-top: calc(20px); /* 60px topbar height + 20px original padding */
        }

        /* Modifikasi Margin Global */
        .NavSide__main-content h2 { 
            margin-bottom: 0.5cm;
            font-weight: 700; 
        }

        /* Status badge (merah default) */
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

        /* Gaya untuk status "Disetujui" (hijau) */
        .status-badge.approved {
            background-color: #4BFBAF;
            color: black;
        }

        .info-card {
            margin-bottom: 1.2cm; 
        }

        .NavSide__main-content h5 { 
            margin-top: 0.9cm;
            margin-bottom: 0.9cm;
            font-weight: 700; 
        }

        .file-buttons-container {
            margin-bottom: 1.8cm; 
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

        /* Desktop toggle icon */
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


        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
                z-index: 1040; /* Make sure sidebar is above other content but below topbar when active */
                padding-top: 60px; /* Shift sidebar content down to clear topbar */
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            .NavSide__sidebar-brand {
                padding: 20px 10px 30px 10px;
            }
            .NavSide__sidebar-brand img {
                width: 90%;
            }

            .NavSide__sidebar-nav {
                padding-top: 20%;
            }
            .NavSide__sidebar-item a {
                padding: 12% 10%;
                height: 2vh;
            }

            /* Mobile styles for NavSide__topbar (as per mPerbaikan.php) */
            .NavSide__topbar {
                display: flex; /* Show topbar on mobile */
                margin-left: 0; /* No margin-left from sidebar on mobile */
                z-index: 1045; /* Ensure topbar is on top */
                background-color: #ffffff; /* Keep it white */
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Keep shadow on mobile */
            }
            /* Mobile styles for NavSide__toggle (inside topbar) */
            .NavSide__topbar .NavSide__toggle {
                display: flex; /* Show toggle on mobile */
                position: static; /* Remove fixed positioning */
                top: auto;
                left: auto;
                background-color: transparent; /* Remove background */
                box-shadow: none; /* Remove shadow */
            }
            .NavSide__topbar .NavSide__toggle i.bi {
                 font-size: 28px; /* Reset icon size for mobile as per original detailSidang.php */
            }


            .NavSide__main-content {
                margin-left: 0; 
                padding: 20px; /* Adjust padding for mobile to be more consistent */
                margin-right: 0; /* Menghilangkan margin kanan yang tersisa di mobile */
                padding-top: calc(60px + 20px); /* Adjust padding-top for topbar height on mobile */
            }
            
            /* Sesuaikan margin vertikal agar elemen lebih rapat di mobile */
            .NavSide__main-content h2 { 
                margin-bottom: 0.5cm; 
            }
            .status-badge {
                margin-bottom: 0.5cm; 
            }
            .info-card {
                margin-bottom: 0.cm; 
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
            /* Akhir perubahan jarak */

            /* Mobile styles for info-card (dari revisi sebelumnya, dijaga agar tidak berubah) */
            .info-card {
                flex-direction: column; 
                background-color: rgb(235, 238, 245); 
                color: #333; 
                transition: background-color 0.4s ease; 
                box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            }

            .info-card::after {
                content: ""; 
                width: 60px; 
                border-radius: 20px; 
            }
            .info-card .section {
                z-index: 1; 
                margin-bottom: 1rem; 
                transition: color 0.4s ease; 
            }
            .info-card .section:last-child {
                margin-bottom: 0; 
            }
            
            .info-card .section .label-row i {
                color: #495057; 
                transition: color 0.4s ease; 
            }

            .info-card:hover {
                background-color: #4B68FB; 
                color: white; 
            }
            .info-card:hover::after {
                width: 100%; 
                border-radius: 20px; 
            }
            .info-card:hover .section {
                color: white; 
            }
            .info-card:hover .section .label-row i {
                color: white; 
            }
            /* Hide desktop toggle icon on mobile */
            .desktop-toggle-icon {
                display: none;
            }
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

        /* Styling untuk baris label dan nilai */
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
       
        /* Styling Tombol Berkas */
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

        .file-button:hover {
            background-color: #4B68FB;
            color: white; 
            text-decoration: none; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        }

        .file-button i {
            font-size: 1.25rem; 
            margin-right: 10px; 
            color: #4B68FB;
            transition: color 0.2s ease; 
        }

        .file-button:hover i { 
            color: white;
        }

        /* Penyesuaian responsif untuk tombol berkas */
        @media (max-width: 576px) {
            .file-button {
                flex-basis: 100%;
                width: 100%; 
                display: flex; 
                margin-right: 0; 
            }
        }

        /* Styling info-group dan spacer */
        .info-card .section .info-group {
            margin-bottom: 0rem; 
        }
        .info-card .section .info-group:last-child {
            margin-bottom: 0; 
        }
        .info-card .section .spacer {
            flex-grow: 1; 
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
                    <a onclick="location.href='mdetailSidang.php'">
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

        <!-- NEW: Wrap main content in a flex-grow div and add NavSide__topbar -->
        <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
            <!-- Ikon Hamburger Desktop (Hanya untuk Desktop) -->
            <div class="desktop-toggle-icon">
                <i class="bi bi-list"></i>
            </div>
            <div class="NavSide__topbar">
                <div class="NavSide__toggle">
                    <i class="bi bi-list open"></i>
                    <!-- Removed <i class="bi bi-x-lg close"></i> -->
                </div>
            </div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
                <!-- Badge status pengajuan -->
                <div class="status-badge" id="statusBadge">Status Pengajuan : Belum Disetujui</div>
                
                <div class="info-card">
                    <div class="section">
                        <!-- Judul Sidang -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-file-invoice"></i> 
                                <span class="fw-bold">Judul Sidang</span>
                            </div>
                            <div class="value-row">Sistem Pengajuan Sidang</div>
                        </div>

                        <!-- Dosen Pembimbing -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-tie"></i> 
                                <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row">
                                Dr. Rida Indah Fariani, S.Kom, M.Kom
                            </div>
                        </div>

                        <!-- Dosen Penguji -->
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
                        <!-- Ruangan -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-door-open"></i>
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">CB101 - RPL 1B</div>
                        </div>

                        <!-- Tanggal -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">Selasa, 22 April 2025</div>
                        </div>

                        <!-- Jam -->
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
                
                <!-- Modal Penjadwalan Sidang telah dihapus -->

            </main>
        </div>
    </div>
    
    <!-- Modals (successModal and confirmationKirimModal) have been removed -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript">
      // Skrip untuk toggle sidebar dan active menu item
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          // Hanya toggle class untuk sidebar, tidak perlu mengubah ikon karena ikon 'X' sudah dihapus
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
          // Remove the NavSide__toggle--active class as it's no longer needed to hide the burger icon
          // If you want the toggle button itself to change visual state (e.g., background),
          // you might need to keep this toggle, but it won't affect the icon.
          menuToggle.classList.toggle("NavSide__toggle--active");
        };
      }

      let listItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (listItems.length > 0) {
        // Find the active item based on the current URL
        const currentPath = window.location.pathname.split('/').pop();
        listItems.forEach(item => {
            const link = item.querySelector('a');
            if (link) {
                const linkHref = link.getAttribute('onclick');
                // Check if the onclick href matches the current page, case-insensitive
                if (linkHref && linkHref.toLowerCase().includes(currentPath.toLowerCase())) {
                    item.classList.add("NavSide__sidebar-item--active");
                }
            }
        });

        // Add click event listener to update active class
        for (let i = 0; i < listItems.length; i++) {
          listItems[i].onclick = function (event) {
            for (let j = 0; j < listItems.length; j++) {
              listItems[j].classList.remove("NavSide__sidebar-item--active");
            }
            this.classList.add("NavSide__sidebar-item--active");
          };
        }
      }

      // Fungsionalitas: Mengubah status badge saat diklik
      const statusBadge = document.getElementById('statusBadge');

      if (statusBadge) {
          statusBadge.addEventListener('click', function() {
              // Periksa apakah badge saat ini bertuliskan "Belum Disetujui"
              if (this.textContent.includes('Belum Disetujui')) {
                  this.textContent = 'Status Pengajuan : Disetujui';
                  this.classList.add('approved'); // Tambahkan kelas 'approved'
              } else {
                  // Jika bertuliskan "Disetujui", kembalikan ke "Belum Disetujui"
                  this.textContent = 'Status Pengajuan : Belum Disetujui';
                  this.classList.remove('approved'); // Hapus kelas 'approved'
              }
          });
      }

      // Semua fungsi JS terkait form nilai, catatan, dan modal konfirmasi pengiriman telah dihapus.
    </script>
</body>
</html>