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

        thead th:nth-child(6) {
            text-align: center;
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

        .isiTabel td:nth-child(6) {
            border-radius: 0 20px 20px 0;
        }


          /* CSS BARU untuk tombol tanpa border */
        .detail-btn {
            border: none !important;
            background-color: transparent !important;
            color: #4B68FB; /* Warna ikon */
            padding: 0.25rem 0.5rem; /* Menyesuaikan padding agar tidak terlalu besar */
        }

        /* Efek saat hover pada tombol */
        .detail-btn:hover {
            opacity: 0.7;
        }

        /* Memastikan warna ikon menjadi putih saat baris di-hover */
        .table-admin-custom tbody tr.isiTabel:hover .detail-btn {
            color: #FFFFFF;
            opacity: 1;
        }

        .modal-header-custom {
            background-color: #4B68FB;
            color: white;
        }

        /* Saat baris di-hover, ubah warna ikon di tombol aksi */
        tr.jadiBiru:hover .detail-btn i {
            color: white !important;
        }

         .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            margin-bottom: 30px;
        }

        .dashboard-header .bodyHeading {
            font-weight: bold;
            font-size: 40px;
            margin: 0;
            color: #1a1a1a;
        }

        .modal-footer .btn-danger {
            background-color: #FD7D7D;
            border-color: #FD7D7D;
        }

        .modal-footer .btn-success {
            background-color: #4FD382;
            border-color: #4FD382;
        }

         .search-input-group {
        background-color: #F3F4F6;
        border-radius: 0.5rem;
        overflow: hidden;
        width: 250px; /* Standardize width as seen in aDaftarSidang.php */
        margin-top: 0.19vh -1px;
        margin-right : 1vh;
    }

    .search-input-group input.form-control {
        background-color: transparent;
        border: none;
        box-shadow: none;
        padding-left: 1rem; /* Adjust padding as search icon is inside span */
    }

    .search-input-group .input-group-text {
        background-color: transparent;
        border: none;
        padding-right: 0; /* No padding on right as input has left padding */
    }

   .pagination-container {
    margin-top: 2rem;
}

