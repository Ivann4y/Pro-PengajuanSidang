<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswa - Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css"> 
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
            background-color: #ffffff;
        }

        /* NavSide styles from mBeranda.php - START */
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
            padding: 20px;
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            background-color: #F9FAFB;
            padding-top: 3vh;
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
        
        .text-heading { 
            font-size: 1.75rem;
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

        .tambah-sidang-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            transition: all 0.2s ease;
            padding: 10px 20px;
            /* font-family: "Poppins", sans-serif; Inherited */
            border-radius: 10px;
            background-color: rgb(67, 54, 240); 
            color: white;
            border: none;
        }

        .tambah-sidang-btn:hover {
            background-color: rgb(57, 44, 210); 
            padding-top: 12px; 
            padding-bottom: 12px;
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
        /* Original mPengajuan table structural styles - END */


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
                    <a href="logout.html"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
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

        <main class="NavSide__main-content" id="mPengajuan">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
                    </div>
                </div><br><br>

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
                </div><br><br>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
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

                <button class="btn btn-primary tambah-sidang-btn" onclick="tambahData()">+ Tambah Sidang</button>
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

        // Sidebar Active Item Logic
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function(event) { 
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
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
                // Restoring .jadiBiru class to the row
                tbody.innerHTML += `
                  <tr class="isiTabel jadiBiru"> 
                    <td>${index + 1}</td>
                    <td>${item.judul}</td>
                    <td>${item.matkul}</td>
                    <td>${item.dosen}</td>
                    <td class="text-center">
                      <button class="btn btn-link p-0" type="button" title="Edit" onclick="editData(${index}, '${id.substring(1)}', '${encodeURIComponent(item.judul)}', '${encodeURIComponent(item.matkul)}')">
                        <i class="bi bi-pencil-square" style="color: rgb(67, 54, 240);"></i>
                      </button>
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