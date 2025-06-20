<?php
// 1. INISIALISASI DAN KONEKSI
require "../../koneksi.php"; // Pastikan path ini benar

// 2. PERSIAPAN VARIABEL FILTER DAN PAGINASI
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$prodiFilter = isset($_GET['prodi']) ? $_GET['prodi'] : 'all';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = 10;

// 3. AMBIL DAFTAR PRODI UNTUK DROPDOWN
$prodiListQuery = "SELECT DISTINCT prodi FROM Mahasiswa WHERE prodi IS NOT NULL AND prodi != '' ORDER BY prodi ASC";
$prodiListResult = sqlsrv_query($conn, $prodiListQuery);
$prodiList = [];
if ($prodiListResult) {
    while ($row = sqlsrv_fetch_array($prodiListResult, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// 4. MEMBUAT KLAUSA WHERE DINAMIS
$whereClause = [];
if ($filter !== 'all') {
    $whereClause[] = "s.jenis_sidang = " . ($filter === 'ta' ? 0 : 1);
}
if ($prodiFilter !== 'all') {
    $cleanedProdi = str_replace("'", "''", $prodiFilter); // Escaping sederhana untuk SQL Server
    $whereClause[] = "ma.prodi = '" . $cleanedProdi . "'";
}
$whereSql = "";
if (!empty($whereClause)) {
    $whereSql = " WHERE " . implode(' AND ', $whereClause);
}

// 5. QUERY UNTUK MENGHITUNG TOTAL DATA (DENGAN FILTER)
$countQuery = "SELECT COUNT(DISTINCT s.id_sidang) as total 
               FROM Sidang s
               JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
               JOIN Mahasiswa ma ON km.nim = ma.nim" . $whereSql;

$countResult = sqlsrv_query($conn, $countQuery);
if ($countResult === false) { 
    die("Error di countQuery: " . print_r(sqlsrv_errors(), true)); 
}
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// 6. QUERY UTAMA UNTUK MENGAMBIL DATA PER HALAMAN (DENGAN FILTER)
// Perbaikan: Mengambil data mahasiswa melalui join yang benar
$query = "SELECT s.id_sidang, s.id_kelompok, s.judul, CAST(s.jenis_sidang AS INT) AS jenis_sidang,
                 m.nama_matkul, 
                 ma.nim, ma.nama AS nama_mahasiswa, ma.prodi,
                 MIN(d.nama_dosen) AS dosen 
          FROM Sidang s
          JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
          JOIN Mahasiswa ma ON km.nim = ma.nim
          JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
          JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
          JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen" . $whereSql;

$query .= " GROUP BY s.id_sidang, s.id_kelompok, s.judul, s.jenis_sidang, m.nama_matkul, ma.nim, ma.nama, ma.prodi 
            ORDER BY s.id_sidang";
$query .= " OFFSET " . (($currentPage - 1) * $rowsPerPage) . " ROWS FETCH NEXT " . $rowsPerPage . " ROWS ONLY";

$result = sqlsrv_query($conn, $query);
if ($result === false) { 
    die("Error di main query: " . print_r(sqlsrv_errors(), true)); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <title>Admin - Daftar Sidang</title>
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
            width: 15%;
        }

        thead th:nth-child(3) {
            width: 20%;
        }

        thead th:nth-child(4) {
            width: 15%;
        }

        thead th:nth-child(5) {
            width: 20%;
        }

        thead th:nth-child(6) {
            width: 15%;
        }

        thead th:nth-child(7) {
            text-align: center;
            width: 10%;
        }

        .isiTabel td {
            padding: 12px 15px;
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            vertical-align: middle;
        }

        .isiTabel td:nth-child(1) {
            border-radius: 20px 0 0 20px;
            text-align: center;
        }

        .isiTabel td:nth-child(7) {
            border-radius: 0 20px 20px 0;
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

        .table-admin-custom tbody tr.isiTabel:hover .detail-btn {
            color: #FFFFFF;
            opacity: 1;
        }

        .modal-header-custom {
            background-color: #4B68FB;
            color: white;
        }

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
            margin-top: 0.19vh -1px;
            margin-right: 1vh;
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
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="logout.html" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons d-flex d-md-none">
                <a href="aNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon">
                    <a href="aProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Daftar Sidang</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                         <a href="aProfil.php" title="Profil">
                            <i class="bi bi-person-fill fs-5" style="color: white"></i>
                        </a>
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
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $filter === 'ta' ? 'Sidang TA' : ($filter === 'semester' ? 'Sidang Semester' : 'Semua') ?>
                                </button>
                                <ul class="dropdown-menu rounded shadow">
                                    <li><a class="dropdown-item" href="?filter=all&prodi=<?= $prodiFilter ?>&page=1">Semua</a></li>
                                    <li><a class="dropdown-item" href="?filter=ta&prodi=<?= $prodiFilter ?>&page=1">Sidang TA</a></li>
                                    <li><a class="dropdown-item" href="?filter=semester&prodi=<?= $prodiFilter ?>&page=1">Sidang Semester</a></li>
                                </ul>
                            </div>
                            
                            <label for="ddProdi" class="fw-semibold mb-0 ms-3">Prodi:</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $prodiFilter === 'all' ? 'Semua Prodi' : htmlspecialchars($prodiFilter) ?>
                                </button>
                                <ul class="dropdown-menu rounded shadow">
                                    <li><a class="dropdown-item" href="?filter=<?= $filter ?>&prodi=all&page=1">Semua Prodi</a></li>
                                    <?php foreach ($prodiList as $prodi): ?>
                                        <li><a class="dropdown-item" href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodi) ?>&page=1"><?= htmlspecialchars($prodi) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="search-input-group ms-auto d-flex align-items-center">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari Nama Mahasiswa..." aria-label="Cari" id="searchInput">
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
                                    <th scope="col">Prodi</th>
                                    <th scope="col">Mata Kuliah</th>
                                    <th scope="col">Dosen Pembimbing</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (sqlsrv_has_rows($result)): ?>
                                    <?php 
                                    $counter = ($currentPage - 1) * $rowsPerPage + 1;
                                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): 
                                    ?>
                                        <tr class="isiTabel jadiBiru">
                                            <td><?= $counter ?></td>
                                            <td><?= htmlspecialchars($row['nim']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                                            <td><?= htmlspecialchars($row['prodi']) ?></td>
                                            <td><?= htmlspecialchars(($row['jenis_sidang'] == 0) ? 'Tugas Akhir' : $row['nama_matkul']) ?></td>
                                            <td><?= htmlspecialchars($row['dosen']) ?></td>
                                            <td style="text-align: center;">
                                                <button class="detail-btn" onclick="viewDetails('<?= $row['id_sidang'] ?>')">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                        $counter++;
                                    endwhile; 
                                    ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">Tidak ada data untuk ditampilkan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($totalPages > 1): ?>
                                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?filter=<?= $filter ?>&prodi=<?= $prodiFilter ?>&page=<?= $currentPage - 1 ?>">&laquo;</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link" href="?filter=<?= $filter ?>&prodi=<?= $prodiFilter ?>&page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?filter=<?= $filter ?>&prodi=<?= $prodiFilter ?>&page=<?= $currentPage + 1 ?>">&raquo;</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
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
                // JavaScript untuk search dengan client-side filtering
                document.addEventListener("DOMContentLoaded", function () {
                    const searchInput = document.getElementById('searchInput');
                    const tableRows = document.querySelectorAll('tbody tr.isiTabel');

                    searchInput.addEventListener("keyup", function () {
                        const query = searchInput.value.toLowerCase();
                        
                        tableRows.forEach(row => {
                            const namaCell = row.children[2]; // Kolom nama (index 2)
                            const namaText = namaCell.textContent.toLowerCase();
                            
                            if (namaText.includes(query)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                });

                function viewDetails(idSidang) {
                    // Implementasi untuk melihat detail sidang
                    console.log('View details for sidang ID:', idSidang);
                    // window.location.href = 'path/to/detail/page.php?id=' + idSidang;
                }
                
                let menuToggle = document.querySelector(".NavSide__toggle");
                let sidebar = document.getElementById("main-sidebar");

                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            </script>
            <script src="../../assets/js/main.js"></script>
        </main>
    </div>
</body>
</html>

<?php
// Tutup koneksi database
if ($conn) {
    sqlsrv_close($conn);
}
?>