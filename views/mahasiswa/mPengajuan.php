<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswaa - Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
        .text-heading { 
            font-size: 2.0rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem; 
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
                background-color: transparent;
                box-shadow: none;
                left: 0;
            }

            .NavSide__toggle i.bi.open {
                display: block;
            }

            .NavSide__toggle.NavSide__toggle--active {
                left: calc(50% + 10px); 
                background-color: aliceblue;
            }

            .NavSide__topbar {
                display: flex;
            }
        }
        /* NavSide styles from mBeranda.php - END */


        /* Original mPengajuan table structural styles - START */
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
            /* font-family will be inherited from * or body */
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

        thead th:nth-child(5) {
            width: 5%;
            text-align: center;
        }

        .isiTabel td { /* My explicit background-color and border are removed */
            padding: 12px 15px;
            /* font-family: "Poppins", sans-serif; Inherited */
            font-weight: 400;
            vertical-align: middle;
        }

        .isiTabel td:nth-child(1) {
            border-radius: 20px 0 0 20px;
            text-align: center;
        }

        .isiTabel td:nth-child(5) {
            border-radius: 0 20px 20px 0;
            text-align: center;
        }

        /* Add jadiBiru class styling */
        .jadiBiru {
            background-color: rgb(235, 238, 245);
            transition: all 0.3s ease;
        }

        .jadiBiru:hover {
            background-color: #4B68FB;
            color: white;
            cursor: pointer;
        }

        .action-column {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
            padding-right: 15px;
        }

        .tambah-sidang-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 25px;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 20px;
            background-color: rgb(67, 54, 240); 
            color: white;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .tambah-sidang-btn:hover {
            background-color: rgb(57, 44, 210);
            color: white;
        }

        @media (max-width: 768px) {
            .action-column {
                position: fixed;
                top: 15px;
                right: 60px;
                margin: 0;
                padding: 0;
                z-index: 1046;
            }

            .tambah-sidang-btn {
                padding: 6px 16px;
                font-size: 0.85rem;
                background-color: rgb(67, 54, 240);
                border-radius: 15px;
            }

            .NavSide__topbar {
                padding-right: 140px;
            }
        }

        .btn-primary { 
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

        /* Edit icon styling */
        .bi-pencil-square {
            color: rgb(67, 54, 240);
            transition: color 0.3s ease;
        }

        tr.jadiBiru:hover .bi-pencil-square {
            color: white;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icons a {
            color: #333;
            font-size: 1.2rem;
            text-decoration: none;
        }

        .header-icons a:hover {
            color: rgb(67, 54, 240);
        }

        .header-icons .tambah-sidang-btn {
            padding: 6px 15px;
            font-size: 0.85rem;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .header-icons {
                position: fixed;
                top: 15px;
                right: 60px;
                z-index: 1046;
            }

            .header-icons a {
                color: rgb(67, 54, 240);
            }

            .action-column {
                display: none;
            }      
        }
        .modal-footer .btn-danger {
            background-color: #FD7D7D;
            border-color: #FD7D7D;
        }

        .modal-footer .btn-success {
            background-color: #4FD382;
            border-color: #4FD382;
        }

        h1.page-title {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: black;
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
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
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
                <button class="tambah-sidang-btn d-md-none" onclick="tambahData()">+ Tambah Sidang</button>
                <i class="bi bi-bell-fill"></i>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>  
            </div>
        </div>

        <main class="NavSide__main-content" id="mPengajuan">
            <!-- Top bar desktop -->
      
            <div class="dashboard-header">
                <h2 class="text-heading" style="color:black">Nayaka Ivana Putra (Mahasiswa)</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="mNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>

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
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <div class="action-column">
                                <button class="tambah-sidang-btn" onclick="tambahData()">+ Tambah Sidang</button>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Judul</th>
                                        <th scope="col">Mata Kuliah</th>
                                        <th scope="col">Dosen Pembimbing</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="mSidangTA"></tbody>
                                <tbody id="mSidangSem" style="display: none;"></tbody>
                            </table>
                        </div>
                    </div>
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
                            <div class="modal-footer justify-content-center border-0">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                                <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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

        // Original mPengajuan.php script
        const dataTA = [{
            judul: "Sistem Pengajuan Sidang",
            matkul: "Tugas Akhir",
            dosen: "Rida Indah Fariani"
        }];

        const dataSemester = [{
            judul: "Implementasi Sistem Sidang",
            matkul: "Pemrograman 2",
            dosen: "Timotius Victory"
        }, {
            judul: "Deployment Sistem Sidang",
            matkul: "Sistem Operasi",
            dosen: "Suhendra"
        }];

        function renderTabel(id, data) {
            const tbody = document.getElementById(id);
            tbody.innerHTML = "";
            data.forEach((item, index) => {
                tbody.innerHTML += `
                  <tr class="isiTabel jadiBiru"> 
                    <td>${index + 1}</td>
                    <td>${item.judul}</td>
                    <td>${item.matkul}</td>
                    <td>${item.dosen}</td>
                    <td>
                      <i class="bi bi-pencil-square" style="cursor: pointer;" onclick="editData(${index}, '${id.substring(1)}', '${encodeURIComponent(item.judul)}', '${encodeURIComponent(item.matkul)}')"></i>
                    </td>
                  </tr>
                `;
            });
        }

        function editData(index, jenis, judul, matkul) {
            let jenisParam = jenis === 'SidangTA' ? 'mSidangTA' : 'mSidangSem';
            window.location.href = `mEditPengajuan.php?index=${index}&jenis=${jenisParam}&judul=${judul}&matkul=${matkul}`;
        }

        function tambahData() {
            window.location.href = `mEditPengajuan.php`;
        }

        function switchMSidang() {
            const ta = document.getElementById("mSidangTA");
            const sem = document.getElementById("mSidangSem");
            const btn = document.getElementById("ddMSidang");
            const menu = document.getElementById("ddMSidangMenu");

            if (ta.style.display !== "none") {
                ta.style.display = "none";
                sem.style.display = "";
                btn.innerText = "Sidang Semester";
                menu.innerText = "Sidang TA";
            } else {
                ta.style.display = "";
                sem.style.display = "none";
                btn.innerText = "Sidang TA";
                menu.innerText = "Sidang Semester";
            }
        }

        window.onload = () => {
            renderTabel("mSidangTA", dataTA);
            renderTabel("mSidangSem", dataSemester);
        };
    </script>
</body>

</html>