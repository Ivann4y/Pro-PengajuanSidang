<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <title>Dosen - Pengajuan</title>
    <style>
        
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
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons d-flex d-md-none">
                <a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon">
                    <a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="mBeranda">
            <div class="dashboard-header">
                <h2 class="page-title"> </h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>

    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <h2 class="bodyHeading">
                    Pengajuan Sidang
                </h2>
            </div><br><br>
            <div class="row">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddDPengajuan">
                        Sidang TA
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="ddDSidangMenu" onclick="switchDPengajuan();">Sidang Semester</a></li>
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
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240033', 'TA')">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240053', 'TA')">
                            <td>2</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240055', 'TA')">
                            <td>3</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                    </tbody>
                    <tbody id="dPengajuanSem" style="display: none;">
                    <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240033', 'Semester')">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240053', 'Semester')">
                            <td>2</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240055', 'Semester')">
                            <td>3</td>
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
    
    <!-- Modal keluar-->
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
// Sidebar Toggle Logic
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // Sidebar Active Item Logic
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }
let isTA = true;

function switchDPengajuan() {
    const taTable = document.getElementById('dPengajuanTA');
    const semTable = document.getElementById('dPengajuanSem');
    const dropdownButton = document.getElementById('ddDPengajuan');
    const dropdownMenuItem = document.getElementById('ddDSidangMenu');

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