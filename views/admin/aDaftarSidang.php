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
<<<<<<< HEAD
               JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok -- Jembatan ke-1
               JOIN Mahasiswa m ON km.nim = m.nim"; // Jembatan ke-2

if ($filter === 'ta') {
    $countQuery .= " WHERE s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $countQuery .= " WHERE s.jenis_sidang = 1";
}
=======
               JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
               JOIN Mahasiswa ma ON km.nim = ma.nim" . $whereSql;
>>>>>>> 956844bed77e97f6728654849ff854ae97e102e7

$countResult = sqlsrv_query($conn, $countQuery);
if ($countResult === false) { die("Error di countQuery: " . print_r(sqlsrv_errors(), true)); }
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);


<<<<<<< HEAD
// --- PERBAIKAN QUERY UTAMA PENGAMBILAN DATA ---
$query = "SELECT s.id_sidang, s.judul, s.jenis_sidang, s.id_kelompok, 
                 m.nama_matkul, 
                 MIN(d.nama_dosen) AS dosen 
          FROM Sidang s
          -- PERBAIKAN UTAMA: Menggunakan tabel jembatan Kelompok_Mahasiswa
          -- Join lainnya tetap sama
          JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
          JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
          JOIN Jadwal j ON s.id_sidang = j.id_sidang
          JOIN Penjadwalan p ON j.id_sidang = p.id_sidang
          JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen";

$whereClause = [];
if ($filter === 'ta') {
    $whereClause[] = "s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $whereClause[] = "s.jenis_sidang = 1";
}
if (!empty($whereClause)) {
    $query .= " WHERE " . implode(' AND ', $whereClause);
}

// Menyesuaikan GROUP BY dengan semua kolom yang dibutuhkan
$query .= " GROUP BY s.id_sidang, s.judul, s.jenis_sidang, s.id_kelompok, m.nama_matkul 
            ORDER BY s.id_sidang";
=======
// 6. QUERY UTAMA UNTUK MENGAMBIL DATA PER HALAMAN (DENGAN FILTER)
$query = "SELECT s.id_sidang, s.id_kelompok, s.judul, CAST(s.jenis_sidang AS INT) AS jenis_sidang,
                 m.nama_matkul, 
                 MIN(d.nama_dosen) AS dosen 
          FROM Sidang s
          JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
          JOIN Mahasiswa ma ON km.nim = ma.nim
          JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
          JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
          JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen" . $whereSql;

$query .= " GROUP BY s.id_sidang, s.id_kelompok, s.judul, s.jenis_sidang, m.nama_matkul ORDER BY s.id_sidang";
>>>>>>> 956844bed77e97f6728654849ff854ae97e102e7
$query .= " OFFSET " . (($currentPage - 1) * $rowsPerPage) . " ROWS FETCH NEXT " . $rowsPerPage . " ROWS ONLY";

$result = sqlsrv_query($conn, $query);
if ($result === false) { die("Error di main query: " . print_r(sqlsrv_errors(), true)); }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/aDaftarSidang.css"> 
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo Admin"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aBeranda.php"><span class="fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aPenjadwalan.php"><span class="fw-semibold">Penjadwalan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span class="fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div id="mobile-icons-container"></div>
        </div>
        
        <main class="NavSide__main-content" id="adminDaftarSidangContent">
            <div class="main-header">
                <div class="header-left-panel">
                    <h1 class="main-title">Daftar Sidang</h1>
                    <div class="filter-container">
                        <span class="filter-label fw-semibold">Filter:</span>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $filter === 'ta' ? 'Sidang TA' : ($filter === 'semester' ? 'Sidang Semester' : 'Jenis Sidang') ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=all&prodi=<?= urlencode($prodiFilter) ?>&page=1">Semua Jenis</a></li>
                                <li><a class="dropdown-item" href="?filter=ta&prodi=<?= urlencode($prodiFilter) ?>&page=1">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=semester&prodi=<?= urlencode($prodiFilter) ?>&page=1">Sidang Semester</a></li>
                            </ul>
                        </div>
                        
                        <div class="dropdown ms-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $prodiFilter === 'all' ? 'Pilih Prodi' : htmlspecialchars($prodiFilter) ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=<?= $filter ?>&prodi=all&page=1">Semua Prodi</a></li>
                                <?php foreach ($prodiList as $prodi): ?>
                                    <li><a class="dropdown-item" href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodi) ?>&page=1"><?= htmlspecialchars($prodi) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="header-right-panel">
                    <div id="desktop-icons-container">
                        <div class="header-icons">
                            <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                            <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="bi bi-person-fill"></i></a></div>
                        </div>
                    </div>
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari" id="searchInput">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table-admin-custom">
                    <thead>
                        <tr>
                            <th scope="col">Nomor</th>
