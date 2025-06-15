<?php
$serverName = "BALTO\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "SistemSidang",
    "TrustServerCertificate" => true,
];

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
// echo "Koneksi Berhasil!<br>";

$query = "SELECT s.id_sidang, s.judul, m.nama_matkul, MIN(d.nama_dosen) AS dosen FROM Sidang s, MataKuliah m, Dosen d, Detail_Sidang ds WHERE ds.id_sidang = s.id_sidang AND ds.id_matkul = m.id_matkul AND ds.nomor_Dsn = d.nomor_dosen GROUP BY s.id_sidang, s.judul, m.nama_matkul ORDER BY s.id_sidang";
$result = sqlsrv_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa - Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            width: 35%;
        }

        thead th:nth-child(3) {
            width: 25%;
        }

        thead th:nth-child(4) {
            width: 25%; 
        }

        thead th:nth-child(5) {
            width: 10%;
            text-align: center;
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

        .isiTabel td:nth-child(5) { 
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

        .modal-footer .btn-danger {
            background-color: #FD7D7D;
            border-color: #FD7D7D;
        }

        .modal-footer .btn-success {
            background-color: #4FD382;
            border-color: #4FD382;
        }
        .detail-btn {
            border: none !important;
            background-color: transparent !important;
            color: #4B68FB; 
            padding: 0.25rem 0.5rem; 
        }
        

        /* Efek saat hover pada tombol */
        .detail-btn:hover {
            opacity: 0.7;
        }

        .table-admin-custom tbody tr.isiTabel:hover .detail-btn {
            color: #FFFFFF;
            opacity: 1;
        }

        tr.jadiBiru:hover .detail-btn i {
            color: white !important;
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
        }

        @media (max-width: 375px) {
            .pagination .page-item:not(:first-child):not(:last-child):not(.active) {
                display: none;
            }
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
                    <div class="dashboard-header">
                    <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
                    <div class="header-icons d-none d-md-flex">
                        <a href="aNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                        <div class="profile-icon">
                            <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                        </div>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 d-flex align-items-center"> 
                        <label for="ddMsidang" class="fw-semibold mb-0">Filter: </label>
                        <div class="dropdown ms-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                Sidang TA
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchMSidang();">Sidang Semester</a></li>
                            </ul>
                        </div>
                    </div>
                </div><br><br>
                <div class="row table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen Pembimbing</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mSidangTA">
                            <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $row['id_sidang'] ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td><a href="mdetailSidangTA.php?id=<?= $row['id_sidang'] ?>" class="bi bi-eye"></a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tbody id="mSidangSem" style="display: none;"></tbody>
                    </table>
                    <div class="pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                        </nav>
                    </div>
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
                <div class="modal-footer justify-content-center border-0">
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

        // Sample data with entries for each type
        // const dataTA = [
        //     { judul: "Sistem Pengajuan Sidang", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
        //     { judul: "Pengembangan Aplikasi Mobile Learning", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
        //     { judul: "Sistem Manajemen Perpustakaan Digital", matkul: "Tugas Akhir", dosen: "Suhendra" },
        //     { judul: "Aplikasi IoT untuk Smart Home", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
        //     { judul: "Sistem Informasi Akademik Terintegrasi", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
        //     { judul: "Platform E-Learning Adaptif", matkul: "Tugas Akhir", dosen: "Suhendra" },
        //     { judul: "Sistem Keamanan Berbasis AI", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
        //     { judul: "Aplikasi Manajemen Proyek Agile", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
        //     { judul: "Sistem Monitoring Kesehatan IoT", matkul: "Tugas Akhir", dosen: "Suhendra" },
        //     { judul: "Platform Social Learning", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
        //     { judul: "Sistem Analisis Data Pendidikan", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
        //     { judul: "Aplikasi AR untuk Pembelajaran", matkul: "Tugas Akhir", dosen: "Suhendra" },
        //     { judul: "Sistem Manajemen Aset Digital", matkul: "Tugas Akhir", dosen: "Rida Indah Fariani" },
        //     { judul: "Platform Kolaborasi Online", matkul: "Tugas Akhir", dosen: "Timotius Victory" },
        //     { judul: "Sistem Otomasi Smart Campus", matkul: "Tugas Akhir", dosen: "Suhendra" }
        // ];

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
                    const detailPage = tbody.id === 'mSidangTA' ? 'mdetailsidangta.php' : 'mdetailsidang.php';
                    tbody.innerHTML += `
                        <tr class="isiTabel jadiBiru">
                            <td>${globalIndex + 1}</td>
                            <td>${item.judul}</td>
                            <td>${item.matkul}</td>
                            <td>${item.dosen}</td>
                            <td style="text-align: center;">
                                <button class="detail-btn" onclick="location.href='${detailPage}';">
                                    <i class="bi bi-eye"></i>
                                </button>
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
    <script src="../../assets/js/main.js"></script>
</body>
</html>