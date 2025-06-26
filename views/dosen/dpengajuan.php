<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];
include '../../koneksi/koneksiAndrew.php';
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
                                    <th scope="col">Judul</th>
                                    <th scope="col">Mata Kuliah</th>
                                    <th scope="col">Dosen Pembimbing</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>

                        <tbody id="dPengajuanTA">
                            <?php
                           $no = 1;
                      $sqlTA = "SELECT s.id_kelompok, s.judul, s.jenis_sidang, d.nama_dosen
                                FROM Sidang s
                                JOIN Dosen d ON s.nomor_dosen = d.nomor_dosen
                                WHERE s.jenis_sidang = '0'";
                        $resultTA = sqlsrv_query($conn, $sqlTA);

                        // Simulasi dummy data
                        $dummyTA = [
                            [
                                'id_kelompok' => '001',
                                'judul' => 'Sistem Informasi Penggajian',
                                'jenis_sidang' => 'Sidang Akhir',
                                'nama_dosen' => 'Timotius Victory'
                            ],
                            [
                                'id_kelompok' => '002',
                                'judul' => 'Aplikasi Kasir Modern',
                                'jenis_sidang' => 'Sidang Semester',
                                'nama_dosen' => 'Timotius Victory'
                            ]
                        ];
                        $no = 1;
                        foreach ($dummyTA as $row) {
                            echo "<tr class='isiTabel jadiBiru'>
                                <td>{$no}</td>
                                <td>{$row['id_kelompok']}</td>
                                <td>{$row['judul']}</td>
                                <td>{$row['jenis_sidang']}</td>
                                <td>{$row['nama_dosen']}</td>
                                <td style='text-align: center;'>
                                    <button class='detail-btn' onclick=\"goToDetail('{$row['id_kelompok']}', '0')\">
                                        <i class='bi bi-eye'></i>
                                    </button>
                                </td>
                            </tr>";
                            $no++;
                        }

                        if ($resultTA && sqlsrv_has_rows($resultTA)) {
                            while ($row = sqlsrv_fetch_array($resultTA, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['id_kelompok']); ?></td>
                                    <td><?= htmlspecialchars($row['id_sidang']); ?></td>
                                    <td><?= htmlspecialchars($row['jenis_sidang']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_dosen']); ?></td>
                                    <td style="text-align: center;">
                                        <button class="detail-btn" onclick="goToDetail('<?= $row['id_kelompok']; ?>', '0')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            // echo '<tr><td colspan="6" class="text-center">Tidak ada data Sidang TA.</td></tr>';
                        }
                            ?>
                        </tbody>
                        <tbody id="dPengajuanSem" style="display: none;">
                            <?php
                            $no = 1;
                            $sqlSem = "SELECT s.id_kelompok, s.judul, s.jenis_sidang, d.nama_dosen
                                        FROM Sidang s
                                        JOIN Dosen d ON s.nomor_dosen = d.nomor_dosen
                                        WHERE s.jenis_sidang = '1'";

                            $resultSem = sqlsrv_query($conn, $sqlSem);
                            if ($resultSem && sqlsrv_has_rows($resultSem) > 0) {
                                while ($row = sqlsrv_fetch_assoc($resultSem)) {
                                    ?>
                                    <tr class="isiTabel jadiBiru">
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['id_kelompok']); ?></td>
                                        <td><?= htmlspecialchars($row['id_sidang']); ?></td>
                                        <td><?= htmlspecialchars($row['jenis_sidang']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_dosen']); ?></td>
                                        <td style="text-align: center;">
                                            <button class="detail-btn" onclick="goToDetail('<?= $row['id_kelompok']; ?>', 'Semester')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                // echo '<tr><td colspan="6" class="text-center">Tidak ada data Sidang Semester.</td></tr>';
                            }
                            ?>
                        </tbody>
                        </table>
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                            </nav>
                        </div>

                    </div>
                </div>

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

                <div class="modal fade" id="kelompokModal" tabindex="-1" aria-labelledby="kelompokModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered ">
                        <div class="modal-content">
                            <div class="modal-header modal-header-custom">
                                <h5 class="modal-title" id="kelompokModalLabel">Kelompok Mahasiswa</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="modal-tab-container">
                                    <button class="modal-tab active" onclick="switchTab('tambah')">Tambah Kelompok</button>
                                    <button class="modal-tab" onclick="switchTab('daftar')">Daftar Kelompok</button>
                                </div>

                                <div id="tambah-tab" class="modal-tab-content active">
                                    <div class="kelompok-form-container">
                                        <form id="kelompokForm">
                                            <div class="kelompok-form-group"> <label for="kelompok_id">ID Kelompok:</label>
                                                <input type="text" id="kelompok_id" name="kelompok_id" readonly />
                                            </div>
                                            <div class="kelompok-form-group">
                                                <label for="kelompok_prodi">Prodi:</label>
                                                <select id="kelompok_prodi" name="kelompok_prodi" onchange="filterMahasiswaByProdi()">
                                                    <option value="">Pilih Prodi</option>
                                                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                                    <option value="Manajemen Informatika">Manajemen Informatika</option>
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
                                                        <div class="anggota-nama-display" id="anggota_nama_1">Nama akan muncul otomatis</div>
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

                                <div id="daftar-tab" class="modal-tab-content">
                                    <div class="kelompok-list-container" id="kelompok-list-container">
                                        <p class="text-center text-muted">Memuat daftar kelompok...</p>
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
                                pageButton.innerHTML = <a class="page-link" href="#">${i}</a>;
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
                    let mahasiswaData = []; // This will now be loaded from DB
                    let kelompokData = []; // This will now be loaded from DB

                    // Initialize modal and data
                    document.addEventListener('DOMContentLoaded', function() {
                        const kelompokModalEl = document.getElementById('kelompokModal');
                        if (kelompokModalEl) {
                            kelompokModalInstance = new bootstrap.Modal(kelompokModalEl);
                            // Event listener to reset form when modal is hidden
                            kelompokModalEl.addEventListener('hidden.bs.modal', resetKelompokForm);
                        }

                        // Set up form submission
                        const kelompokForm = document.getElementById('kelompokForm');
                        if (kelompokForm) {
                            kelompokForm.addEventListener('submit', handleKelompokFormSubmit);
                        }

                        // Initial data load for mahasiswa
                        fetchMahasiswaData();
                    });

                    // Function to fetch mahasiswa data from the backend
                    async function fetchMahasiswaData() {
                        try {
                            const response = await fetch('../../control/get_mahasiswa.php');
                            if (!response.ok) {
                                throw new Error(HTTP error! status: ${response.status});
                            }
                            mahasiswaData = await response.json();
                            console.log('Loaded mahasiswaData:', mahasiswaData); // Debug log
                        } catch (error) {
                            console.error('Error fetching mahasiswa data:', error);
                            alert('Gagal memuat data mahasiswa untuk autocomplete.');
                        }
                    }

                    // Open Kelompok Modal
                    function openKelompokModal() {
                        resetKelompokForm(); // Ensure form is reset every time it opens
                        setNextKelompokId(); // Fetch and set the next Kelompok ID
                        switchTab('tambah'); // Default to 'Tambah Kelompok' tab
                        loadKelompokList(); // Load the list when opening the modal
                        kelompokModalInstance.show();
                    }

                    // Fetch and set the next Kelompok ID
                    async function setNextKelompokId() {
                        try {
                            const response = await fetch('../../control/get_next_kelompok_id.php');
                            if (!response.ok) throw new Error('Failed to fetch next Kelompok ID');
                            const data = await response.json();
                            document.getElementById('kelompok_id').value = data.next_id;
                        } catch (e) {
                            document.getElementById('kelompok_id').value = '';
                        }
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
                            loadKelompokList(); // Reload list when switching to daftar tab
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

                    // Filter mahasiswa by prodi (no changes needed for this function's logic)
                    function filterMahasiswaByProdi() {
                        const prodiSelect = document.getElementById('kelompok_prodi');
                        currentProdi = prodiSelect.value;
                        resetAnggotaInputs(); // Clear current inputs when prodi changes
                    }

                    // Search mahasiswa for autocomplete
                    function searchMahasiswa(input, anggotaIndex) {
                        const query = input.value.toLowerCase().trim();
                        const dropdown = document.getElementById(autocomplete_${anggotaIndex});
                        const namaDisplay = document.getElementById(anggota_nama_${anggotaIndex});

                        // console.log('currentProdi:', currentProdi);
                        // console.log('mahasiswaData sample:', mahasiswaData.slice(0, 5));

                        // Clear name display and hide dropdown if input is empty
                        if (query.length === 0) {
                            dropdown.style.display = 'none';
                            namaDisplay.textContent = 'Nama mahasiswa'; // Reset display
                            return;
                        }

                        if (!currentProdi) {
                            dropdown.innerHTML = '<div class="autocomplete-item">Pilih Prodi terlebih dahulu</div>';
                            dropdown.style.display = 'block';
                            return;
                        }

                        // Filter mahasiswa by prodi and search query (case-insensitive, trimmed)
                        const filteredMahasiswa = mahasiswaData.filter(mhs =>
                            mhs.prodi && currentProdi &&
                            mhs.prodi.trim().toLowerCase() === currentProdi.trim().toLowerCase() &&
                            (String(mhs.nim).toLowerCase().includes(query) || mhs.nama_mhs.toLowerCase().includes(query))
                        );

                        // Exclude already selected NIMS
                        const selectedNIMs = Array.from(document.querySelectorAll('input[name="anggota_nim[]"]'))
                                    .map(input => input.value.trim())
                                    .filter(nim => nim !== '' && nim !== input.value.trim()); // Exclude current input's value

                        const finalFilteredMahasiswa = filteredMahasiswa.filter(mhs => !selectedNIMs.includes(String(mhs.nim)));

                        if (finalFilteredMahasiswa.length > 0) {
                            dropdown.innerHTML = '';
                            finalFilteredMahasiswa.forEach((mhs, index) => {
                                const item = document.createElement('div');
                                item.className = 'autocomplete-item';
                                item.dataset.nim = mhs.nim;
                                item.dataset.nama = mhs.nama_mhs; // Use nama_mhs from DB
                                item.dataset.index = index;
                                item.innerHTML = `
                                <div class="nim">${mhs.nim}</div>
                                <div class="nama">${mhs.nama_mhs}</div>
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

                    // Highlight autocomplete item on hover (no changes needed)
                    function highlightItem(item, dropdown) {
                        const items = dropdown.querySelectorAll('.autocomplete-item');
                        items.forEach(i => i.classList.remove('selected'));
                        item.classList.add('selected');
                    }

                    // Select mahasiswa from autocomplete
                    function selectMahasiswa(mahasiswa, anggotaIndex) {
                        const nimInput = document.getElementById(anggota_nim_${anggotaIndex});
                        const namaDisplay = document.getElementById(anggota_nama_${anggotaIndex});
                        const dropdown = document.getElementById(autocomplete_${anggotaIndex});
                        nimInput.value = mahasiswa.nim;
                        namaDisplay.textContent = mahasiswa.nama_mhs;
                        dropdown.style.display = 'none';
                    }

                    // Add new anggota (no changes needed, but ensure its initial display is correct)
                    function addAnggota() {
                        anggotaCount++;
                        const wrapper = document.getElementById('anggota-wrapper');
                        const div = document.createElement('div');
                        div.className = 'anggota-form-group';
                        div.id = anggota-form-${anggotaCount};
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

                    // Remove anggota (no changes needed)
                    function removeAnggota() {
                        if (anggotaCount > 1) {
                            const lastForm = document.getElementById(anggota-form-${anggotaCount});
                            if (lastForm) {
                                lastForm.remove();
                                anggotaCount--;
                            }
                        }
                        updateToggleButtonsVisibility();
                    }

                    // Update toggle buttons visibility (no changes needed)
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
                        const wrapper = document.getElementById('anggota-wrapper');
                        wrapper.innerHTML = `
                            <div class="anggota-form-group" id="anggota-form-1">
                                <label for="anggota_nim_1">Anggota 1:</label>
                                <div class="anggota-input-group">
                                    <div class="input-container">
                                        <input type="text" id="anggota_nim_1" name="anggota_nim[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" />
                                        <div class="autocomplete-dropdown" id="autocomplete_1" style="display: none;"></div>
                                    </div>
                                    <div class="anggota-nama-display" id="anggota_nama_1">Nama akan muncul otomatis</div>
                                    <div class="form-toggle-buttons">
                                        <button type="button" onclick="addAnggota()">+</button>
                                        <button type="button" onclick="removeAnggota()" style="display: none;">-</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        anggotaCount = 1;
                        updateToggleButtonsVisibility();
                    }

                    // Reset kelompok form
                    function resetKelompokForm() {
                        document.getElementById('kelompokForm').reset();
                        document.getElementById('kelompok_prodi').value = '';
                        resetAnggotaInputs(); // Resets to one empty anggota input
                        // No need for generateKelompokId() as it's auto-generated by DB
                        updateToggleButtonsVisibility();
                    }

                    // Function to fetch and load kelompok list from backend
                    async function loadKelompokList() {
                        const container = document.getElementById('kelompok-list-container');
                        container.innerHTML = '<p class="text-center text-muted">Memuat daftar kelompok...</p>'; // Loading state

                        try {
                            const response = await fetch('../../control/get_kelompok_list.php'); // Create this new PHP file
                            if (!response.ok) {
                                throw new Error(HTTP error! status: ${response.status});
                            }
                            kelompokData = await response.json(); // Update global kelompokData

                            if (kelompokData.length === 0) {
                                container.innerHTML = '<p class="text-center text-muted">Belum ada kelompok yang dibuat.</p>';
                                return;
                            }

                            container.innerHTML = ''; // Clear loading state
                            kelompokData.forEach(kelompok => {
                                const kelompokItem = document.createElement('div');
                                kelompokItem.className = 'kelompok-list-item';
                                kelompokItem.innerHTML = `
                                    <div class="kelompok-list-header">
                                        <div>
                                            <div class="kelompok-list-title">${kelompok.id_kelompok}</div>
                                            <div class="kelompok-list-prodi">${kelompok.prodi || 'Tidak ada prodi'}</div>
                                        </div>
                                    </div>
                                    <div class="kelompok-list-anggota">
                                        <strong>Anggota:</strong><br>
                                        ${kelompok.anggota.map(angg => ${angg.nim} - ${angg.nama_mhs}).join('<br>')}
                                    </div>
                                `;
                                container.appendChild(kelompokItem);
                            });
                        } catch (error) {
                            console.error('Error fetching kelompok data:', error);
                            container.innerHTML = '<p class="text-center text-danger">Gagal memuat daftar kelompok. Terjadi kesalahan.</p>';
                        }
                    }

                    // Handle form submission - NOW SENDS TO BACKEND!
                    async function handleKelompokFormSubmit(event) {
                        event.preventDefault();

                        // Validate form
                        if (!validateKelompokForm()) {
                            return;
                        }

                        const prodi = document.getElementById('kelompok_prodi').value;
                        const anggotaNIMs = [];
                        for (let i = 1; i <= anggotaCount; i++) {
                            const nimInput = document.getElementById(anggota_nim_${i});
                            if (nimInput.value.trim() !== '') {
                                anggotaNIMs.push(nimInput.value.trim());
                            }
                        }

                        const formData = new FormData();
                        formData.append('kelompok_prodi', prodi);
                        anggotaNIMs.forEach(nim => {
                            formData.append('anggota_nim[]', nim);
                        });

                        try {
                            const response = await fetch('../../control/kelompok_create.php', {
                                method: 'POST',
                                body: formData
                            });

                            if (!response.ok) {
                                throw new Error(HTTP error! status: ${response.status});
                            }

                            const result = await response.json();

                            if (result.success) {
                                alert(result.message);
                                resetKelompokForm(); // Reset form after successful submission
                                kelompokModalInstance.hide(); // Close modal
                                loadKelompokList(); // Refresh the list of groups
                            } else {
                                alert('Error: ' + result.message);
                            }
                        } catch (error) {
                            console.error('Error creating kelompok:', error);
                            alert('Terjadi kesalahan saat membuat kelompok. Silakan coba lagi.');
                        }
                    }

                    // Validate kelompok form (no need to check prodi of anggota, since autocomplete already filters by prodi)
                    function validateKelompokForm() {
                        const prodi = document.getElementById('kelompok_prodi').value;
                        if (!prodi) {
                            alert('Pilih Prodi terlebih dahulu!');
                            return false;
                        }

                        let hasAnggota = false;
                        const selectedNIMs = new Set(); // Use a Set to check for duplicates
                        for (let i = 1; i <= anggotaCount; i++) {
                            const nimInput = document.getElementById(anggota_nim_${i});
                            if (nimInput.value.trim() !== '') {
                                // Check if NIM exists in the fetched mahasiswaData
                                const foundMahasiswa = mahasiswaData.find(mhs => String(mhs.nim) === nimInput.value.trim());
                                if (!foundMahasiswa) {
                                    alert(NIM ${nimInput.value.trim()} tidak ditemukan.);
                                    return false;
                                }
                                if (selectedNIMs.has(nimInput.value.trim())) {
                                    alert(NIM ${nimInput.value.trim()} sudah ditambahkan.);
                                    return false;
                                }
                                selectedNIMs.add(nimInput.value.trim());
                                hasAnggota = true;
                            }
                        }

                        if (!hasAnggota) {
                            alert('Minimal harus ada satu anggota!');
                            return false;
                        }

                        return true;
                    }

                    // Close autocomplete dropdowns when clicking outside (no changes needed)
                    document.addEventListener('click', function(event) {
                        const dropdowns = document.querySelectorAll('.autocomplete-dropdown');
                        dropdowns.forEach(dropdown => {
                            if (!dropdown.contains(event.target) && !event.target.matches('input[name="anggota_nim[]"]')) {
                                dropdown.style.display = 'none';
                            }
                        });
                    });

                    // Add keyboard navigation for autocomplete (no changes needed)
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
                                        nim: nim,
                                        nama_mhs: nama
                                    }, anggotaIndex); // Pass as object with nama_mhs
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