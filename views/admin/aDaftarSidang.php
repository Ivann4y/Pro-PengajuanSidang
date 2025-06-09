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
        color: #4B68FB;
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
        background: #4B68FB;
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
    }

    .NavSide__topbar, .main-content-profile-icon, .search-bar-admin {
        display: none;
    }
    
    .main-content-header-title {
        font-size: 2rem;
        font-weight: 700;
        color: #000;
    }
    
    .main-content-subheader {
        justify-content: flex-start;
    }

    .subheader-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }
    
    .table-admin-custom {
        border-spacing: 0 10px;
        border-collapse: separate;
        width: 100%;
    }

    .table-admin-custom thead {
        border-bottom: 2px solid rgb(0, 0, 0) !important;
    }

    .table-admin-custom thead th {
        padding: 12px 15px;
        text-align: left;
    }

    .table-admin-custom tbody tr.isiTabel {
        background-color: #FFFFFF;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-admin-custom tbody tr.isiTabel:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    
    .table-admin-custom .isiTabel td {
        padding: 15px 18px;
        vertical-align: middle;
    }

    .table-admin-custom .isiTabel td:first-child {
        border-radius: 20px 0 0 20px;
    }

    .table-admin-custom .isiTabel td:last-child {
        border-radius: 0 20px 20px 0;
    }

    #ddAdminSidangTypeButton {
        background-color: rgb(67, 54, 240);
        border-color: rgb(67, 54, 240);
    }
    #ddAdminSidangTypeButton:hover {
        background-color: rgb(57, 44, 210);
        border-color: rgb(57, 44, 210);
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
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="logout.html" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        
        <main class="NavSide__main-content" id="adminDaftarSidangContent">
            <div class="main-content-header">
                <h1 class="main-content-header-title">Admin (Daftar Sidang)</h1>
            </div>

            <div class="main-content-subheader">
                 <div class="dropdown" id="filterSidangDropdownContainer">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                        {Teks Akan Diisi JS} 
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="ddAdminSidangTypeButton">
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('TA', this);">Sidang TA</a></li>
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('Semester', this);">Sidang Semester</a></li>
                    </ul>
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
                        <tr class="isiTabel" data-id="TA001" data-type="ta"><td>001</td><td>0920240053</td><td>Nayaka Ivanna</td><td>Sistem Pengajuan Sidang</td><td>Dr. Rida Indah F.</td></tr>
                        <tr class="isiTabel" data-id="TA002" data-type="ta"><td>002</td><td>0920240054</td><td>Zahrah Imelda</td><td>Pengembangan Aplikasi Mobile Edukasi</td><td>Dr. Rida Indah F.</td></tr>
                        <tr class="isiTabel" data-id="TA003" data-type="ta"><td>003</td><td>0920240055</td><td>Nur Widya Astuti</td><td>Analisis Keamanan Jaringan Komputer</td><td>Dr. Rida Indah F.</td></tr>
                    </tbody>
                    <tbody id="adminSidangSem" style="display: none;">
                        <tr class="isiTabel" data-id="SEM001" data-type="semester"><td>S01</td><td>0920230053</td><td>Nayaka Ivanna</td><td>Basis Data 1</td><td>Dr. Rida Indah F</td></tr>
                        <tr class="isiTabel" data-id="SEM002" data-type="semester"><td>S02</td><td>0920230054</td><td>Zahrah Imelda</td><td>Pemrograman 2</td><td>Dr. Rida Indah F</td></tr>
                        <tr class="isiTabel" data-id="SEM003" data-type="semester"><td>S03</td><td>0920240055</td><td>Nur Widya Astuti</td><td>Sistem Operasi</td><td>Dr. Rida Indah F.</td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);">
                    <div class="modal-header"><h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1></div>
                </div>
                <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            } else if (viewType === 'Semester') {
                taTable.style.display = "none"; 
                semTable.style.display = ""; 
                ddButton.innerText = "Sidang Semester";
                dynamicHeader.textContent = "Mata Kuliah"; 
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const initialActiveItem = document.querySelector('#filterSidangDropdownContainer .dropdown-item[onclick*="\'TA\'"]');
            switchAdminSidangView('TA', initialActiveItem); 

            function makeRowsClickable() {
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