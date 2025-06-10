<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa - Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        /* Original mSidang table structural styles - START */
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
            /* font-family: "Poppins"; Will be inherited or set by external CSS */
        }

        thead th:nth-child(1) {
            text-align: center;
            width: 5%;
        }

        thead th:nth-child(2) {
            width: 30%;
        }

        thead th:nth-child(3) {
            width: 20%;
        }

        thead th:nth-child(4) {
            width: 20%; 
        }

        .isiTabel td { /* My explicit background-color and border are removed */
            padding: 12px 15px;
            font-family: "Poppins"; /* Restored original font-family from mSidang.php */
            font-weight: 400;
            vertical-align: middle;
        }

        .isiTabel td:nth-child(1) {
            border-radius: 20px 0 0 20px;
            text-align: center;
        }

        .isiTabel td:nth-child(4) { 
            border-radius: 0 20px 20px 0;
        }
        .btn-primary { 
            /* font-family: "Poppins", sans-serif; Inherited */
            background-color: rgb(67, 54, 240);
            border-color: rgb(67, 54, 240);
        }
        .btn-primary:hover {
            background-color: rgb(57, 44, 210);
            border-color: rgb(57, 44, 210);
        }
        .dropdown-menu .dropdown-item {
            font-family: "Poppins", sans-serif;
        }
        /* Original mSidang table structural styles - END */
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
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
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

        <main class="NavSide__main-content" id="mSidang">
            <div class="container-fluid"> 
                <div class="row">
                    <h2 class="text-heading" style="color:black">
                        Nayaka Ivana Putra (Mahasiswa)
                    </h2>
                </div><br>
                <div class="row">
                    <div class="col-12 col-md-6"> 
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                Sidang TA
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchMSidang();">Sidang Semester</a></li>
                            </ul>
                        </div>
                    </div>
                </div><br>
                <div class="row table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody id="mSidangTA">
                            <tr class="isiTabel jadiBiru" onclick="location.href='mdetailsidangta.php';">
                                <td>1</td>
                                <td>Sistem Pengajuan Sidang</td>
                                <td>Tugas Akhir</td>
                                <td>Rida Indah Fariani</td>
                            </tr>
                        </tbody>
                        <tbody id="mSidangSem" style="display: none;">
                            <tr class="isiTabel jadiBiru" onclick="location.href='mdetailsidang.php';">
                                <td>1</td>
                                <td>Implementasi Sistem Sidang</td>
                                <td>Pemrograman 2</td>
                                <td>Timotius Victory</td>
                            </tr>
                            <tr class="isiTabel jadiBiru">
                                <td>2</td>
                                <td>Deployment Sistem Sidang</td>
                                <td>Sistem Operasi</td>
                                <td>Suhendra</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal keluar-->
    <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Logic 
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        if (menuToggle) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        // Sidebar Active Item Logic 
        // let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        // for (let i = 0; i < listItems.length; i++) {
        //     listItems[i].onclick = function(event) {
        //         if (!this.classList.contains("NavSide__sidebar-item--active")) {
        //             for (let j = 0; j < listItems.length; j++) {
        //                 listItems[j].classList.remove("NavSide__sidebar-item--active");
        //             }
        //             this.classList.add("NavSide__sidebar-item--active");
        //         }
        //     };
        // }
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>