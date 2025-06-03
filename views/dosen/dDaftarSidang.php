<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <title>Dosen - Daftar Sidang</title>
    <style>
        
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
            border-left: 5px solid rgb(67, 54, 240);
            background: rgb(67, 54, 240);
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
            margin-bottom: 10px; /* Kept from user's last version */
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

        .NavSide_sidebar-item.NavSide_sidebar-item--active {
            background: #ffffff;
        }

        .NavSide_sidebar-item.NavSide_sidebar-item--active a {
            color: rgb(67, 54, 240);
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
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
            background: rgb(67, 54, 240);
            display: block;
        }
        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            bottom: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
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
            background: rgb(67, 54, 240);
            display: block;
        }
        .NavSide_sidebar-item.NavSide_sidebar-item--active b:nth-child(1),
        .NavSide_sidebar-item.NavSide_sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            background-color: #F9FAFB;
            padding-top: 3vh; /* Default for larger screens */
        }

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
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none; /* Hidden by default for larger screens */
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            display: none;
        }

        .NavSide__toggle i.bi.open {
            color: rgb(67, 54, 240);
        }
        .NavSide__toggle i.bi.close {
            color: rgb(67,54,240);
        }
        .NavSide_toggle.NavSide_toggle--active i.bi.open { display: none; }
        .NavSide_toggle.NavSide_toggle--active i.bi.close { display: block; }

        /* NEW: Top Bar for smaller screens */
        .NavSide__topbar {
            display: none; /* Hidden by default for larger screens */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px; /* Adjust height as needed */
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 999; /* Below sidebar, above main content */
            align-items: center; /* Vertically align items */
            padding: 0 15px; /* Add horizontal padding */
            justify-content: space-between; /* Space between toggle and icons */
        }

        .NavSide__topbar .header-icons {
            display: flex;
            align-items: center;
            /* margin-left: auto; This is handled by justify-content: space-between */
        }

        /* --- STYLES FOR ICONS IN TOPBAR TO MATCH DESKTOP --- */
        .NavSide__topbar .header-icons .bi-bell-fill {
            font-size: 1.5rem; /* Matches desktop size */
            color: #555; /* Matches desktop color */
            margin-right: 1.5rem; /* Space between bell and profile */
            cursor: pointer;
        }
        .NavSide__topbar .profile-icon {
            width: 40px; /* Matches desktop size */
            height: 40px; /* Matches desktop size */
            background-color: #333; /* Matches desktop color */
            border-radius: 50%; /* Matches desktop shape */
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        /* --- END ICON STYLES --- */


        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
            }
            .NavSide_sidebar.NavSide_sidebar--active-mobile {
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
                height: auto;
            }
            .NavSide__main-content {
                margin-left: 0;
                padding: 15px;
                padding-top: 75px; /* Adjust this to create space for the fixed top bar */
            }
            .NavSide__toggle {
                display: flex; /* Show toggle button on small screens */
                /* When inside NavSide__topbar, remove fixed positioning */
                position: relative;
                top: auto; /* Reset */
                background-color: transparent; /* Maintain transparency */
                box-shadow: none; /* Remove shadow if topbar has one */
                left: 0; /* Adjusted for better alignment */
            }
            .NavSide__toggle i.bi.open {
                display: block;
            }
            .NavSide_toggle.NavSide_toggle--active {
                /* This rule targets the toggle's position when the sidebar is open on mobile */
                /* We need to re-evaluate if this specific 'left' adjustment is still needed/correct */
                /* given the toggle is now part of the topbar's flex layout. */
                /* For now, leaving it as is, but it might not have the intended effect or might conflict */
                /* if the toggle is a flex item. */
                left: calc(50% + 10px);
                background-color: aliceblue; /* This might still be needed if you want a background change when active */
            }

            /* Show the top bar on small screens */
            .NavSide__topbar {
                display: flex;
            }
        }

        table {
        border-spacing: 0 10px;
        border-collapse: separate;
        width: 100%;
        }

        thead {
        border-bottom: 2px solid rgb(0, 0, 0) !important;
        }

        thead th {
        padding: 12px 15px;
        text-align: left;
        }

        thead th:nth-child(1) {
        text-align: center;
        width: 5%;
        }

        thead th:nth-child(2) {
        width: 20%;
        }

        thead th:nth-child(3) {
        width: 20%;
        }

        thead th:nth-child(4) {
        width: 20%;
        }

        thead th:nth-child(5) {
        width: 20%;
        }

        .isiTabel td {
        padding: 12px 15px;
        font-family: "Poppins";
        font-weight: 400;
        vertical-align: middle;
        }

        .isiTabel td:nth-child(1) {
        border-radius: 20px 0 0 20px;
        text-align: center;
        }

        .isiTabel td:nth-child(5) {
        border-radius: 0 20px 20px 0;
        }
    

    </style>
</head>
<body>
        
        <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item ">
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="logout.html" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="dPengajuan">
            <div class="dashboard-header">
                <div class="header-icons d-none d-md-flex"> <i class="bi bi-bell-fill"></i>
                    <div class="profile-icon">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                </div>
            </div>

    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <h2 class="bodyHeading">
                    Daftar Sidang
                </h2>
            </div><br><br>
            <div class="row">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                        Sidang TA
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchDdaftarSidang();">Sidang Semester</a></li>
                    </ul>
                </div>
            </div><br><br>
            <div class="row">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Mata Kuliah</th>
                            <th scope="col">Dosen Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody id="dPengajuanTA">
                        <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>2</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>3</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                    </tbody>
                    <tbody id="dPengajuanSem" style="display: none;">
                        <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>2</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- modal keluar -->
    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header mx-auto">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Perhatian!</h1>
        </div>
        <div class="modal-body mx-auto">
             Apakah anda yakin ingin keluar?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
            <button type="button" class="btn btn-success" onclick="window.location.href='../../index.php'">Lanjutkan</button>
        </div>
        </div>
    </div>
    </div>

    <script>
        let isTA = true;

        function switchDdaftarSidang() {
            const taTable = document.getElementById('dPengajuanTA');
            const semTable = document.getElementById('dPengajuanSem');
            const dropdownButton = document.getElementById('ddMSidang');
            const dropdownMenuItem = document.getElementById('ddMSidangMenu');

            if (isTA) {
                taTable.style.display = 'none';
                semTable.style.display = 'table-row-group';
                dropdownButton.textContent = 'Sidang Semester';
                dropdownMenuItem.textContent = 'Sidang TA';
            } else {
                taTable.style.display = 'table-row-group';
                semTable.style.display = 'none';
                dropdownButton.textContent = 'Sidang TA';
                dropdownMenuItem.textContent = 'Sidang Semester';
            }

            isTA = !isTA;
        }
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>