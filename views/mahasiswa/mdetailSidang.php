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
            /* Adjust padding to move logo and menu higher */
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

        /* Ensure white text color for INACTIVE navigation items */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a {
            color: rgb(252, 252, 252); 
        }

        /* Ensure white text color on hover for INACTIVE navigation items */
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

        /* Styles for the sidebar toggle button (menu icon for mobile) */
        .NavSide__toggle {
            display: none; /* Hidden by default on desktop */
            position: fixed; 
            top: 15px;
            left: 20px;
            z-index: 1045; 
            cursor: pointer;
            color: rgb(67, 54, 240);
            transition: transform 0.4s ease-in-out;
            width: 28px; 
            height: 28px;
        }

        .NavSide__toggle i.bi {
            font-size: 28px;
            /* Use opacity and visibility for icon fade transition */
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0; 
            visibility: hidden; 
            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        }
        .NavSide__toggle i.bi.open { 
            opacity: 1; 
            visibility: visible;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.open { 
            opacity: 0; 
            visibility: hidden;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.close { 
            opacity: 1; 
            visibility: visible;
        }

        /* Styles for the main content area (wrapper for topbar + main) */
        #page-content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            margin-left: 280px; /* On desktop, make space for the sidebar */
            transition: margin-left 0.5s ease-in-out;
        }

        /* Styles for the full-width topbar (visual only) */
        .NavSide__topbar {
            display: none; /* Hidden on desktop */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 998; 
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm); 
            margin-right: 30px;
            overflow-y: auto;
            /* Top padding on desktop (no fixed topbar here) */
            padding-top: 20px; 
        }

        /* Adjust global margins for content elements */
        .NavSide__main-content h2 { 
            margin-bottom: 0.5cm;
            font-weight: 700; 
        }

        /* Status badge styles (default red) */
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

        /* Styles for "Approved" status (green) */
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
            margin-bottom: 2.5cm; 
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

        @media (max-width: 700px) {
            .NavSide__sidebar {
                transform: translateX(-280px); /* Initially hidden off-screen */
                border-left-width: 0;
                z-index: 1040; 
                /* Reduce top padding to lift logo and menu higher on mobile */
                padding-top: 35px;
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0); /* Show sidebar */
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            .NavSide__sidebar-brand {
               padding: 10% 5% 50% 5%;
            }
            .NavSide__sidebar-brand img {
                width: 90%;
            }

            .NavSide__sidebar-nav {
                padding-top: 3%;
            }
            .NavSide__sidebar-item a {
                padding: 12% 10%;
                height: 2vh;
            }

            /* Mobile styles for NavSide__toggle (visible and moves) */
            .NavSide__toggle {
                display: block; /* Show on mobile */
            }
            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px); /* Toggle moves right when sidebar is open */
            }
            
            /* Adjust main content wrapper for mobile */
            #page-content-wrapper {
                margin-left: 0; /* No margin-left on mobile */
            }

            /* Show fixed topbar on mobile */
            .NavSide__topbar {
                display: block;
            }

            .NavSide__main-content {
                margin-left: 0; 
                padding: 20px; /* General padding for mobile */
                /* Adjust top padding for fixed topbar (60px + 20px content padding) */
                padding-top: calc(60px + 20px); 
            }
            
            /* Adjust vertical margins for tighter elements on mobile */
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

            /* Info-card styles on mobile (hover fix) */
            .info-card {
                flex-direction: column; 
            }
            .info-card .section {
                margin-bottom: 1rem; 
            }
            .info-card .section:last-child {
                margin-bottom: 0; 
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

        /* Styles for label and value rows */
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
           
        /* File Button Styles */
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

        /* Responsive adjustments for file buttons */
        @media (max-width: 576px) {
            .file-button {
                flex-basis: 100%;
                width: 100%; 
                display: flex; 
                margin-right: 0; 
            }
        }

        /* Info-group and spacer styles */
        .info-card .section .info-group {
            margin-bottom: rem; 
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
        <!-- Sidebar -->
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

        <!-- Sidebar toggle button, visible and functional on mobile -->
        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <!-- Wrapper for main page content -->
        <div id="page-content-wrapper">
            <!-- Full-width topbar visible on mobile -->
            <div class="NavSide__topbar"></div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
                <!-- Application status badge -->
                <div class="status-badge" id="statusBadge">Status Pengajuan : Belum Disetujui</div>
                
                <div class="info-card">
                    <div class="section">
                        <!-- Course Title -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-book"></i>
                                <span class="fw-bold">Judul Mata Kuliah</span>
                            </div>
                            <div class="value-row">Pemrograman 2</div>
                        </div>

                        <!-- Spacer to push Lecturer info down -->
                        <div class="spacer"></div>

                        <!-- Lecturers -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-group"></i> 
                                <span class="fw-bold">Dosen Pengampu</span>
                            </div>
                            <div class="value-row">
                                Timotius Victory, S.Kom, M.Kom<br>
                                Yosep Setiawan, S.Kom, M.Kom
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <!-- Room -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-door-open"></i>
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">CB101 - RPL 1B</div>
                        </div>

                        <!-- Date -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">Selasa, 22 April 2025</div>
                        </div>

                        <!-- Time -->
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
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript">
      // Script for sidebar toggle functionality
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          menuToggle.classList.toggle("NavSide__toggle--active");
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
      }

      // Script for marking active menu item
      let menuItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (menuItems.length > 0) {
        menuItems.forEach(item => {
          item.onclick = function (event) {
            menuItems.forEach(innerItem => {
              innerItem.classList.remove("NavSide__sidebar-item--active");
            });
            this.classList.add("NavSide__sidebar-item--active");
          };
        });
      }

      // Functionality: Change status badge on click
      const statusBadge = document.getElementById('statusBadge');

      if (statusBadge) {
          statusBadge.addEventListener('click', function() {
              // Check if the badge currently says "Belum Disetujui"
              if (this.textContent.includes('Belum Disetujui')) {
                  this.textContent = 'Status Pengajuan : Disetujui';
                  this.classList.add('approved'); // Add 'approved' class
              } else {
                  // If it says "Disetujui", revert to "Belum Disetujui"
                  this.textContent = 'Status Pengajuan : Belum Disetujui';
                  this.classList.remove('approved'); // Remove 'approved' class
              }
          });
      }
    </script>
</body>
</html>