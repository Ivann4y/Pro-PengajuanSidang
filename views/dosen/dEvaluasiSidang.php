<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Sidang</title>

    <!-- CSS Frameworks and Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for pop-up notifications -->

    <style>
        /* General Styles */
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

        /* Base Sidebar, Main Content, and Info Card Styles */
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
            align-items: center; /* Vertically center content */
            justify-content: center; /* Horizontally center content */
            width: 100%;
            text-decoration: none;
            color: rgb(252, 252, 252);
            height: 60px; /* Fixed height for consistent sizing */
            box-sizing: border-box;
        }

        /* Ensure white text color for inactive navigation items */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a {
            color: rgb(252, 252, 252);
        }

        /* Ensure white text color on hover for inactive navigation items */
        .NavSide__sidebar-item:not(.NavSide__sidebar-item--active) a:hover {
            color: rgb(252, 252, 252);
        }

        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
        }

        /* Active sidebar item styling */
        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4B68FB !important; /* Apply color to active link text */
        }

        /* Active sidebar item curve effects */
        .NavSide__sidebar-item b:nth-child(1),
        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
        }
        .NavSide__sidebar-item b:nth-child(1) { top: -20px; }
        .NavSide__sidebar-item b:nth-child(2) { bottom: -20px; }

        .NavSide__sidebar-item b:nth-child(1)::before,
        .NavSide__sidebar-item b:nth-child(2)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #4B68FB;
            display: block;
        }
        .NavSide__sidebar-item b:nth-child(1)::before { border-bottom-right-radius: 20px; }
        .NavSide__sidebar-item b:nth-child(2)::before { border-top-right-radius: 20px; }

        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        /* Sidebar toggle button (for mobile view) */
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
        .NavSide__toggle.NavSide__toggle--active i.bi.open { /* Hide menu icon when active */
            opacity: 0;
            visibility: hidden;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.close { /* Show close icon when active */
            opacity: 1;
            visibility: visible;
        }

        /* Main content area wrapper */
        #page-content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            margin-left: 280px; /* Space for sidebar on desktop */
            transition: margin-left 0.5s ease-in-out;
        }

        /* Topbar (visible on mobile) */
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

        /* Main content styling */
        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 0.7cm 20px calc(20px + 0.7cm); /* Adjusted padding */
            margin-right: 0; 
            overflow-y: auto;
            padding-top: 20px;
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

        /* Status badge styling */
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

        /* Info card styling */
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
            margin-right: 0.9cm;
        }

        /* Info card hover effect */
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

        /* Back button styling */
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

        /* Form card styling */
        .form-card {
            background:rgb(235, 238, 245);
            border-radius: 30px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 25px;
            margin-bottom: 1.2cm;
            margin-right: 0.9cm;
        }

        .form-card h4 {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.8cm;
        }

        /* Penilaian input row layout */
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
            width: calc(25% - (20px * 3 / 4)); /* Distribute 4 columns with gap */
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

        /* Custom input field for scores */
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

        /* Custom form group for textareas */
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

        /* Bottom button group styling */
        .button-group-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.2cm;
            margin-right: 0.9cm; 
        }

        /* Submit button styling */
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

        /* Custom modal styling (for confirmation pop-ups) */
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
            margin-bottom: 20px; 
        }

        .modal-body.custom-modal-body {
            text-align: center;
            padding: 0; 
        }

        .btn-tolak,
        .btn-setujui {
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            height: 45px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 25px;
        }

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

        /* Error message styling for validation */
        .error-message {
            color: red;
            font-size: 0.9rem;
            font-weight: 500;
            display: none; /* Hidden by default */
            margin-top: 10px;
            margin-left: 0;
            text-align: left;
        }

        /* --- Media Queries for Responsiveness --- */

        /* Styles for tablets and mobile (max-width: 768px) */
        @media (max-width: 768px) {
            .penilaian-row {
                margin-left: 0 !important;
                margin-right: 0 !important;
                gap: 15px 0;
            }
            .penilaian-row .col-3 {
                width: 100%; /* Make each score input take full width */
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

            .modal-body.custom-modal-body .btn-tolak,
            .modal-body.custom-modal-body .btn-setujui {
                flex-grow: 1;
                flex-shrink: 0;
                flex-basis: auto;
                width: auto;
                height: 40px;
                font-size: 0.9rem;
                padding: 0 10px !important;
                border-radius: 18px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }
        }

        /* Styles for mobile (max-width: 700px) */
        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 280px;
                transform: translateX(-280px); /* Initially hidden off-screen */
                border-left-width: 0;
                z-index: 1040;
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
                height: 50px; /* Adjust height for mobile */
            }

            .NavSide__toggle {
                display: block;
            }
            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px);
            }

            #page-content-wrapper {
                margin-left: 0;
            }

            .NavSide__topbar {
                display: block;
            }

            .NavSide__main-content {
                margin-left: 0;
                padding: 20px; 
                padding-top: calc(60px + 20px);
                margin-right: 0;
            }

            .NavSide__main-content h2 { margin-bottom: 0.5cm; }
            .status-badge { margin-bottom: 0.5cm; }
            .info-card {
                margin-bottom: 0.5cm;
                margin-right: 0; 
                flex-direction: column;
            }
            .info-card .section {
                flex: 0 0 100%;
                margin-bottom: 1rem;
            }
            .info-card .section:last-child {
                margin-bottom: 0;
            }

            .form-card {
                margin-right: 0; 
                padding: 15px;
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
    <!-- Main container for sidebar and content -->
    <div id="NavSide">
        <!-- Sidebar Navigation -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <!-- Sidebar branding area (logo) -->
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <!-- Sidebar navigation menu -->
            <ul class="NavSide__sidebar-nav">
                <!-- 'Evaluasi' menu item (marked as active) -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php">
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                    </a>
                </li>
                <!-- 'Dokumen' menu item -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php">
                        <span class="fw-semibold NavSide__sidebar-title">Dokumen</span>
                    </a>
                </li>
                <!-- 'Nilai Akhir' menu item -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php">
                        <span class="fw-semibold NavSide__sidebar-title">Nilai Akhir</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar toggle button for mobile view -->
        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <!-- Wrapper for responsive topbar and main content area -->
        <div id="page-content-wrapper">
            <!-- Topbar (appears on mobile devices) -->
            <div class="NavSide__topbar"></div>

            <!-- Main content area of the page -->
            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>

                <!-- Info card displaying sidang details -->
                <div class="info-card">
                    <div class="section">
                        <!-- Sidang Title information group -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span class="fw-bold">Judul Sidang</span>
                            </div>
                            <div class="value-row">Struktur Data</div>
                        </div>

                        <!-- Supervising Lecturer information group -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-tie"></i>
                                <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row">Dr. Rida Indah Fariani, S.Si, M.T.I</div>
                        </div>

                        <!-- Examining Lecturers information group -->
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
                        <!-- Room information group -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-door-open"></i>
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">CB101 - RPL 1B</div>
                        </div>

                        <!-- Date information group -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">Selasa, 22 April 2025</div>
                        </div>

                        <!-- Time information group -->
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
                <!-- Form card for score input -->
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
                                placeholder=""/>
                        </div>
                        <div class="col-3 d-flex align-items-center">
                            <label for="materiPresentasi" class="text-black">Materi Presentasi</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="materiPresentasi"
                                placeholder=""/>
                        </div>
                        <div class="col-3 d-flex align-items-center ">
                            <label for="nilaiPenyampaian" class="text-black">Penyampaian</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="nilaiPenyampaian"
                                placeholder=""/>
                        </div>
                        <div class="col-3 d-flex align-items-center ">
                            <label for="nilaiProyek" class="text-black">Nilai Proyek</label>
                            <span class="colon">:</span>
                            <input
                                type="text"
                                class="form-control-custom input-nilai"
                                id="nilaiProyek"
                                placeholder=""/>
                        </div>
                    </div>
                    <!-- Error message for score inputs -->
                    <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                </div>

                <h3>Catatan Evaluasi Sidang</h3>
                <!-- Form card for evaluation notes -->
                <div class="form-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                    </div>
                    <div class="form-group-custom">
                        <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                        <textarea id="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.."></textarea>
                    </div>
                    <!-- Error message for evaluation notes -->
                    <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                </div>

                <!-- Button group at the bottom of the page -->
                <div class="button-group-bottom">
                    <!-- 'Kembali' (Back) button -->
                    <button class="btn btn-kembali" onclick="location.href='dDaftarSidang.php'">
                        <span class="icon-circle">
                            <i class="fa-solid fa-arrow-left"></i>
                        </span>
                        Kembali
                    </button>
                    <!-- 'Kirim' (Submit) button -->
                    <button class="btn-kirim" id="btnKirim">
                        Kirim
                    </button>
                </div>
            </main>
        </div>
    </div>

    <!-- Confirmation Modal for submission -->
    <div class="modal fade" id="confirmationKirimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationKirimModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header custom-modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin hendak mengirimkan evaluasi sidang?</p>
                    <div class="d-flex justify-content-between px-5">
                        <button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-setujui fw-semibold" id="btnKonfirmasiKirim">Kirimkan</button>
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
            // Script for sidebar toggle functionality on mobile
            let menuToggle = document.querySelector(".NavSide__toggle");
            let sidebar = document.getElementById("main-sidebar");

            if (menuToggle && sidebar) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            // Script for setting the active state of sidebar menu items
            let listItems = document.querySelectorAll(".NavSide__sidebar-item");
            if (listItems.length > 0) {
                listItems.forEach(item => {
                    item.addEventListener('click', function (event) { 
                        listItems.forEach(innerItem => {
                            innerItem.classList.remove("NavSide__sidebar-item--active");
                        });
                        this.classList.add("NavSide__sidebar-item--active");
                    });
                });
                // Set initial active state based on current URL path
                const currentPath = window.location.pathname.split('/').pop();
                listItems.forEach(item => {
                    const link = item.querySelector('a');
                    if (link) {
                        const linkHref = link.getAttribute('href'); 
                        if (linkHref && linkHref.toLowerCase().includes(currentPath.toLowerCase())) {
                            item.classList.add("NavSide__sidebar-item--active");
                        }
                    }
                });
            }

            // DOM element variables for form inputs and modals
            const btnKirim = document.getElementById('btnKirim');
            const nilaiLaporan = document.getElementById('nilaiLaporan');
            const materiPresentasi = document.getElementById('materiPresentasi');
            const nilaiPenyampaian = document.getElementById('nilaiPenyampaian');
            const nilaiProyek = document.getElementById('nilaiProyek');
            const catatanEvaluasi = document.getElementById('catatanEvaluasi');

            const nilaiSidangError = document.getElementById('nilaiSidangErrorMessage');
            const catatanEvaluasiError = document.getElementById('catatanEvaluasiErrorMessage');

            const confirmationKirimModalElement = document.getElementById('confirmationKirimModal');
            const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim');

            // Input validation for score fields:
            // 1. Allow only numbers
            // 2. Limit length to 3 characters
            // 3. Limit value to 0-100
            document.querySelectorAll('.input-nilai').forEach(function(input){
                input.addEventListener('input', function() {
                    // 1. Remove non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // 2. Limit length to 3 characters (must be after non-numeric removal)
                    if (this.value.length > 3) {
                        this.value = this.value.slice(0, 3);
                    }
                    
                    // Remove leading zeros if more than one digit (e.g., '05' becomes '5')
                    if (this.value.length > 1 && this.value.startsWith('0')) {
                        this.value = this.value.replace(/^0+/, '');
                    }

                    // 3. Limit maximum value to 100
                    if (parseInt(this.value) > 100) {
                        this.value = '100';
                    }
                });
            });

            // Event listener for the 'Kirim' (Submit) button
            btnKirim.addEventListener('click', function(event) {
                let isValid = true;

                // Hide previous error messages
                nilaiSidangError.style.display = 'none';
                catatanEvaluasiError.style.display = 'none';

                // Validate "Nilai Sidang" fields (check for emptiness)
                if (nilaiLaporan.value.trim() === '' ||
                    materiPresentasi.value.trim() === '' ||
                    nilaiPenyampaian.value.trim() === '' ||
                    nilaiProyek.value.trim() === '') {
                    nilaiSidangError.style.display = 'block';
                    isValid = false;
                }

                // Validate "Catatan Evaluasi Sidang" field (check for emptiness)
                if (catatanEvaluasi.value.trim() === '') {
                    catatanEvaluasiError.style.display = 'block';
                    isValid = false;
                }

                // If any validation fails (empty fields)
                if (!isValid) {
                    event.preventDefault(); // Prevent default form submission
                    Swal.fire({
                        title: 'Harap mengisi kolom nilai dan catatan terlebih dahulu sebelum mengirim!',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4B68FB'
                    });
                } else {
                    // If validation succeeds, show the confirmation modal
                    const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                    confirmationKirimModal.show();
                }
            });

            // Event listener for the 'Kirimkan' (Confirm Submit) button in the modal
            btnKonfirmasiKirim.addEventListener('click', function() {
                // Hide the confirmation modal
                const confirmationKirimModalInstance = bootstrap.Modal.getInstance(confirmationKirimModalElement);
                if (confirmationKirimModalInstance) {
                    confirmationKirimModalInstance.hide();
                }

                // Display success SweetAlert
                Swal.fire({
                    title: 'Evaluasi Sidang Berhasil Dikirim!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                }).then(() => {
                    // Redirect to the daftar sidang page after SweetAlert is closed
                    window.location.href = 'dDaftarSidang.php';
                });
            });
        });
    </script>
</body>
</html>