<?php

session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}

include "../../koneksi/koneksiAndrew.php";

if ($conn === false) { die("Koneksi gagal: " . print_r(sqlsrv_errors(), true)); }

// --- SIMULASI LOGIN (Anda bisa ganti dengan data dari session) ---
// $nomor_dosen_login = '1002'; 
// Hapus atau beri komentar pada baris simulasi
// $nomor_dosen_login = '1001'; 
// --- SIMULASI LOGIN (Anda bisa ganti dengan data dari session) ---
// $nomor_dosen_login = '1002'; // atau '1001', '1005', dll.


// Periksa apakah data pengguna ada di session
if (!isset($_SESSION['user_data']) || !isset($_SESSION['user_data']['nomor_dosen'])) {
    // Jika karena suatu hal session tidak ada, redirect ke logout
    header("Location: ../../logout.php");
    exit();
}
// Ambil nomor dosen dari dalam array 'user_data'
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen'];

// --- LOGIKA FILTER & PAGINasi ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// --- BASE QUERY ---
$baseQuery = "FROM Vw_DaftarLengkapSidang";

// --- WHERE CLAUSE ---
$whereConditions = [];
$paramsForWhere = [];
$whereConditions[] = "nomor_dosen_terlibat = ?";
array_push($paramsForWhere, $nomor_dosen_login);
$whereClause = " WHERE " . implode(' AND ', $whereConditions);

// --- HAVING CLAUSE ---
$havingConditions = [];
$paramsForHaving = [];

// DIPERBAIKI: Filter menggunakan kolom 'kategori_sidang'
if ($filter === 'ta') {
    $havingConditions[] = "(MAX(kategori_sidang) = ?)"; 
    array_push($paramsForHaving, 'Tugas Akhir');
} elseif ($filter === 'semester') {
    $havingConditions[] = "(MAX(kategori_sidang) != ?)";
    array_push($paramsForHaving, 'Tugas Akhir');
}

if (!empty($search)) {
    // DIPERBAIKI: Search menggunakan kolom 'judul_tampil'
    $havingConditions[] = "(CAST(id_kelompok AS VARCHAR(255)) LIKE ? OR MAX(judul_tampil) LIKE ? OR MAX(nama_pembimbing_utama) LIKE ?)";
    $likeParam = "%" . $search . "%";
    array_push($paramsForHaving, $likeParam, $likeParam, $likeParam);
}

$havingClause = "";
if (!empty($havingConditions)) {
    $havingClause = " HAVING " . implode(' AND ', $havingConditions);
}

$allParams = array_merge($paramsForWhere, $paramsForHaving);

// --- QUERY PENGHITUNGAN TOTAL DATA ---
$countQuery = "
    SELECT COUNT(*) as total FROM (
        SELECT id_sidang
        " . $baseQuery . $whereClause . "
        GROUP BY id_sidang, id_kelompok
        " . $havingClause . "
    ) AS CountSubQuery;
";
$countResult = sqlsrv_query($conn, $countQuery, $allParams);
if ($countResult === false) { die("Error saat menghitung total data: " . print_r(sqlsrv_errors(), true)); }
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);
if($totalPages == 0) $totalPages = 1;

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $rowsPerPage;
}

// --- QUERY UTAMA ---
$mainQuery = "
    SELECT 
        id_sidang, 
        id_kelompok, 
        MAX(judul_tampil) as judul_tampil, -- DIPERBAIKI: Ambil kolom 'judul_tampil'
        MAX(nama_pembimbing_utama) as nama_pembimbing_utama
    " . $baseQuery . $whereClause . " 
    GROUP BY 
        id_sidang, id_kelompok
    " . $havingClause . "
    ORDER BY 
        id_kelompok ASC 
    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";

$mainParams = array_merge($allParams, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $mainQuery, $mainParams);
if ($result === false) { die("Error pada query utama: " . print_r(sqlsrv_errors(), true)); }

$nomor = $offset + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosen - Daftar Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/dDaftarSidang.css">
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons d-flex d-md-none"><a href="mNotifikasi.php" title="Notifikasi" style="text-decoration:none;color:inherit"><i class="bi bi-bell-fill"></i></a><div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration:none;color:inherit"><i class="bi bi-person-fill fs-5"></i></a></div></div>
        </div>
        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Daftar Sidang</h2>
                <div class="header-icons d-none d-md-flex"><a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a><div class="profile-icon"><a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color:#fff"></i></a></div></div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <label class="fw-semibold mb-0">Filter:</label>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php if ($filter === 'ta') echo 'Sidang TA'; elseif ($filter === 'semester') echo 'Sidang Semester'; else echo 'Semua'; ?>
                            </button>
                            <ul class="dropdown-menu rounded shadow">
                                <li><a class="dropdown-item" href="?filter=all&search=<?= urlencode($search) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=ta&search=<?= urlencode($search) ?>">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=semester&search=<?= urlencode($search) ?>">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <form method="GET" action="" class="search-input-group ms-auto d-flex align-items-center">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Kelompok, Matkul..." value="<?= htmlspecialchars($search) ?>">
                        </form>
                    </div>
                </div>
                <div class="row">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kelompok</th>
                                <th scope="col">Judul/Mata Kuliah</th>
                                <th scope="col">Pembimbing</th>
                                <th scope="col" style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalRecords > 0 && $result && sqlsrv_has_rows($result)): ?>
                                <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                                    <tr class="isiTabel jadiBiru">
                                        <td data-label="No"><?= $nomor++ ?></td>
                                        <td data-label="Kelompok"><?= htmlspecialchars($row['id_kelompok']) ?></td>
                                        <!-- DIPERBAIKI: Menampilkan kolom 'judul_tampil' -->
                                        <td data-label="Judul/Mata Kuliah"><?= htmlspecialchars($row['judul_tampil']) ?></td>
                                        <td data-label="Pembimbing"><?= htmlspecialchars($row['nama_pembimbing_utama'] ?? 'Belum Ditentukan') ?></td>
                                        <td data-label="Aksi" style="text-align: center;">
                                            <a href="dEvaluasiSidang.php?id=<?= $row['id_sidang'] ?>" class="detail-btn">
                                                <i class="fa-solid fa-file-signature"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($totalPages > 1): ?>
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $currentPage - 1 ?>">«</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $currentPage + 1 ?>">»</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div style="background-color: rgb(67, 54, 240);"><div class="modal-header"><h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1></div></div><div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div><div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button></div></div></div>
    </div>
    <script src="/Projek/Pro-PengajuanSidang/assets/js/dDaftarSidang.js"></script>
</body>
</html>