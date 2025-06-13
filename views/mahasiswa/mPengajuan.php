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
            color: #4B68FB;
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

        .isiTabel td {
            padding: 12px 15px;
            font-weight: 400;
            vertical-align: middle;
        }

        .isiTabel td:nth-child(1) {
            border-radius: 20px 0 0 20px;
            -moz-border-radius: 20px 0 0 20px;
            text-align: center;
        }

        .isiTabel td:nth-child(5) {
            border-radius: 0 20px 20px 0;
            -moz-border-radius: 0 20px 20px 0;
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
            -moz-border-radius: 20px;
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
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            .isiTabel {
                margin-bottom: 15px;
                background-color: rgb(235, 238, 245);
                border-radius: 10px;
                -moz-border-radius: 10px;
            }

            .isiTabel td {
                position: relative;
                padding-left: 50% !important;
                text-align: right;
                border: none;
                border-bottom: 1px solid #eee;
            }

            .isiTabel td:last-child {
                border-bottom: none;
                text-align: center;
                padding: 10px !important;
            }

            .isiTabel td:before {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: 600;
            }

            /* Add labels for each td */
            .isiTabel td:nth-of-type(1):before { content: "No"; }
            .isiTabel td:nth-of-type(2):before { content: "Judul"; }
            .isiTabel td:nth-of-type(3):before { content: "Mata Kuliah"; }
            .isiTabel td:nth-of-type(4):before { content: "Dosen Pembimbing"; }
            .isiTabel td:nth-of-type(5):before { content: "Aksi"; }

            /* Reset border radius for mobile */
            .isiTabel td:first-child,
            .isiTabel td:last-child {
                border-radius: 0;
                -moz-border-radius: 0;
            }

            /* Adjust table width */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0;
                padding: 0 10px;
            }

            table {
                min-width: 800px;
                width: 100%;
            }

            /* Hover effect adjustment */
            .jadiBiru:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            /* Adjust font sizes */
            .isiTabel td {
                font-size: 0.9rem;
                padding: 12px 15px;
            }

            .isiTabel td:before {
                font-size: 0.85rem;
            }
        }

        /* Additional mobile optimizations */
        @media (max-width: 576px) {
            .isiTabel td {
                font-size: 0.85rem;
                padding: 10px 15px;
            }

            .isiTabel td:before {
                font-size: 0.8rem;
            }

            .pagination .page-link {
                padding: 0.3rem 0.6rem;
                font-size: 0.9rem;
            }

            .tambah-sidang-btn {
                width: 100%;
                margin-bottom: 15px;
            }
        }

        /* Small mobile devices */
        @media (max-width: 375px) {
            .isiTabel td {
                font-size: 0.8rem;
                padding: 8px 12px;
            }

            .isiTabel td:before {
                font-size: 0.75rem;
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

        /* Pagination styles */
        .pagination-container {
            margin-top: 2rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #4B68FB;
            border-color: #4B68FB;
            color: white;
            z-index: 2;
        }

        .pagination .page-link {
            color: #4B68FB;
        }
        .pagination .page-link:hover {
            color: #2c45c9;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0;
                padding: 0 10px;
            }

            table {
                min-width: 800px;
                width: 100%;
            }

            .pagination-container {
                overflow-x: hidden;
                margin-top: 1rem;
            }
        }

        @media (max-width: 576px) {
            table {
                min-width: 700px;
            }

            .pagination .page-item .page-link {
                padding: 0.35rem 0.65rem;
                min-width: 32px;
                font-size: 0.9rem;
            }

            .tambah-sidang-btn {
                width: 100%;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 375px) {
            .pagination .page-item:not(:first-child):not(:last-child):not(.active) {
                display: none;
            }
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
            
    <div class="container-fluid">
        <div class="row">
            <div class="dashboard-header">
                <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="mNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <div class="row">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <label for="ddMsidang" class="fw-semibold mb-0">Filter:</label>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                    Sidang TA
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchMSidang();">Sidang Semester</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="mobile-add-button-container d-md-none">
                            <button class="tambah-sidang-btn" onclick="tambahData()">+ Tambah Sidang</button>
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
                            <div class="pagination-container">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                                </nav>
                            </div>
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

        // Sample data with 15 entries for each type
        const dataTA = [
            { judul: "Sistem Pengajuan Sidang", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
            { judul: "Pengembangan Aplikasi Mobile Learning", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
            { judul: "Sistem Manajemen Perpustakaan Digital", matkul: "Tugas Akhir", dosen: "Suhendra" },
            { judul: "Aplikasi IoT untuk Smart Home", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
            { judul: "Sistem Informasi Akademik Terintegrasi", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
            { judul: "Platform E-Learning Adaptif", matkul: "Tugas Akhir", dosen: "Suhendra" },
            { judul: "Sistem Keamanan Berbasis AI", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
            { judul: "Aplikasi Manajemen Proyek Agile", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
            { judul: "Sistem Monitoring Kesehatan IoT", matkul: "Tugas Akhir", dosen: "Suhendra" },
            { judul: "Platform Social Learning", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
            { judul: "Sistem Analisis Data Pendidikan", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
            { judul: "Aplikasi AR untuk Pembelajaran", matkul: "Tugas Akhir", dosen: "Suhendra" },
            { judul: "Sistem Manajemen Aset Digital", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
            { judul: "Platform Kolaborasi Online", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
            { judul: "Sistem Otomasi Smart Campus", matkul: "Tugas Akhir", dosen: "Suhendra" }
        ];

        const dataSemester = [
            { judul: "Implementasi Database NoSQL", matkul: "Basis Data Lanjut", dosen: "Timotius Victory" },
            { judul: "Pengembangan Web Service", matkul: "Pemrograman Web", dosen: "Suhendra" },
            { judul: "Analisis Algoritma", matkul: "Struktur Data", dosen: "Rida Indah Fariani" },
            { judul: "Implementasi Machine Learning", matkul: "Kecerdasan Buatan", dosen: "Timotius Victory" },
            { judul: "Arsitektur Microservices", matkul: "Sistem Terdistribusi", dosen: "Suhendra" },
            { judul: "Keamanan Jaringan", matkul: "Jaringan Komputer", dosen: "Rida Indah Fariani" },
            { judul: "Cloud Computing", matkul: "Komputasi Awan", dosen: "Timotius Victory" },
            { judul: "Mobile App Development", matkul: "Pemrograman Mobile", dosen: "Suhendra" },
            { judul: "Data Mining", matkul: "Analisis Data", dosen: "Rida Indah Fariani" },
            { judul: "UI/UX Design", matkul: "Interaksi Manusia Komputer", dosen: "Timotius Victory" },
            { judul: "Software Testing", matkul: "Pengujian Perangkat Lunak", dosen: "Suhendra" },
            { judul: "Computer Vision", matkul: "Pengolahan Citra", dosen: "Rida Indah Fariani" },
            { judul: "Network Programming", matkul: "Pemrograman Jaringan", dosen: "Timotius Victory" },
            { judul: "Embedded Systems", matkul: "Sistem Tertanam", dosen: "Suhendra" },
            { judul: "Big Data Analytics", matkul: "Analisis Big Data", dosen: "Rida Indah Fariani" }
        ];

        // Make these functions global so they can be accessed from HTML
        function editData(index, jenis, judul, matkul) {
            window.location.href = `mEditPengajuan.php?index=${index}&jenis=${jenis}&judul=${encodeURIComponent(judul)}&matkul=${encodeURIComponent(matkul)}`;
        }

        function tambahData() {
            window.location.href = 'mEditPengajuan.php';
        }

        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;
            const rowsPerPage = 10;
            let currentData = dataTA;

            function renderTable(data, tbody) {
                tbody.innerHTML = '';
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedData = data.slice(startIndex, endIndex);

                paginatedData.forEach((item, index) => {
                    const globalIndex = startIndex + index;
                    tbody.innerHTML += `
                        <tr class="isiTabel jadiBiru">
                            <td>${globalIndex + 1}</td>
                            <td>${item.judul}</td>
                            <td>${item.matkul}</td>
                            <td>${item.dosen}</td>
                            <td>
                                <i class="bi bi-pencil-square" style="cursor: pointer;" onclick="editData(${globalIndex}, '${tbody.id.substring(1)}', '${item.judul}', '${item.matkul}')"></i>
                            </td>
                        </tr>
                    `;
                });
            }

            function displayPage(page) {
                currentPage = page;
                const activeTable = document.getElementById('mSidangTA').style.display === 'none' ? 'mSidangSem' : 'mSidangTA';
                renderTable(currentData, document.getElementById(activeTable));
                updatePaginationButtons();
            }

            function setupPagination() {
                const paginationControls = document.getElementById('pagination-controls');
                paginationControls.innerHTML = '';
                const pageCount = Math.ceil(currentData.length / rowsPerPage);
                if (pageCount <= 1) return;

                const prevButton = document.createElement('li');
                prevButton.className = 'page-item';
                prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage > 1) displayPage(currentPage - 1);
                });
                paginationControls.appendChild(prevButton);

                for (let i = 1; i <= pageCount; i++) {
                    const pageButton = document.createElement('li');
                    pageButton.className = 'page-item';
                    pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        displayPage(i);
                    });
                    paginationControls.appendChild(pageButton);
                }

                const nextButton = document.createElement('li');
                nextButton.className = 'page-item';
                nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage < pageCount) displayPage(currentPage + 1);
                });
                paginationControls.appendChild(nextButton);

                updatePaginationButtons();
            }

            function updatePaginationButtons() {
                const pageCount = Math.ceil(currentData.length / rowsPerPage);
                const pageItems = document.querySelectorAll('#pagination-controls .page-item');
                if (pageItems.length === 0) return;

                pageItems.forEach((item, index) => {
                    item.classList.remove('active', 'disabled');
                    if (index === 0) {
                        if (currentPage === 1) item.classList.add('disabled');
                    } else if (index === pageItems.length - 1) {
                        if (currentPage === pageCount) item.classList.add('disabled');
                    } else {
                        if (index === currentPage) {
                            item.classList.add('active');
                        }
                    }
                });
            }

            window.switchMSidang = function() {
                const ta = document.getElementById("mSidangTA");
                const sem = document.getElementById("mSidangSem");
                const btn = document.getElementById("ddMSidang");
                const menu = document.getElementById("ddMSidangMenu");

                if (ta.style.display !== "none") {
                    ta.style.display = "none";
                    sem.style.display = "";
                    btn.innerText = "Sidang Semester";
                    menu.innerText = "Sidang TA";
                    currentData = dataSemester;
                } else {
                    ta.style.display = "";
                    sem.style.display = "none";
                    btn.innerText = "Sidang TA";
                    menu.innerText = "Sidang Semester";
                    currentData = dataTA;
                }
                currentPage = 1;
                setupPagination();
                displayPage(1);
            };

            // Initial setup
            currentData = dataTA;
            setupPagination();
            displayPage(1);
        });
    </script>
</body>

</html>