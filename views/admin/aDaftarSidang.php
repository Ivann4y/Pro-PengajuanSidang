<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css"> 
    <link rel="stylesheet" href="../../extra/style.css">
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
        background-color: #F9FAFB;
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

    .NavSide__sidebar-title {
        white-space: normal;
        text-align: center;
        line-height: 1.5;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active {
        background: #ffffff;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active a {
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

    .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
    .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
        display: block;
    }

    .NavSide__main-content {
        flex-grow: 1;
        padding: 25px 30px;
        margin-left: 280px;
        overflow-y: auto;
        transition: margin-left 0.5s ease-in-out;
        background-color: #F9FAFB;
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
        display: none;
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
        color: rgb(67, 54, 240);
    }

    .NavSide__toggle.NavSide__toggle--active i.bi.open {
        display: none;
    }

    .NavSide__toggle.NavSide__toggle--active i.bi.close {
        display: block;
    }

    .NavSide__topbar {
        display: none;
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

    .NavSide__topbar .header-icons {
        display: flex;
        align-items: center;
    }

    .NavSide__topbar .header-icons .bi-bell-fill {
        font-size: 1.5rem;
        color: #555;
        margin-right: 1.5rem;
        cursor: pointer;
    }

    .NavSide__topbar .profile-icon {
        width: 40px;
        height: 40px;
        background-color: #333;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    @media (max-width: 700px) {
        .NavSide__sidebar {
            width: 50%;
            transform: translateX(-100%);
            border-left-width: 0;
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
            height: auto;
        }

        .NavSide__main-content {
            margin-left: 0;
            padding: 15px;
            padding-top: 75px;
        }

        .NavSide__toggle {
            display: flex;
            position: relative;
            top: auto;
            left: auto;
            background-color: transparent;
            box-shadow: none;
        }

        .NavSide__toggle i.bi.open {
            display: block;
        }

        .NavSide__topbar {
            display: flex;
        }

        .main-content-header-title {
            font-size: 1.8rem;
        }

        .main-content-profile-icon {
            font-size: 2rem;
            width: 40px;
            height: 40px;
        }

        .search-bar-admin input {
            width: 100%;
        }

        #ddAdminSidangTypeButton {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
    }

    .main-content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .main-content-header-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #4336F0;
    }

    .main-content-profile-icon {
        font-size: 2.5rem;
        color: #333;
        background-color: #E0E0E0;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .main-content-profile-icon i {
        color: #555;
    }

    .main-content-subheader {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .subheader-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }

    .search-bar-admin {
        position: relative;
    }

    .search-bar-admin input {
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        padding: 8px 15px 8px 35px;
        font-size: 0.9rem;
        width: 250px;
        background-color: #F5F5F5;
    }

    .search-bar-admin .bi-search {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #757575;
    }

    #filterSidangDropdownContainer {
        margin-bottom: 25px;
    }

    #ddAdminSidangTypeButton {
        background-color: #4336F0;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.4rem 1rem;
        font-weight: 500;
        font-size: 0.9rem;
    }

    #ddAdminSidangTypeButton:hover,
    #ddAdminSidangTypeButton:focus {
        background-color: #3a2eb8;
        color: white;
    }

    .dropdown-menu .dropdown-item.active,
    .dropdown-menu .dropdown-item:active {
        background-color: #4336F0;
        color: white;
    }

    .dropdown-menu .dropdown-item {
        font-size: 0.9rem;
        font-weight: 500;
    }

    .table-admin-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .table-admin-custom thead th {
        padding: 12px 18px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #E0E0E0;
        font-size: 0.9rem;
    }

    .table-admin-custom thead th:nth-child(1) {
        width: 8%;
    }

    .table-admin-custom thead th:nth-child(2) {
        width: 15%;
    }

    .table-admin-custom thead th:nth-child(3) {
        width: 22%;
    }

    .table-admin-custom thead th#thDynamicHeader {
        width: 35%;
    }

    .table-admin-custom thead th:nth-child(5) {
        width: 20%;
    }

    .table-admin-custom tbody tr {
        background-color: #FFFFFF;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s ease-in-out, background-color 0.3s ease, color 0.3s ease;
        cursor: pointer;
    }

    .table-admin-custom tbody tr:hover {
        transform: translateY(-2px);
        background-color: #4336F0;
    }

    .table-admin-custom tbody tr:hover td {
        color: white;
    }

    .table-admin-custom tbody td {
        padding: 15px 18px;
        font-size: 0.9rem;
        color: #555;
        vertical-align: middle;
        transition: color 0.3s ease;
    }

    .table-admin-custom tbody td:first-child {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    .table-admin-custom tbody td:last-child {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
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
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aPenjadwalan.php"><span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="logout.html" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons"><i class="bi bi-bell-fill"></i><div class="profile-icon"><i class="bi bi-person-gear fs-5"></i></div></div>
        </div>

        <main class="NavSide__main-content" id="adminDaftarSidangContent">
            <div class="main-content-header">
                <h1 class="main-content-header-title">Daftar Sidang</h1>
                <div class="main-content-profile-icon"><i class="bi bi-person-fill"></i></div>
            </div>

            <div class="main-content-subheader">
                <h2 class="subheader-title">Pengajuan Sidang</h2>
                <div class="search-bar-admin">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari mahasiswa...">
                </div>
            </div>
            
            <div class="dropdown" id="filterSidangDropdownContainer">
                <button class="btn dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                    {Teks Akan Diisi JS} 
                </button>
                <ul class="dropdown-menu" aria-labelledby="ddAdminSidangTypeButton">
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('TA', this);">Sidang TA</a></li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('Semester', this);">Sidang Semester</a></li>
                </ul>
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
                        <tr data-id="TA001" data-type="ta"><td>001</td><td>0920240053</td><td>Nayaka Ivanna</td><td>Sistem Pengajuan Sidang</td><td>Dr. Rida Indah F.</td></tr>
                        <tr data-id="TA002" data-type="ta"><td>002</td><td>0920240054</td><td>Zahrah Imelda</td><td>Pengembangan Aplikasi Mobile Edukasi</td><td>Dr. Rida Indah F.</td></tr>
                        <tr data-id="TA003" data-type="ta"><td>003</td><td>0920240055</td><td>Nur Widya Astuti</td><td>Analisis Keamanan Jaringan Komputer</td><td>Dr. Rida Indah F.</td></tr>
                    </tbody>
                    <tbody id="adminSidangSem" style="display: none;">
                        <tr data-id="SEM001" data-type="semester"><td>S01</td><td>0920230053</td><td>Nayaka Ivanna</td><td>Basis Data 1</td><td>Dr. Rida Indah F</td></tr>
                        <tr data-id="SEM002" data-type="semester"><td>S02</td><td>0920230054</td><td>Zahrah Imelda</td><td>Pemrograman 2</td><td>Dr. Rida Indah F</td></tr>
                        <tr data-id="SEM003" data-type="semester"><td>S03</td><td>0920240055</td><td>Nur Widya Astuti</td><td>Sistem Operasi</td><td>Dr. Rida Indah F.</td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header mx-auto"> <h1 class="modal-title fs-5" id="exampleModalLabel">Perhatian!</h1>
                </div>
            <div class="modal-body text-center"> Apakah anda yakin ingin keluar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
            </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");
        if (menuToggle) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function(event) {
                const linkInItem = this.querySelector('a');
                if (linkInItem && linkInItem.getAttribute('data-bs-toggle') === 'modal' && linkInItem.getAttribute('data-bs-target') === '#logout') {
                    return; 
                }

                if (this.classList.contains("NavSide__sidebar-item--active") && this.querySelector('a[href="#.php"]')) {
                    return;
                }

                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }

        function switchAdminSidangView(viewType, clickedElement) {
            const taTable = document.getElementById("adminSidangTA");
            const semTable = document.getElementById("adminSidangSem");
            const ddButton = document.getElementById("ddAdminSidangTypeButton");
            const dynamicHeader = document.getElementById("thDynamicHeader"); 

            const dropdownItems = document.querySelectorAll('#filterSidangDropdownContainer .dropdown-item');
            dropdownItems.forEach(item => item.classList.remove('active'));

            if (clickedElement) {
                clickedElement.classList.add('active');
            }

            if (viewType === 'TA') {
                taTable.style.display = ""; 
                semTable.style.display = "none"; 
                ddButton.innerText = "Sidang TA";
                dynamicHeader.textContent = "Judul Sidang"; 
                if (!clickedElement) { 
                    const taMenuItem = document.querySelector('#filterSidangDropdownContainer .dropdown-item[onclick*="\'TA\'"]');
                    if (taMenuItem) taMenuItem.classList.add('active');
                }
            } else if (viewType === 'Semester') {
                taTable.style.display = "none"; 
                semTable.style.display = ""; 
                ddButton.innerText = "Sidang Semester";
                dynamicHeader.textContent = "Prodi"; 
                if (!clickedElement) { 
                    const semesterMenuItem = document.querySelector('#filterSidangDropdownContainer .dropdown-item[onclick*="\'Semester\'"]');
                    if (semesterMenuItem) semesterMenuItem.classList.add('active');
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const initialActiveItem = document.querySelector('#filterSidangDropdownContainer .dropdown-item[onclick*="\'TA\'"]');
            switchAdminSidangView('TA', initialActiveItem); 

            function makeRowsClickable() {
                const tableRowsTA = document.querySelectorAll('#adminSidangTA tr');
                const tableRowsSemester = document.querySelectorAll('#adminSidangSem tr');
                const allTableRows = [...tableRowsTA, ...tableRowsSemester];

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
                        } else {
                            console.error('Data ID atau Tipe Sidang tidak ditemukan pada baris:', this);
                        }
                    });
                });
            }
            makeRowsClickable(); 
        });
    </script>
</body>
</html>