<?php
// Letakkan ini di baris paling atas file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path ke root directory. Untuk file di dalam /views/admin/, path ini sudah benar.
$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. PERUBAHAN: Cek jika role pengguna BUKAN 'admin'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

require "../../koneksi/koneksiAndrew.php";

// --- PERSIAPAN AWAL (Tidak berubah) ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$prodiFilter = isset($_GET['prodi']) ? $_GET['prodi'] : 'all';
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// --- Ambil Daftar Prodi (Tidak berubah) ---
$prodiList = [];
$prodiQuery = "SELECT DISTINCT prodi FROM dbo.Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi ASC";
$prodiResult = sqlsrv_query($conn, $prodiQuery);
if ($prodiResult) {
    while ($row = sqlsrv_fetch_array($prodiResult, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// --- QUERY BARU YANG LEBIH CERDAS ---

// Persiapan filter
// --- QUERY BARU YANG LEBIH CERDAS (VERSI PERBAIKAN) ---

// Persiapan filter
$params = [];
$whereClause = [];

if ($filter === 'ta') {
    $whereClause[] = "s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $whereClause[] = "s.jenis_sidang = 1";
}

$prodiJoin = "";
if ($prodiFilter !== 'all') {
    $prodiJoin = "JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok JOIN Mahasiswa m_prodi ON km.nim = m_prodi.nim";
    $whereClause[] = "m_prodi.prodi = ?";
    $params[] = $prodiFilter;
}

// Query untuk menghitung total data
$countQuery = "SELECT COUNT(DISTINCT s.id_sidang) as total FROM Sidang s {$prodiJoin}";
if (!empty($whereClause)) {
    // Buat klausa WHERE untuk count query
    $countWhereClause = $whereClause;
    if ($prodiFilter !== 'all') {
        array_pop($countWhereClause); // Hapus parameter prodi dari klausa count jika ada
    }
    if (!empty($countWhereClause)) {
        $countQuery .= " WHERE " . implode(" AND ", $countWhereClause);
    }
}
$countParams = ($prodiFilter !== 'all' ? [$prodiFilter] : []);
$countResult = sqlsrv_query($conn, $countQuery, $countParams);
if($countResult === false) { die(print_r(sqlsrv_errors(), true)); }
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// Query utama untuk mengambil data
$query = "SELECT DISTINCT
        s.id_sidang,
        s.judul,
        s.id_kelompok,
        s.jenis_sidang,
        
        (SELECT TOP 1 mk.nama_matkul 
         FROM Detail_Sidang ds
         JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
         WHERE ds.id_sidang = s.id_sidang) AS nama_matkul,
         
        -- [PERBAIKAN UTAMA DI SINI]
        CASE 
            WHEN s.jenis_sidang = 0 THEN -- Jika Sidang TA, ambil HANYA Pembimbing (peran=1)
                (SELECT d.nama_dosen
                 FROM Penjadwalan p
                 JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen
                 WHERE p.id_sidang = s.id_sidang AND p.peran_dosen = 1)
            WHEN s.jenis_sidang = 1 THEN -- Jika Sidang Semester, ambil Pengampu
                (SELECT STRING_AGG(d.nama_dosen, CHAR(13) + CHAR(10))
                 FROM Pengampu_Kelas pk
                 JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
                 WHERE 
                -- Filter 1: Mencocokkan mata kuliahnya (sama seperti sebelumnya)
                pk.id_matkul = (SELECT TOP 1 ds.id_matkul FROM Detail_Sidang ds WHERE ds.id_sidang = s.id_sidang)
                
                -- Filter 2: Mencocokkan kelas mahasiswa
                AND pk.id_kelas = (SELECT TOP 1 km.id_kelas
                                   FROM Kelompok_Mahasiswa kpm
                                   JOIN Kelas_Mahasiswa km ON kpm.nim = km.nim
                                   WHERE kpm.id_kelompok = s.id_kelompok))
        END AS nama_dosen_terkait
    
    FROM Sidang s
    {$prodiJoin}
";

if (!empty($whereClause)) {
    $query .= " WHERE " . implode(' AND ', $whereClause);
}

$query .= " ORDER BY s.id_sidang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

// Tambahkan parameter untuk OFFSET dan FETCH
$params_final = $params;
$params_final[] = $offset;
$params_final[] = $rowsPerPage;

$result = sqlsrv_query($conn, $query, $params_final);
if ($result === false) {
    die("Error di main query: " . print_r(sqlsrv_errors(), true));
}
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
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/aDaftarSidang.css">

</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo Admin">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aBeranda.php"><span
                            class="fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aPenjadwalan.php"><span
                            class="fw-semibold">Penjadwalan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span
                            class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal"
                        data-bs-target="#logABeranda"><span class="fw-semibold">Keluar</span></a></li>
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
                            <button class="btn btn-primary dropdown-toggle" id="ddAdminSidangTypeButton" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $filter === 'ta' ? 'Sidang TA' : ($filter === 'semester' ? 'Sidang Semester' : 'Jenis Sidang') ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="?filter=all&prodi=<?= urlencode($prodiFilter) ?>&page=1">Semua Jenis</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="?filter=ta&prodi=<?= urlencode($prodiFilter) ?>&page=1">Sidang TA</a></li>
                                <li><a class="dropdown-item"
                                        href="?filter=semester&prodi=<?= urlencode($prodiFilter) ?>&page=1">Sidang
                                        Semester</a></li>
                            </ul>
                        </div>

                        <div class="dropdown ms-2">
                            <button class="btn btn-primary dropdown-toggle" id="ddAdminSidangTypeButton" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $prodiFilter === 'all' ? 'Pilih Prodi' : htmlspecialchars($prodiFilter) ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=<?= $filter ?>&prodi=all&page=1">Semua
                                        Prodi</a></li>
                                <?php foreach ($prodiList as $prodi): ?>
                                    <li><a class="dropdown-item"
                                            href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodi) ?>&page=1"><?= htmlspecialchars($prodi) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="header-right-panel">
                    <div id="desktop-icons-container">
                        <div class="header-icons">
                            <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                            <div class="profile-icon"><a href="aProfil.php" title="Profil"><i
                                        class="bi bi-person-fill"></i></a></div>
                        </div>
                    </div>
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari"
                            id="searchInput">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table-admin-custom">
                    <thead>
                        <tr>
                            <th scope="col">Nomor</th>
                            <th scope="col">Kelompok</th>
                            <th scope="col" id="thDynamicHeader">
                                <?php
                                if ($filter === 'ta')
                                    echo "Judul Sidang";
                                elseif ($filter === 'semester')
                                    echo "Mata Kuliah";
                                else
                                    echo "Judul/Mata Kuliah";
                                ?>
                            </th>
                            <th scope="col" id="thDynamicHeader">
                                 <?php
                                if ($filter === 'ta') echo "Pembimbing";
                                elseif ($filter === 'semester') echo "Pengampu";
                                else echo "Pembimbing/Pengampu";
                                ?>
                            </th>
                            <th scope="col" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php if (sqlsrv_has_rows($result)): ?>
        <?php
        $counter = ($currentPage - 1) * $rowsPerPage + 1;
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)):
             $jenis_sidang_int = ord($row['jenis_sidang']);
        ?>
            <tr class="isiTabel">
                <td data-label="Nomor"><?= $counter ?></td>
                <td data-label="ID_Kelompok"><?= htmlspecialchars($row['id_kelompok']) ?></td>
                <td data-label="Judul/MK">
                    <?php 
                    echo htmlspecialchars(($jenis_sidang_int == 1) ? $row['nama_matkul'] : $row['judul']); 
                    ?>
                </td>
                <td data-label="Pembimbing/Pengampu">
                    <?php 
                    echo nl2br(htmlspecialchars($row['nama_dosen_terkait'])); 
                    ?>
                </td>
                <td data-label="Aksi">
                       <div class="action-wrapper">  
                         <button type="button" class="btn detail-btn"  onclick="window.location.href='aDetailSidang.php?id=<?= $row['id_sidang'] ?>'">
                          <i class="fa-solid fa-file-signature"></i>
                         </button>
                      </div>
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
                                <a class="page-link"
                                    href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodiFilter) ?>&page=<?= $currentPage - 1 ?>">&laquo;</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodiFilter) ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?filter=<?= $filter ?>&prodi=<?= urlencode($prodiFilter) ?>&page=<?= $currentPage + 1 ?>">&raquo;</a>
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
                <div class="modal-header modal-header-custom">
                    <h1 class="modal-title mx-auto fs-5" id="modalLogoutLabel">Perhatian!</h1>
                </div>
                <div class="modal-body text-center py-3">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success"
                        onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../../assets/js/aDaftarSidang.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
