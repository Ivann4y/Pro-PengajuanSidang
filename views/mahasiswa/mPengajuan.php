<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswaa - Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

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
            width: auto;
            min-width: 160px;
        }

        .tambah-sidang-btn:hover {
            background-color: rgb(57, 44, 210);
            color: white;
        }

        @media (max-width: 768px) {
            .action-column {
                display: none;
            }

            .mobile-add-button-container {
                display: block;
                margin: 15px 0;
                padding: 0 15px;
            }

            .mobile-add-button-container .tambah-sidang-btn {
                width: fit-content;
            }

            .header-icons .tambah-sidang-btn {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .mobile-add-button-container {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .table-responsive {
                padding: 0 5px;
            }

            table {
                min-width: 700px;
            }

            .isiTabel td {
                font-size: 0.85rem;
                padding: 10px 12px;
            }
        }

        @media (max-width: 375px) {
            table {
                min-width: 650px;
            }

            .isiTabel td {
                font-size: 0.8rem;
                padding: 8px 10px;
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
        .fa-file-signature {
            color: rgb(67, 54, 240);
            transition: color 0.3s ease;
        }

        tr.jadiBiru:hover .fa-file-signature {
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

        .pagination-container {
            margin-top: 2rem;
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .pagination {
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
            margin: 0;
            padding: 0;
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

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0;
                padding: 0 10px;
            }

            .pagination-container {
                position: sticky;
                left: 0;
                right: 0;
                background: white;
                z-index: 1;
                padding: 10px 0;
            }

            .pagination {
                position: relative;
                width: 100%;
                justify-content: center;
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
                margin-bottom: 0px;
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
                <h2 class="text-heading" color:black; >Nayaka Ivana Putra (Mahasiswa)</h2>
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
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                        Semua
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="switchDdaftarPengajuan('Semua')">Semua</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="switchDdaftarPengajuan('TA')">Sidang TA</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="switchDdaftarPengajuan('Semester')">Sidang Semester</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mobile-add-button-container">
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
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar Toggle Logic
    const menuToggle = document.querySelector(".NavSide__toggle");
    const sidebar = document.getElementById("main-sidebar");

    if (menuToggle) {
        menuToggle.onclick = function () {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // Data
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

    // Element references - CORRECTED IDs
    const tbodyTA = document.getElementById("mSidangTA");
    const tbodySem = document.getElementById("mSidangSem");
    const dropdownButton = document.getElementById("ddMSidang");
    // Added a search input for demonstration. You can add this HTML input:
    // <input type="text" class="form-control search-input" placeholder="Cari..." aria-label="Cari">
    const searchInput = document.querySelector('.search-input'); // Corrected selector
    const paginationControls = document.getElementById("pagination-controls");

    let currentData = []; // Initialize with an empty array
    let currentActiveTbody = tbodyTA; // Track which tbody is currently active
    let currentPage = 1;
    const rowsPerPage = 10;

    // Render table
    function renderTable(data, tbody) {
        // Clear both tables before rendering to prevent duplicate entries when switching
        tbodyTA.innerHTML = '';
        tbodySem.innerHTML = '';

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = data.slice(start, end);

        if (tbody) { // Ensure tbody is not null before attempting to modify innerHTML
            pageData.forEach((item, index) => {
                const globalIndex = start + index;
                tbody.innerHTML += `
                    <tr class="isiTabel jadiBiru">
                        <td>${globalIndex + 1}</td>
                        <td>${item.judul}</td>
                        <td>${item.matkul}</td>
                        <td>${item.dosen}</td>
                        <td>
                            <i class="fa-solid fa-file-signature" style="cursor: pointer;" onclick="editData(${globalIndex}, '${tbody.id}', '${item.judul}', '${item.matkul}')"></i>
                        </td>
                    </tr>
                `;
            });
        }
    }

    // Pagination
    function setupPagination() {
        paginationControls.innerHTML = '';
        const pageCount = Math.ceil(currentData.length / rowsPerPage);
        if (pageCount <= 1) return;

        const createBtn = (label, disabled, onClick) => {
            const li = document.createElement("li");
            li.className = `page-item ${disabled ? "disabled" : ""}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            li.onclick = (e) => {
                e.preventDefault();
                if (!disabled) onClick();
            };
            return li;
        };

        paginationControls.appendChild(createBtn("«", currentPage === 1, () => changePage(currentPage - 1)));

        // Display fewer page numbers for mobile responsiveness
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(pageCount, currentPage + 1);

        if (currentPage === 1) {
            endPage = Math.min(pageCount, 3);
        } else if (currentPage === pageCount) {
            startPage = Math.max(1, pageCount - 2);
        }

        for (let i = startPage; i <= endPage; i++) {
            const li = createBtn(i, false, () => changePage(i));
            if (i === currentPage) li.classList.add("active");
            paginationControls.appendChild(li);
        }

        // Add ellipses if there are more pages
        if (endPage < pageCount) {
            if (endPage < pageCount - 1) { // Add ellipses if not just next to the last page
                const li = document.createElement("li");
                li.className = "page-item disabled";
                li.innerHTML = `<a class="page-link" href="#">...</a>`;
                paginationControls.appendChild(li);
            }
            if (endPage < pageCount) { // Always show the last page number
                const lastPageLi = createBtn(pageCount, false, () => changePage(pageCount));
                if (pageCount === currentPage) lastPageLi.classList.add("active");
                paginationControls.appendChild(lastPageLi);
            }
        }

        // Add first page number if not already shown
        if (startPage > 1) {
            if (startPage > 2) { // Add ellipses if not just next to the first page
                const li = document.createElement("li");
                li.className = "page-item disabled";
                li.innerHTML = `<a class="page-link" href="#">...</a>`;
                paginationControls.prepend(li);
            }
            const firstPageLi = createBtn(1, false, () => changePage(1));
            if (1 === currentPage) firstPageLi.classList.add("active");
            paginationControls.prepend(firstPageLi);
        }


        paginationControls.appendChild(createBtn("»", currentPage === pageCount, () => changePage(currentPage + 1)));
    }

    function changePage(page) {
        currentPage = page;
        renderTable(currentData, currentActiveTbody); // Render with the currently active tbody
        setupPagination();
    }

    // Dropdown handler
    window.switchDdaftarPengajuan = function (tipe) {
        if (tipe === 'TA') {
            tbodyTA.style.display = '';
            tbodySem.style.display = 'none';
            dropdownButton.textContent = 'Sidang TA';
            currentData = dataTA;
            currentActiveTbody = tbodyTA; // Set active tbody
        } else if (tipe === 'Semester') {
            tbodyTA.style.display = 'none';
            tbodySem.style.display = '';
            dropdownButton.textContent = 'Sidang Semester';
            currentData = dataSemester;
            currentActiveTbody = tbodySem; // Set active tbody
        } else { // Semua
            tbodyTA.style.display = '';
            tbodySem.style.display = 'none'; // Only one tbody should display content for 'Semua'
            dropdownButton.textContent = 'Semua';
            currentData = dataTA.concat(dataSemester);
            currentActiveTbody = tbodyTA; // Use tbodyTA to display combined data
        }
        currentPage = 1;
        renderTable(currentData, currentActiveTbody); // Render with the newly set active tbody
        setupPagination();
    };

    // Search
    if (searchInput) { // Check if searchInput exists
        searchInput.addEventListener("keyup", function () {
            const query = searchInput.value.toLowerCase();
            const filtered = currentData.filter(item =>
                item.judul.toLowerCase().includes(query) ||
                item.matkul.toLowerCase().includes(query) ||
                item.dosen.toLowerCase().includes(query)
            );
            currentPage = 1;
            renderTable(filtered, currentActiveTbody); // Render filtered data to the active tbody
            setupPagination();
        });
    }

    // Edit dan Tambah
    window.editData = function (index, jenis, judul, matkul) {
        // `jenis` now correctly refers to the `tbody.id` (e.g., "mSidangTA" or "mSidangSem")
        // You might want to adjust how `jenis` is used in mEditPengajuan.php
        window.location.href = `mEditPengajuan.php?index=${index}&jenis=${jenis}&judul=${encodeURIComponent(judul)}&matkul=${encodeURIComponent(matkul)}`;
    };

    window.tambahData = function () {
        window.location.href = 'mEditPengajuan.php';
    };

    // Load awal
    // Initialize currentData and currentActiveTbody correctly at the start
    currentData = dataTA;
    currentActiveTbody = tbodyTA;
    renderTable(currentData, currentActiveTbody);
    setupPagination();
});
</script>

</body>

</html>