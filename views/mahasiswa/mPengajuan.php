<?php 

 include '../../koneksi/koneksiAndrew.php';
 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswa - Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <link rel="stylesheet" href="../../assets/css/mPengajuan.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

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
                        <h2 class="text-heading" style="color:black;">Nayaka Ivana Putra (Mahasiswa)</h2>
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
                            <tbody id="mSidangTableBody"></tbody> 
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

    // Element references
    const mainTableBody = document.getElementById("mSidangTableBody"); // Menggunakan satu tbody utama
    const dropdownButton = document.getElementById("ddMSidang");
    const paginationControls = document.getElementById("pagination-controls");

    let currentFilteredData = []; // Data yang sedang ditampilkan setelah filter
    let currentPage = 1;
    const rowsPerPage = 10;

    // Render table
    function renderTable(dataToRender) {
        if (!mainTableBody) {
            console.error("Main table body element not found!");
            return;
        }
        mainTableBody.innerHTML = ''; // Bersihkan isi tabel

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = dataToRender.slice(start, end);

        if (pageData.length === 0) {
            mainTableBody.innerHTML = `
                <tr class="isiTabel">
                    <td colspan="5" class="text-center py-4">Tidak ada data untuk ditampilkan.</td>
                </tr>
            `;
        } else {
            pageData.forEach((item, index) => {
                const globalIndex = start + index;
                mainTableBody.innerHTML += `
                    <tr class="isiTabel jadiBiru">
                        <td>${globalIndex + 1}</td>
                        <td>${item.judul}</td>
                        <td>${item.matkul}</td>
                        <td>${item.dosen}</td>
                        <td>
                            <i class="fa-solid fa-file-signature" style="cursor: pointer;" onclick="editData(${globalIndex}, '${item.matkul}', '${item.judul}')"></i>
                        </td>
                    </tr>
                `;
            });
        }
    }

    // Pagination
    function setupPagination() {
        paginationControls.innerHTML = '';
        const pageCount = Math.ceil(currentFilteredData.length / rowsPerPage);
        
        // If there's no data or only one page, hide pagination or show only page 1
        if (currentFilteredData.length === 0) {
            return; // Don't show pagination if no data
        }
        if (pageCount <= 1) {
            const li = document.createElement("li");
            li.className = `page-item active`;
            li.innerHTML = `<a class="page-link" href="#">1</a>`;
            paginationControls.appendChild(li);
            return;
        }

        const createBtn = (label, disabled, onClick, isActive = false) => {
            const li = document.createElement("li");
            li.className = `page-item ${disabled ? "disabled" : ""} ${isActive ? "active" : ""}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            li.onclick = (e) => {
                e.preventDefault();
                if (!disabled) onClick();
            };
            return li;
        };

        // Previous button
        paginationControls.appendChild(createBtn("«", currentPage === 1, () => changePage(currentPage - 1)));

        // Page numbers
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(pageCount, currentPage + 1);

        if (currentPage === 1) {
            endPage = Math.min(pageCount, 3);
        } else if (currentPage === pageCount) {
            startPage = Math.max(1, pageCount - 2);
        }

        if (startPage > 1) {
            paginationControls.appendChild(createBtn(1, false, () => changePage(1), 1 === currentPage));
            if (startPage > 2) {
                const li = document.createElement("li");
                li.className = "page-item disabled";
                li.innerHTML = `<span class="page-link">...</span>`;
                paginationControls.appendChild(li);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationControls.appendChild(createBtn(i, false, () => changePage(i), i === currentPage));
        }

        if (endPage < pageCount) {
            if (endPage < pageCount - 1) {
                const li = document.createElement("li");
                li.className = "page-item disabled";
                li.innerHTML = `<span class="page-link">...</span>`;
                paginationControls.appendChild(li);
            }
            paginationControls.appendChild(createBtn(pageCount, false, () => changePage(pageCount), pageCount === currentPage));
        }

        // Next button
        paginationControls.appendChild(createBtn("»", currentPage === pageCount, () => changePage(currentPage + 1)));
    }

    function changePage(page) {
        currentPage = page;
        renderTable(currentFilteredData);
        setupPagination();
    }

    // Dropdown handler
    window.switchDdaftarPengajuan = function (tipe) {
        if (tipe === 'TA') {
            currentFilteredData = dataTA;
            dropdownButton.textContent = 'Sidang TA';
        } else if (tipe === 'Semester') {
            currentFilteredData = dataSemester;
            dropdownButton.textContent = 'Sidang Semester';
        } else { // Semua
            currentFilteredData = dataTA.concat(dataSemester); // Gabungkan semua data
            dropdownButton.textContent = 'Semua';
        }

        currentPage = 1; // Reset halaman ke 1 setiap kali filter berubah
        renderTable(currentFilteredData);
        setupPagination();
    };

    // Edit dan Tambah
    window.editData = function (index, matkul, judul) {
        // `index` di sini adalah index pada `currentFilteredData` yang sedang ditampilkan.
        // Anda mungkin perlu menyesuaikan `mEditPengajuan.php` untuk menangani ini.
        window.location.href = `mEditPengajuan.php?index=${index}&matkul=${encodeURIComponent(matkul)}&judul=${encodeURIComponent(judul)}`;
    };

    window.tambahData = function () {
        window.location.href = 'mEditPengajuan.php';
    };

    // Initial load: Display "Semua" data
    // Panggil switchDdaftarPengajuan('Semua') untuk inisialisasi tabel dengan semua data
    switchDdaftarPengajuan('Semua');
});
</script>

</body>

</html>