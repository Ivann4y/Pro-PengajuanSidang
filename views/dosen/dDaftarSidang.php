<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <title>Dosen - Daftar Sidang</title>
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
            width: 15%;
        }
        .isiTabel td {
            padding: 12px 15px;
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            vertical-align: middle;
        }
        tr.isiTabel {
            background-color: #F5F5F5;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        tr.isiTabel:hover {
            background-color: #4B68FB;
            color: white;
        }
        .isiTabel td:nth-child(1) {
            border-radius: 20px 0 0 20px;
            text-align: center;
        }
        .isiTabel td:nth-child(6) {
            border-radius: 0 20px 20px 0;
            text-align: center;
        }
        .detail-btn {
            border: none !important;
            background-color: transparent !important;
            color: #4B68FB; 
            padding: 0.25rem 0.5rem; 
        }
        .detail-btn:hover {
            opacity: 0.7;
        }
        tr.isiTabel:hover {
            background-color: #4B68FB;
            color: white;
        }
        tr.isiTabel:hover .detail-btn {
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
            font-family: "Poppins", sans-serif;
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
            width: 250px;
        }
        .search-input-group input.form-control {
            background-color: transparent;
            border: none;
            box-shadow: none;
            padding-left: 1rem;
        }
        .search-input-group .input-group-text {
            background-color: transparent;
            border: none;
            padding-right: 0;
        }
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
    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons d-flex d-md-none">
                <a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;"><i class="bi bi-bell-fill"></i></a>
                <div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;"><i class="bi bi-person-fill fs-5"></i></a></div>
            </div>
        </div>
        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Daftar Sidang</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon"><a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="d-flex align-items-center gap-2">
                        <label class="fw-semibold mb-0">Filter:</label>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddDosenSidang">Semua</button>
                            <ul class="dropdown-menu rounded shadow">
                                <li><a class="dropdown-item" href="#" data-filter="Semua">Semua</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="TA">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="Semester">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <div class="search-input-group ms-auto d-flex align-items-center">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama Mahasiswa..." aria-label="Cari">
                        </div>
                    </div>
                </div><br><br>
                <div class="row table-responsive">
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
                        <tbody id="dataTableBody">
                            <tr class="isiTabel" data-type="TA"><td>1</td><td>0920240033</td><td>M. Harris Nur S.</td><td>Tugas Akhir</td><td>Timotius Victory</td><td><button class="detail-btn" data-nim="0920240033" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>2</td><td>0920240053</td><td>Nayaka Ivanna</td><td>Tugas Akhir</td><td>Timotius Victory</td><td><button class="detail-btn" data-nim="0920240053" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>3</td><td>0920240055</td><td>Nur Widya Astuti</td><td>Tugas Akhir</td><td>Timotius Victory</td><td><button class="detail-btn" data-nim="0920240055" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>4</td><td>0920240033</td><td>M. Harris Nur S.</td><td>Pemrograman 2</td><td>Timotius Victory</td><td><button class="detail-btn" data-nim="0920240033" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>5</td><td>0920240053</td><td>Nayaka Ivanna</td><td>Basis Data</td><td>Timotius Victory</td><td><button class="detail-btn" data-nim="0920240053" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>6</td><td>0920240055</td><td>Nur Widya Astuti</td><td>Struktur Data</td><td>Timotius Victory</td><td><button class="detail-btn" data-nim="0920240055" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>7</td><td>0920240001</td><td>Ahmad Budi</td><td>Tugas Akhir</td><td>Rida Indah Fariani</td><td><button class="detail-btn" data-nim="0920240001" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>8</td><td>0920240002</td><td>Citra Dewi</td><td>Tugas Akhir</td><td>Rida Indah Fariani</td><td><button class="detail-btn" data-nim="0920240002" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>9</td><td>0920240003</td><td>Eko Prasetyo</td><td>Tugas Akhir</td><td>Suhendra</td><td><button class="detail-btn" data-nim="0920240003" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>10</td><td>0920240004</td><td>Fitriani Lestari</td><td>Tugas Akhir</td><td>Suhendra</td><td><button class="detail-btn" data-nim="0920240004" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="TA"><td>11</td><td>0920240005</td><td>Gunawan Wibisono</td><td>Tugas Akhir</td><td>Rida Indah Fariani</td><td><button class="detail-btn" data-nim="0920240005" data-type="TA"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>12</td><td>0920240001</td><td>Ahmad Budi</td><td>Basis Data</td><td>Siti Aisyah</td><td><button class="detail-btn" data-nim="0920240001" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>13</td><td>0920240002</td><td>Citra Dewi</td><td>Struktur Data</td><td>Ahmad Khoirul</td><td><button class="detail-btn" data-nim="0920240002" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>14</td><td>0920240003</td><td>Eko Prasetyo</td><td>Jaringan Komputer</td><td>Benyamin</td><td><button class="detail-btn" data-nim="0920240003" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                            <tr class="isiTabel" data-type="Semester"><td>15</td><td>0920240004</td><td>Fitriani Lestari</td><td>Sistem Operasi</td><td>Siti Aisyah</td><td><button class="detail-btn" data-nim="0920240004" data-type="Semester"><i class="fa-solid fa-file-signature"></i></button></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);"><div class="modal-header"><h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1></div></div>
                <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let menuToggle = document.querySelector(".NavSide__toggle");
            let sidebar = document.getElementById("main-sidebar");
            if (menuToggle) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            let currentPage = 1;
            const rowsPerPage = 10;
            let currentFilter = 'Semua';

            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('dataTableBody');
            const allTableRows = Array.from(tableBody.querySelectorAll('tr'));
            const paginationControls = document.getElementById('pagination-controls');
            const dropdownButton = document.getElementById('ddDosenSidang');
            const dropdownItems = document.querySelectorAll('.dropdown-menu a[data-filter]');

            function renderTable() {
                const searchQuery = searchInput.value.toLowerCase();
                
                let filteredRows = allTableRows.filter(row => {
                    const typeMatch = currentFilter === 'Semua' || row.dataset.type === currentFilter;
                    const nameCell = row.children[2];
                    const searchMatch = nameCell.textContent.toLowerCase().includes(searchQuery);
                    return typeMatch && searchMatch;
                });
                
                allTableRows.forEach(row => row.style.display = 'none');
                
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedRows = filteredRows.slice(startIndex, endIndex);

                paginatedRows.forEach(row => {
                    row.style.display = 'table-row';
                });

                setupPagination(filteredRows.length);
            }

            function setupPagination(totalRows) {
                paginationControls.innerHTML = '';
                const pageCount = Math.ceil(totalRows / rowsPerPage);
                if (pageCount <= 1) return;

                const prevButton = createPaginationButton('&laquo;', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderTable();
                    }
                });
                if (currentPage === 1) prevButton.classList.add('disabled');
                paginationControls.appendChild(prevButton);

                for (let i = 1; i <= pageCount; i++) {
                    const pageButton = createPaginationButton(i, () => {
                        currentPage = i;
                        renderTable();
                    });
                    if (i === currentPage) pageButton.classList.add('active');
                    paginationControls.appendChild(pageButton);
                }

                const nextButton = createPaginationButton('&raquo;', () => {
                    if (currentPage < pageCount) {
                        currentPage++;
                        renderTable();
                    }
                });
                if (currentPage === pageCount) nextButton.classList.add('disabled');
                paginationControls.appendChild(nextButton);
            }

            function createPaginationButton(html, clickHandler) {
                const button = document.createElement('li');
                button.className = 'page-item';
                const link = document.createElement('a');
                link.className = 'page-link';
                link.href = '#';
                link.innerHTML = html;
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    clickHandler();
                });
                button.appendChild(link);
                return button;
            }

            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentFilter = this.dataset.filter;
                    dropdownButton.textContent = this.textContent;
                    currentPage = 1;
                    renderTable();
                });
            });

            searchInput.addEventListener('keyup', () => {
                currentPage = 1;
                renderTable();
            });

            const allDetailButtons = document.querySelectorAll('.detail-btn');
            allDetailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const nim = this.dataset.nim;
                    const type = this.dataset.type;
                    if(nim && type) {
                        window.location.href = `dEvaluasi.php?nim=${nim}&type=${type}`;
                    }
                });
            });
            
            renderTable();
        });
    </script>
</body>
</html>