.pagination .page-item.active .page-link {
    background-color: #4B68FB;
    border-color: #4B68FB;
    z-index: 2;
    color: white;
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



    </style>
</head>

<body onload="switchDdaftarPengajuan('Semua')">
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
                <a href="dNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon">
                    <a href="dProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>

                    
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="dBeranda">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Pengajuan Sidang</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="dNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="dProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="container-fluid">
                    <div class="row">
                    </div><br><br>
                    <div class="row">
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
                            <div class="search-input-group ms-auto d-flex align-items-center">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari Nama Mahasiswa..." aria-label="Cari">
                            </div>
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
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dPengajuanTA">
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr class="isiTabel jadiBiru">
                                    <td>2</td>
                                    <td>0920240053</td>
                                    <td>Nayaka Ivanna</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240053', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Tugas Akhir</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'TA')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody id="dPengajuanSem" style="display: none;">
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>1</td>
                                    <td>0920240033</td>
                                    <td>M. Harris Nur S.</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240033', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>2</td>
                                    <td>0920240053</td>
                                    <td>Nayaka Ivanna</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240053', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                 <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                 <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                 <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                 <tr class="isiTabel jadiBiru">
                                    <td>3</td>
                                    <td>0920240055</td>
                                    <td>Nur Widya Astuti</td>
                                    <td>Pemrograman 2</td>
                                    <td>Timotius Victory</td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('0920240055', 'Semester')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>   
                                
                            </tbody>
                        </table>
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                            </nav>
                        </div>

                </div>
            </div>

            <!-- Modal keluar-->
            <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
            <script>
                // Untuk search
                 document.addEventListener("DOMContentLoaded", function () {
                    const searchInput = document.querySelector('.search-input-group input');
                    const tbodyTA = document.getElementById("dPengajuanTA");
                    const tbodySem = document.getElementById("dPengajuanSem");
                    const paginationControls = document.getElementById('pagination-controls');
                    const dropdownButton = document.getElementById('ddMSidang');

                    let currentPage = 1;
                    const rowsPerPage = 10;
                    let activeRows = [];

                    function getAllRows() {
                        const rowsTA = Array.from(tbodyTA.querySelectorAll('tr'));
                        const rowsSem = Array.from(tbodySem.querySelectorAll('tr'));

                        if (tbodyTA.style.display !== 'none' && tbodySem.style.display === 'none') {
                            return rowsTA;
                        } else if (tbodySem.style.display !== 'none' && tbodyTA.style.display === 'none') {
                            return rowsSem;
                        } else {
                            return rowsTA.concat(rowsSem);
                        }
                    }

                    function displayPage(rows, page) {
                        const start = (page - 1) * rowsPerPage;
                        const end = start + rowsPerPage;

                        rows.forEach((row, index) => {
                            row.style.display = (index >= start && index < end) ? '' : 'none';
                        });
                    }

                    function setupPagination(rows) {
                        paginationControls.innerHTML = '';
                        const pageCount = Math.ceil(rows.length / rowsPerPage);

                        if (pageCount <= 1) {
                            paginationControls.style.display = 'none';
                            return;
                        }

                        paginationControls.style.display = 'flex';

                        const prevButton = document.createElement('li');
                        prevButton.className = 'page-item';
                        prevButton.innerHTML = '<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
                        prevButton.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (currentPage > 1) {
                                currentPage--;
                                displayPage(rows, currentPage);
                                updatePaginationButtons(pageCount);
                            }
                        });
                        paginationControls.appendChild(prevButton);

                        for (let i = 1; i <= pageCount; i++) {
                            const pageButton = document.createElement('li');
                            pageButton.className = 'page-item';
                            pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                            pageButton.addEventListener('click', (e) => {
                                e.preventDefault();
                                currentPage = i;
                                displayPage(rows, currentPage);
                                updatePaginationButtons(pageCount);
                            });
                            paginationControls.appendChild(pageButton);
                        }

                        const nextButton = document.createElement('li');
                        nextButton.className = 'page-item';
                        nextButton.innerHTML = '<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
                        nextButton.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (currentPage < pageCount) {
                                currentPage++;
                                displayPage(rows, currentPage);
                                updatePaginationButtons(pageCount);
                            }
                        });
                        paginationControls.appendChild(nextButton);

                        updatePaginationButtons(pageCount);
                    }

                    function updatePaginationButtons(pageCount) {
                        const pageItems = paginationControls.querySelectorAll('.page-item');
                        pageItems.forEach((item, index) => {
                            item.classList.remove('active', 'disabled');

                            if (index === 0 && currentPage === 1) {
                                item.classList.add('disabled');
                            } else if (index === pageItems.length - 1 && currentPage === pageCount) {
                                item.classList.add('disabled');
                            } else if (index === currentPage) {
                                item.classList.add('active');
                            }
                        });
                    }

                    function refreshTable() {
                        displayPage(activeRows, currentPage);
                        setupPagination(activeRows);
                    }

                    function searchTable(query) {
                        const allRows = getAllRows();
                        activeRows = [];

                        allRows.forEach(row => {
                            const namaCell = row.children[2];
                            const namaText = namaCell.textContent.toLowerCase();

                            if (namaText.includes(query)) {
                                row.style.display = '';
                                activeRows.push(row);
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        currentPage = 1;
                        refreshTable();
                    }

                    searchInput.addEventListener("keyup", function () {
                        const query = searchInput.value.toLowerCase();
                        searchTable(query);
                    });

                    window.switchDdaftarPengajuan = function (tipe) {
                        if (tipe === 'TA') {
                            tbodyTA.style.display = '';
                            tbodySem.style.display = 'none';
                            dropdownButton.textContent = 'Sidang TA';
                        } else if (tipe === 'Semester') {
                            tbodyTA.style.display = 'none';
                            tbodySem.style.display = '';
                            dropdownButton.textContent = 'Sidang Semester';
                        } else {
                            tbodyTA.style.display = '';
                            tbodySem.style.display = '';
                            dropdownButton.textContent = 'Semua';
                        }

                        searchInput.value = '';
                        activeRows = getAllRows();
                        currentPage = 1;
                        refreshTable();
                    };

                    // Load awal
                    activeRows = getAllRows();
                    refreshTable();
                });

                // Sidebar Toggle Logic
                let menuToggle = document.querySelector(".NavSide__toggle");
                let sidebar = document.getElementById("main-sidebar");

                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            </script>
            <script src="../../assets/js/main.js"></script>
</body>

</html>