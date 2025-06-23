<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}
// include '../../koneksi.php';
?>

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
    <link rel="stylesheet" href="../../assets/css/dPengajuan.css">
    <title>Dosen - Pengajuan</title>

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
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn kelompok-btn" style="max-width:300px;" onclick="openKelompokModal()">
                                <i class="bi bi-people-fill me-2"></i>Kelompok
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kelompok</th>
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

                <!-- Modal Kelompok -->
                <div class="modal fade" id="kelompokModal" tabindex="-1" aria-labelledby="kelompokModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered ">
                        <div class="modal-content">
                            <div class="modal-header modal-header-custom">
                                <h5 class="modal-title" id="kelompokModalLabel">Kelompok Mahasiswa</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Tab Navigation -->
                                <div class="modal-tab-container">
                                    <button class="modal-tab active" onclick="switchTab('tambah')">Tambah Kelompok</button>
                                    <button class="modal-tab" onclick="switchTab('daftar')">Daftar Kelompok</button>
                                </div>

                                <!-- Tab Content - Tambah Kelompok -->
                                <div id="tambah-tab" class="modal-tab-content active">
                                    <div class="kelompok-form-container">
                                        <form id="kelompokForm">
                                            <div class="kelompok-form-group">
                                                <label for="kelompok_id">ID Kelompok:</label>
                                                <input type="text" id="kelompok_id" name="kelompok_id" readonly />
                                            </div>
                                            <div class="kelompok-form-group">
                                                <label for="kelompok_prodi">Prodi:</label>
                                                <select id="kelompok_prodi" name="kelompok_prodi" onchange="filterMahasiswaByProdi()">
                                                    <option value="">Pilih Prodi</option>
                                                    <option value="Teknologi Rekayasa Perangkat Lunak">Teknologi Rekayasa Perangkat Lunak</option>
                                                    <option value="Teknologi Rekayasa Komputer">Teknologi Rekayasa Komputer</option>
                                                    <option value="Teknologi Rekayasa Jaringan">Teknologi Rekayasa Jaringan</option>
                                                </select>
                                            </div>
                                            <div class="anggota-wrapper" id="anggota-wrapper">
                                                <div class="anggota-form-group" id="anggota-form-1">
                                                    <label for="anggota_nim_1">Anggota 1:</label>
                                                    <div class="anggota-input-group">
                                                        <div class="input-container">
                                                            <input type="text" id="anggota_nim_1" name="anggota_nim[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" />
                                                            <div class="autocomplete-dropdown" id="autocomplete_1" style="display: none;"></div>
                                                        </div>
                                                        <div class="anggota-nama-display" id="anggota_nama_1">Nama mahasiswa</div>
                                                        <div class="form-toggle-buttons">
                                                            <button type="button" onclick="addAnggota()">+</button>
                                                            <button type="button" onclick="removeAnggota()" style="display: none;">-</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="kelompok-form-actions modal-footer border-0">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                                                <button type="submit" class="btn btn-success">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Tab Content - Daftar Kelompok -->
                                <div id="daftar-tab" class="modal-tab-content">
                                    <div class="kelompok-list-container" id="kelompok-list-container">
                                        <!-- Kelompok list will be populated here -->
                                    </div>
                                    <div class="kelompok-form-actions modal-footer justify-content-center border-0">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    // Untuk search
                    document.addEventListener("DOMContentLoaded", function() {
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

                        searchInput.addEventListener("keyup", function() {
                            const query = searchInput.value.toLowerCase();
                            searchTable(query);
                        });

                        window.switchDdaftarPengajuan = function(tipe) {
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

                    // Kelompok Modal Variables
                    let kelompokModalInstance;
                    let anggotaCount = 1;
                    let currentProdi = '';
                    let mahasiswaData = [];
                    let kelompokData = [];

                    // Sample data for demonstration (replace with actual database queries)
                    const sampleMahasiswaData = [{
                            nim: '0920240033',
                            nama: 'M. Harris Nur S.',
                            prodi: 'Teknologi Rekayasa Perangkat Lunak'
                        },
                        {
                            nim: '0920240053',
                            nama: 'Nayaka Ivanna',
                            prodi: 'Teknologi Rekayasa Perangkat Lunak'
                        },
                        {
                            nim: '0920240055',
                            nama: 'Nur Widya Astuti',
                            prodi: 'Teknologi Rekayasa Perangkat Lunak'
                        },
                        {
                            nim: '0920240060',
                            nama: 'Ahmad Fadillah',
                            prodi: 'Manajemen Informatika'
                        },
                        {
                            nim: '0920240065',
                            nama: 'Siti Nurhaliza',
                            prodi: 'Manajemen Informatika'
                        },
                        {
                            nim: '0920240070',
                            nama: 'Budi Santoso',
                            prodi: 'Teknologi Rekayasa Perangkat Lunak'
                        },
                        {
                            nim: '0920240075',
                            nama: 'Dewi Sartika',
                            prodi: 'Manajemen Informatika'
                        },
                        {
                            nim: '0920240080',
                            nama: 'Rizki Pratama',
                            prodi: 'Manajemen Informatika'
                        }
                    ];

                    const sampleKelompokData = [{
                            id: 'KEL001',
                            prodi: 'Teknologi Rekayasa Perangkat Lunak',
                            anggota: [{
                                    nim: '0920240033',
                                    nama: 'M. Harris Nur S.'
                                },
                                {
                                    nim: '0920240053',
                                    nama: 'Nayaka Ivanna'
                                }
                            ]
                        },
                        {
                            id: 'KEL002',
                            prodi: 'Manajemen Informatika',
                            anggota: [{
                                    nim: '0920240060',
                                    nama: 'Ahmad Fadillah'
                                },
                                {
                                    nim: '0920240075',
                                    nama: 'Dewi Sartika'
                                }
                            ]
                        }
                    ];

                    // Initialize modal and data
                    document.addEventListener('DOMContentLoaded', function() {
                        const kelompokModalEl = document.getElementById('kelompokModal');
                        if (kelompokModalEl) {
                            kelompokModalInstance = new bootstrap.Modal(kelompokModalEl);
                        }

                        // Initialize sample data
                        mahasiswaData = sampleMahasiswaData;
                        kelompokData = sampleKelompokData;

                        // Set up form submission
                        const kelompokForm = document.getElementById('kelompokForm');
                        if (kelompokForm) {
                            kelompokForm.addEventListener('submit', handleKelompokFormSubmit);
                        }

                        // Generate initial ID
                        generateKelompokId();
                    });

                    // Open Kelompok Modal
                    function openKelompokModal() {
                        resetKelompokForm();
                        loadKelompokList();
                        kelompokModalInstance.show();
                    }

                    // Switch between tabs
                    function switchTab(tabName) {
                        // Update tab buttons
                        const tabs = document.querySelectorAll('.modal-tab');
                        tabs.forEach(tab => tab.classList.remove('active'));

                        if (tabName === 'tambah') {
                            tabs[0].classList.add('active');
                        } else {
                            tabs[1].classList.add('active');
                        }

                        // Update tab content
                        const tabContents = document.querySelectorAll('.modal-tab-content');
                        tabContents.forEach(content => content.classList.remove('active'));

                        if (tabName === 'tambah') {
                            document.getElementById('tambah-tab').classList.add('active');
                        } else {
                            document.getElementById('daftar-tab').classList.add('active');
                        }
                    }

                    // Generate auto-increment ID
                    function generateKelompokId() {
                        const nextId = kelompokData.length + 1;
                        const id = `KEL${String(nextId).padStart(3, '0')}`;
                        document.getElementById('kelompok_id').value = id;
                    }

                    // Filter mahasiswa by prodi
                    function filterMahasiswaByProdi() {
                        const prodiSelect = document.getElementById('kelompok_prodi');
                        currentProdi = prodiSelect.value;

                        // Clear existing anggota inputs
                        resetAnggotaInputs();
                    }

                    // Search mahasiswa for autocomplete
                    function searchMahasiswa(input, anggotaIndex) {
                        const query = input.value.toLowerCase().trim();
                        const dropdown = document.getElementById(`autocomplete_${anggotaIndex}`);

                        if (query.length < 1) {
                            dropdown.style.display = 'none';
                            return;
                        }

                        if (!currentProdi) {
                            dropdown.innerHTML = '<div class="autocomplete-item">Pilih Prodi terlebih dahulu</div>';
                            dropdown.style.display = 'block';
                            return;
                        }

                        // Filter mahasiswa by prodi and search query
                        const filteredMahasiswa = mahasiswaData.filter(mhs =>
                            mhs.prodi === currentProdi &&
                            (mhs.nim.toLowerCase().includes(query) || mhs.nama.toLowerCase().includes(query))
                        );

                        if (filteredMahasiswa.length > 0) {
                            dropdown.innerHTML = '';
                            filteredMahasiswa.forEach((mhs, index) => {
                                const item = document.createElement('div');
                                item.className = 'autocomplete-item';
                                item.dataset.nim = mhs.nim;
                                item.dataset.nama = mhs.nama;
                                item.dataset.index = index;
                                item.innerHTML = `
                                <div class="nim">${mhs.nim}</div>
                                <div class="nama">${mhs.nama}</div>
                            `;
                                item.onclick = () => selectMahasiswa(mhs, anggotaIndex);
                                item.onmouseenter = () => highlightItem(item, dropdown);
                                dropdown.appendChild(item);
                            });
                            dropdown.style.display = 'block';
                        } else {
                            dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
                            dropdown.style.display = 'block';
                        }
                    }

                    // Highlight autocomplete item on hover
                    function highlightItem(item, dropdown) {
                        const items = dropdown.querySelectorAll('.autocomplete-item');
                        items.forEach(i => i.classList.remove('selected'));
                        item.classList.add('selected');
                    }

                    // Select mahasiswa from autocomplete
                    function selectMahasiswa(mahasiswa, anggotaIndex) {
                        const nimInput = document.getElementById(`anggota_nim_${anggotaIndex}`);
                        const namaDisplay = document.getElementById(`anggota_nama_${anggotaIndex}`);
                        const dropdown = document.getElementById(`autocomplete_${anggotaIndex}`);

                        nimInput.value = mahasiswa.nim;
                        namaDisplay.textContent = mahasiswa.nama;
                        dropdown.style.display = 'none';
                    }


                    // Add new anggota
                    function addAnggota() {
                        anggotaCount++;
                        const wrapper = document.getElementById('anggota-wrapper');
                        const div = document.createElement('div');
                        div.className = 'anggota-form-group';
                        div.id = `anggota-form-${anggotaCount}`;
                        div.innerHTML = `
                        <label for="anggota_nim_${anggotaCount}">Anggota ${anggotaCount}:</label>
                        <div class="anggota-input-group">
                            <div class="input-container">
                                <input type="text" id="anggota_nim_${anggotaCount}" name="anggota_nim[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, ${anggotaCount})" />
                                <div class="autocomplete-dropdown" id="autocomplete_${anggotaCount}" style="display: none;"></div>
                            </div>
                            <div class="anggota-nama-display" id="anggota_nama_${anggotaCount}">Nama akan muncul otomatis</div>
                            <div class="form-toggle-buttons">
                                <button type="button" onclick="addAnggota()">+</button>
                                <button type="button" onclick="removeAnggota()">-</button>
                            </div>
                        </div>
                    `;
                        wrapper.appendChild(div);
                        updateToggleButtonsVisibility();
                    }

                    // Remove anggota
                    function removeAnggota() {
                        if (anggotaCount > 1) {
                            const lastForm = document.getElementById(`anggota-form-${anggotaCount}`);
                            if (lastForm) {
                                lastForm.remove();
                                anggotaCount--;
                            }
                        }
                        updateToggleButtonsVisibility();
                    }

                    // Update toggle buttons visibility
                    function updateToggleButtonsVisibility() {
                        const toggleButtons = document.querySelectorAll('.form-toggle-buttons');
                        toggleButtons.forEach((btnGroup, index) => {
                            if (index === toggleButtons.length - 1) {
                                btnGroup.style.display = 'inline-flex';
                                const removeBtn = btnGroup.querySelector('button[onclick="removeAnggota()"]');
                                if (removeBtn) {
                                    removeBtn.style.display = (anggotaCount <= 1) ? 'none' : 'block';
                                }
                            } else {
                                btnGroup.style.display = 'none';
                            }
                        });
                    }

                    // Reset anggota inputs
                    function resetAnggotaInputs() {
                        const anggotaInputs = document.querySelectorAll('input[name="anggota_nim[]"]');
                        const namaDisplays = document.querySelectorAll('[id^="anggota_nama_"]');
                        const dropdowns = document.querySelectorAll('[id^="autocomplete_"]');

                        anggotaInputs.forEach(input => input.value = '');
                        namaDisplays.forEach(display => display.textContent = 'Nama akan muncul otomatis');
                        dropdowns.forEach(dropdown => dropdown.style.display = 'none');
                    }

                    // Reset kelompok form
                    function resetKelompokForm() {
                        document.getElementById('kelompokForm').reset();
                        document.getElementById('kelompok_prodi').value = '';
                        anggotaCount = 1;
                        resetAnggotaInputs();
                        generateKelompokId();
                        updateToggleButtonsVisibility();
                    }

                    // Load kelompok list
                    function loadKelompokList() {
                        const container = document.getElementById('kelompok-list-container');
                        container.innerHTML = '';

                        if (kelompokData.length === 0) {
                            container.innerHTML = '<p class="text-center text-muted">Belum ada kelompok yang dibuat.</p>';
                            return;
                        }

                        kelompokData.forEach(kelompok => {
                            const kelompokItem = document.createElement('div');
                            kelompokItem.className = 'kelompok-list-item';
                            kelompokItem.innerHTML = `
                            <div class="kelompok-list-header">
                                <div>
                                    <div class="kelompok-list-title">${kelompok.id}</div>
                                    <div class="kelompok-list-prodi">${kelompok.prodi}</div>
                                </div>
                            </div>
                            <div class="kelompok-list-anggota">
                                <strong>Anggota:</strong><br>
                                ${kelompok.anggota.map(angg => `${angg.nim} - ${angg.nama}`).join('<br>')}
                            </div>
                        `;
                            container.appendChild(kelompokItem);
                        });
                    }

                    // Handle form submission
                    function handleKelompokFormSubmit(event) {
                        event.preventDefault();

                        // Validate form
                        if (!validateKelompokForm()) {
                            return;
                        }

                        // Collect form data
                        const formData = {
                            id: document.getElementById('kelompok_id').value,
                            prodi: document.getElementById('kelompok_prodi').value,
                            anggota: []
                        };

                        // Collect anggota data
                        for (let i = 1; i <= anggotaCount; i++) {
                            const nimInput = document.getElementById(`anggota_nim_${i}`);
                            const namaDisplay = document.getElementById(`anggota_nama_${i}`);

                            if (nimInput.value.trim() !== '') {
                                formData.anggota.push({
                                    nim: nimInput.value.trim(),
                                    nama: namaDisplay.textContent
                                });
                            }
                        }

                        kelompokData.push(formData);

                        // Show success message
                        alert('Kelompok berhasil disimpan!');

                        // Reset form and close modal
                        resetKelompokForm();
                        kelompokModalInstance.hide();

                        // Refresh kelompok list
                        loadKelompokList();
                    }

                    // Validate kelompok form
                    function validateKelompokForm() {
                        const prodi = document.getElementById('kelompok_prodi').value;
                        if (!prodi) {
                            alert('Pilih Prodi terlebih dahulu!');
                            return false;
                        }

                        let hasAnggota = false;
                        for (let i = 1; i <= anggotaCount; i++) {
                            const nimInput = document.getElementById(`anggota_nim_${i}`);
                            if (nimInput.value.trim() !== '') {
                                hasAnggota = true;
                                break;
                            }
                        }

                        if (!hasAnggota) {
                            alert('Minimal harus ada satu anggota!');
                            return false;
                        }

                        return true;
                    }

                    // Close autocomplete dropdowns when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdowns = document.querySelectorAll('.autocomplete-dropdown');
                        dropdowns.forEach(dropdown => {
                            if (!dropdown.contains(event.target) && !event.target.matches('input[name="anggota_nim[]"]')) {
                                dropdown.style.display = 'none';
                            }
                        });
                    });

                    // Add keyboard navigation for autocomplete
                    document.addEventListener('keydown', function(event) {
                        const activeDropdown = document.querySelector('.autocomplete-dropdown[style*="block"]');
                        if (!activeDropdown) return;

                        const items = activeDropdown.querySelectorAll('.autocomplete-item');
                        const selectedItem = activeDropdown.querySelector('.autocomplete-item.selected');
                        let currentIndex = -1;

                        if (selectedItem) {
                            currentIndex = parseInt(selectedItem.dataset.index);
                        }

                        switch (event.key) {
                            case 'ArrowDown':
                                event.preventDefault();
                                if (currentIndex < items.length - 1) {
                                    if (selectedItem) selectedItem.classList.remove('selected');
                                    items[currentIndex + 1].classList.add('selected');
                                }
                                break;
                            case 'ArrowUp':
                                event.preventDefault();
                                if (currentIndex > 0) {
                                    if (selectedItem) selectedItem.classList.remove('selected');
                                    items[currentIndex - 1].classList.add('selected');
                                }
                                break;
                            case 'Enter':
                                event.preventDefault();
                                if (selectedItem) {
                                    const nim = selectedItem.dataset.nim;
                                    const nama = selectedItem.dataset.nama;
                                    const anggotaIndex = activeDropdown.id.split('_')[1];
                                    selectMahasiswa({
                                        nim,
                                        nama
                                    }, anggotaIndex);
                                }
                                break;
                            case 'Escape':
                                activeDropdown.style.display = 'none';
                                break;
                        }
                    });
                </script>
                <script src="../../assets/js/main.js"></script>
</body>

</html>