<<<<<<< HEAD
                            <th scope="col">Kelompok</th>
                            <th scope="col" id="thDynamicHeader">
                                <?php
                                if ($filter === 'ta') echo "Judul Sidang";
                                elseif ($filter === 'semester') echo "Mata Kuliah";
                                else echo "Judul/Mata Kuliah";
                                ?>
=======
                            <th scope="col">ID Kelompok</th>
                            <th scope="col">
                                <?= $filter === 'ta' ? "Judul Sidang" : ($filter === 'semester' ? "Mata Kuliah" : "Judul/Mata Kuliah") ?>
>>>>>>> 956844bed77e97f6728654849ff854ae97e102e7
                            </th>
                            <th scope="col">Pembimbing</th>
                            <th scope="col" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sqlsrv_has_rows($result)): ?>
                            <?php 
                            $counter = ($currentPage - 1) * $rowsPerPage + 1;
                            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): 
                            ?>
                                <tr class="isiTabel">
                                    <td data-label="Nomor"><?= $counter ?></td>
<<<<<<< HEAD
                                    <td data-label="ID_Kelompok"><?= htmlspecialchars($row['id_kelompok']) ?></td>
=======
                                    <td data-label="ID Kelompok"><?= htmlspecialchars($row['id_kelompok']) ?></td>
>>>>>>> 956844bed77e97f6728654849ff854ae97e102e7
                                    <td data-label="Judul/MK">
                                        <?= htmlspecialchars(($row['jenis_sidang'] == 0) ? $row['judul'] : $row['nama_matkul']) ?>
                                    </td>
                                    <td data-label="Pembimbing"><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td data-label="Aksi">
<<<<<<< HEAD
                                    <button type="button" class="btn detail-btn" onclick="window.location.href='aDetailSidang.php?id=<?= $row['id_sidang'] ?>'">
                                    <i class="fa-solid fa-file-signature"></i>
                                    </button>
=======
                                        <?php $detailPage = ($row['jenis_sidang'] == 0) ? 'aDetailSidangTA.php' : 'aDetailSidangSem.php'; ?>
                                        <button type="button" class="btn detail-btn" onclick="window.location.href='<?= $detailPage ?>?id=<?= $row['id_sidang'] ?>'">
                                            <i class="fa-solid fa-file-signature"></i>
                                        </button>
>>>>>>> 956844bed77e97f6728654849ff854ae97e102e7
                                    </td>
                                </tr>
                            <?php 
                                $counter++;
                            endwhile; 
                            ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($totalPages > 1): ?>
                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodiFilter) ?>&page=<?= $currentPage - 1 ?>">&laquo;</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodiFilter) ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodiFilter) ?>&page=<?= $currentPage + 1 ?>">&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </main>
    </div>

    <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom"><h1 class="modal-title mx-auto fs-5" id="modalLogoutLabel">Perhatian!</h1></div>
                <div class="modal-body text-center py-3">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<<<<<<< HEAD
    <script>
        // JS untuk sidebar toggle
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            const desktopIconsContainer = document.getElementById('desktop-icons-container');
            const mobileIconsContainer = document.getElementById('mobile-icons-container');
            if (desktopIconsContainer) {
                const headerIcons = desktopIconsContainer.querySelector('.header-icons');
                function handleIconPlacement() {
                    if (window.innerWidth <= 992) { if (mobileIconsContainer && !mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
                    } else { if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons); }
                }
                if (menuToggle && sidebar) {
                    menuToggle.onclick = () => {
                        menuToggle.classList.toggle("NavSide__toggle--active");
                        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                    };
                }
                handleIconPlacement();
                window.addEventListener('resize', handleIconPlacement);
            }
        });
        
    </script>
=======
    <script src="../../assets/js/aDaftarSidang.js"></script>
>>>>>>> 956844bed77e97f6728654849ff854ae97e102e7
</body>
</html>