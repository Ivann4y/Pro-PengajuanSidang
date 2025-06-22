<?php
session_start();
// Pastikan path ke file koneksi sudah benar
include "../../koneksi.php";

// Redirect jika bukan dosen
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}

// Cek koneksi ke database
if ($conn === false) {
    die("Koneksi ke database gagal: " . print_r(sqlsrv_errors(), true));
}

// --- LOGIKA FILTER, PAGINASI, DAN PENCARIAN (SERVER-SIDE) ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// --- MENGGUNAKAN CTE (Common Table Expression) untuk Query yang Efisien ---
$baseQuery = "
    WITH FullSidangData AS (
        SELECT
            s.id_sidang,
            s.id_kelompok,
            s.judul,
            s.jenis_sidang,
            
            (SELECT TOP 1 mk.nama_matkul
             FROM [dbo].[Detail_Sidang] ds
             JOIN [dbo].[MataKuliah] mk ON ds.id_matkul = mk.id_matkul
             WHERE ds.id_sidang = s.id_sidang) AS nama_matkul,
            
            (SELECT TOP 1 d.nama_dosen
             FROM [dbo].[Detail_Sidang] ds
             JOIN [dbo].[Dosen] d ON ds.nomor_dosen = d.nomor_dosen
             WHERE ds.id_sidang = s.id_sidang AND d.isPembimbing = 0x01) AS pembimbing
             
        FROM [dbo].[Sidang] s
    )
";

// --- MEMBUAT KLAUSA WHERE DINAMIS ---
$whereConditions = [];
$params = [];

if ($filter === 'ta') {
    $whereConditions[] = "jenis_sidang = 0x00";
} elseif ($filter === 'semester') {
    $whereConditions[] = "jenis_sidang = 0x01";
}

if (!empty($search)) {
    $whereConditions[] = "(CAST(id_kelompok AS VARCHAR(255)) LIKE ? OR nama_matkul LIKE ? OR pembimbing LIKE ?)";
    $likeParam = "%" . $search . "%";
    array_push($params, $likeParam, $likeParam, $likeParam);
}

$whereClause = "";
if (!empty($whereConditions)) {
    $whereClause = " WHERE " . implode(' AND ', $whereConditions);
}

// --- QUERY PENGHITUNGAN TOTAL DATA ---
$countQuery = $baseQuery . "SELECT COUNT(*) as total FROM FullSidangData" . $whereClause;
$countResult = sqlsrv_query($conn, $countQuery, $params);
if ($countResult === false) {
    die("Error saat menghitung total data: " . print_r(sqlsrv_errors(), true));
}
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// --- QUERY UTAMA UNTUK MENGAMBIL DATA ---
$query = $baseQuery . "SELECT * FROM FullSidangData" . $whereClause . " ORDER BY id_kelompok ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
$mainParams = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $query, $mainParams);
if ($result === false) {
    die("Error pada query utama: " . print_r(sqlsrv_errors(), true));
}

$nomor = $offset + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- SEMUA BAGIAN HEAD ANDA TETAP SAMA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <title>Dosen - Daftar Sidang</title>
    <style>
        /* SEMUA CSS ANDA TETAP SAMA */
        table{border-spacing:0 10px;border-collapse:separate;width:100%}thead{border-bottom:2px solid #000!important}thead th{padding:12px 15px;text-align:left}thead th:nth-child(1){text-align:center;width:5%}thead th:nth-child(2){width:20%}thead th:nth-child(3){width:35%}thead th:nth-child(4){width:25%}thead th:nth-child(5){text-align:center;width:15%}.isiTabel td{padding:12px 15px;font-family:"Poppins",sans-serif;font-weight:400;vertical-align:middle}.isiTabel td:first-child{border-radius:20px 0 0 20px;text-align:center}.isiTabel td:last-child{border-radius:0 20px 20px 0}.detail-btn{border:none!important;background-color:transparent!important;color:#4b68fb;padding:.25rem .5rem}.detail-btn:hover{opacity:.7}tr.jadiBiru:hover .detail-btn i{color:#fff!important}.dashboard-header{display:flex;justify-content:space-between;align-items:center;padding:0 15px;margin-bottom:30px}.dashboard-header .bodyHeading{font-weight:700;font-size:40px;font-family:"Poppins",sans-serif;margin:0;color:#1a1a1a}.search-input-group{background-color:#f3f4f6;border-radius:.5rem;overflow:hidden;width:300px;margin-top:.19vh -1px;margin-right:1vh}.search-input-group input.form-control{background-color:transparent;border:none;box-shadow:none;padding-left:1rem}.search-input-group .input-group-text{background-color:transparent;border:none;padding-right:0}.pagination-container{margin-top:2rem}.pagination .page-item.active .page-link{background-color:#4b68fb;border-color:#4b68fb;color:#fff;z-index:2}.pagination .page-link{color:#4b68fb}.pagination .page-link:hover{color:#2c45c9}.pagination .page-item.disabled .page-link{color:#6c757d}
    </style>
</head>
<body>
    <div id="NavSide">
        <!-- SEMUA BAGIAN SIDEBAR ANDA TETAP SAMA -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <!-- BAGIAN TOPBAR ANDA TETAP SAMA -->
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons d-flex d-md-none"><a href="mNotifikasi.php" title="Notifikasi" style="text-decoration:none;color:inherit"><i class="bi bi-bell-fill"></i></a><div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration:none;color:inherit"><i class="bi bi-person-fill fs-5"></i></a></div></div>
        </div>
        <main class="NavSide__main-content">
            <!-- BAGIAN HEADER ANDA TETAP SAMA -->
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
                                <?php
                                if ($filter === 'ta') echo 'Sidang TA';
                                elseif ($filter === 'semester') echo 'Sidang Semester';
                                else echo 'Semua';
                                ?>
                            </button>
                            <ul class="dropdown-menu rounded shadow">
                                <li><a class="dropdown-item" href="?filter=all&page=1&search=<?= urlencode($search) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=ta&page=1&search=<?= urlencode($search) ?>">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=semester&page=1&search=<?= urlencode($search) ?>">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <form method="GET" action="" class="search-input-group ms-auto d-flex align-items-center">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Kelompok, Matkul, Pembimbing..." value="<?= htmlspecialchars($search) ?>">
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
                            <?php if ($totalRecords > 0): ?>
                                <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                                    <tr class="isiTabel jadiBiru">
                                        <td><?= $nomor++ ?></td>
                                        <td><?= htmlspecialchars($row['id_kelompok']) ?></td>
                                        <td>
                                            <?php
                                            if ($row['jenis_sidang'] == '0x00') {
                                                echo htmlspecialchars($row['judul']);
                                            } else {
                                                echo htmlspecialchars($row['nama_matkul'] ?? $row['judul']);
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['pembimbing'] ?? 'Belum Ditentukan') ?></td>
                                        <td style="text-align: center;">
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
                                            <!-- PERBAIKAN DI SINI -->
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
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    </script>
</body>
</html>