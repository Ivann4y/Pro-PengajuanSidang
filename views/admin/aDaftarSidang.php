<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
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
            background: transparent; 
        }

        .NavSide__toggle {
            font-size: 2rem;
            cursor: pointer;
            color: #4B68FB;
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
                display: block;
            }

            .NavSide__toggle {
                position: fixed;
                top: 15px;
                left: 20px;
                z-index: 1001;
                transition: transform 0.4s ease-in-out;
            }

            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px); 
            }
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

        .main-title {
            color: #4B68FB;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
        }

        .sub-title {
            font-weight: 600;
            font-size: 2.1rem;
            margin-bottom: 1rem;
        }

        .header-right-panel {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.75rem;
        }

        .profile-icon {
            font-size: 2.5rem;
            color: #343a40;
        }

        .search-input-group {
            background-color: #F3F4F6;
            border-radius: 0.5rem;
            overflow: hidden;
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

        #ddAdminSidangTypeButton {
            background-color: #4B68FB;
            border-color: #4B68FB;
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
            cursor: pointer;
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
        }

        .table-admin-custom tbody tr.isiTabel:hover {
            background-color: #4B68FB;
            color: #FFFFFF;
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
    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="https://i.ibb.co/c8Qp2Gv/White-Astra.png" alt="AstraTech Logo Admin">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aBeranda.php"><span class="fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aPenjadwalan.php"><span class="fw-semibold">Penjadwalan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="#"><span class="fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
        </div>


        <main class="NavSide__main-content" id="adminDaftarSidangContent">

            <div class="main-header">
                <div class="header-left-panel">
                    <h1 class="main-title">Daftar Sidang</h1>
                    <p class="sub-title">Pengajuan sidang</p>
                    <div class="dropdown" id="switcherDropdownContainer">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Sidang TA
                        </button>
                        <ul class="dropdown-menu" id="dynamicDropdownMenu">
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('Semester');">Sidang Semester</a></li>
                        </ul>
                    </div>
                </div>
                <div class="header-right-panel">
                    <div class="profile-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="input-group search-input-group" style="width: 250px;">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-admin-custom">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col" id="thDynamicHeader">Judul Sidang</th>
                            <th scope="col">Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody id="adminSidangTA">
                        <tr class="isiTabel" data-id="TA001" data-type="ta">
                            <td>001</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Sistem Pengajuan Sidang</td>
                            <td>Dr. Rida Indah F.</td>
                        </tr>
                        <tr class="isiTabel" data-id="TA002" data-type="ta">
                            <td>002</td>
                            <td>0920240054</td>
                            <td>Zahrah Imelda</td>
                            <td>Pengembangan Aplikasi Mobile Edukasi</td>
                            <td>Dr. Rida Indah F.</td>
                        </tr>
                        <tr class="isiTabel" data-id="TA003" data-type="ta">
                            <td>003</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Analisis Keamanan Jaringan Komputer</td>
                            <td>Dr. Rida Indah F.</td>
                        </tr>
                    </tbody>
                    <tbody id="adminSidangSem" style="display: none;">
                        <tr class="isiTabel" data-id="SEM001" data-type="semester">
                            <td>S01</td>
                            <td>0920230053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Basis Data 1</td>
                            <td>Dr. Rida Indah F</td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM002" data-type="semester">
                            <td>S02</td>
                            <td>0920230054</td>
                            <td>Zahrah Imelda</td>
                            <td>Pemrograman 2</td>
                            <td>Dr. Rida Indah F</td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM003" data-type="semester">
                            <td>S03</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Sistem Operasi</td>
                            <td>Dr. Rida Indah F.</td>
                        </tr>
                    </tbody>
                </table>
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
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchAdminSidangView(viewType) {
            const taTable = document.getElementById("adminSidangTA");
            const semTable = document.getElementById("adminSidangSem");
            const dynamicHeader = document.getElementById("thDynamicHeader");
            const dropdownMenu = document.getElementById("dynamicDropdownMenu");
            const ddButton = document.getElementById("ddAdminSidangTypeButton");

            dropdownMenu.innerHTML = '';

            if (viewType === 'TA') {
                taTable.style.display = "";
                semTable.style.display = "none";
                dynamicHeader.textContent = "Judul Sidang";
                ddButton.textContent = "Sidang TA";
                const semesterOption = `<li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('Semester');">Sidang Semester</a></li>`;
                dropdownMenu.insertAdjacentHTML('beforeend', semesterOption);
            } else if (viewType === 'Semester') {
                taTable.style.display = "none";
                semTable.style.display = "";
                dynamicHeader.textContent = "Mata Kuliah";
                ddButton.textContent = "Sidang Semester";
                const taOption = `<li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('TA');">Sidang TA</a></li>`;
                dropdownMenu.insertAdjacentHTML('beforeend', taOption);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika untuk Toggle Sidebar (Mobile View) ---
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");

            if (menuToggle && sidebar) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            // --- Logika untuk Item Sidebar yang Aktif ---
            const listItems = document.querySelectorAll(".NavSide__sidebar-item");
            
            listItems.forEach(item => {
                item.addEventListener('click', function(event) {
                    const link = this.querySelector('a');
                    if (link && !link.hasAttribute('data-bs-toggle')) {
                        listItems.forEach(li => li.classList.remove("NavSide__sidebar-item--active"));
                        this.classList.add("NavSide__sidebar-item--active");

                        if (link.getAttribute('href') && link.getAttribute('href') !== '#') {
                            window.location.href = link.href;
                        }
                    }
                });
            });


            // --- Logika Asli Halaman Anda ---
            switchAdminSidangView('TA');

            const allTableRows = document.querySelectorAll('.table-admin-custom tbody tr');
            allTableRows.forEach(row => {
                row.addEventListener('click', function() {
                    const sidangId = this.dataset.id;
                    const sidangType = this.dataset.type;
                    if (sidangId && sidangType) {
                        if (sidangType === 'ta') {
                            window.location.href = `aDetailSidangTA.php?type=${sidangType}&id=${sidangId}`;
                        } else if (sidangType === 'semester') {
                            window.location.href = `aDetailSidangSem.php?type=${sidangType}&id=${sidangId}`;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>