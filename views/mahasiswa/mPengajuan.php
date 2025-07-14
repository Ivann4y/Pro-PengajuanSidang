<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek login dan role, ini sudah benar
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || 
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: ../../index.php");
    exit();
}

require_once '../../koneksi/koneksiAndrew.php';
$nim = $_SESSION['user_data']['nim'];

// Flash message check
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

// Ambil semua parameter filter dari URL
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Pending';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
// [PENAMBAHAN] Ambil parameter untuk filter jenis sidang
$jenisFilter = isset($_GET['jenis']) ? $_GET['jenis'] : 'Semua';


// Query SQL utama
$querySource = "
(
    SELECT
        sg.nomor_kelompok, sg.tahun_ajaran, sg.jenis_sidang, sg.id_matkul,
        m.nama_matkul,
        s.id_sidang, s.judul,
        ISNULL(s.status_ajuan, 'Belum Ada Pengajuan') AS status_ajuan,
        s.waktu_pengumpulan
    FROM (
        SELECT DISTINCT
            k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, k.id_matkul
        FROM Kelompok k
        WHERE k.nim = ?
    ) AS sg
    JOIN MataKuliah m ON sg.id_matkul = m.id_matkul
    OUTER APPLY (
        SELECT TOP 1
            s_inner.id_sidang, s_inner.judul, s_inner.status_ajuan, s_inner.waktu_pengumpulan
        FROM Kelompok k_inner
        JOIN Sidang s_inner ON k_inner.id_kelompok = s_inner.id_kelompok
        WHERE k_inner.nomor_kelompok = sg.nomor_kelompok
            AND k_inner.tahun_ajaran = sg.tahun_ajaran
            AND k_inner.jenis_sidang = sg.jenis_sidang
            AND k_inner.id_matkul = sg.id_matkul
        ORDER BY
            CASE s_inner.status_ajuan WHEN 'Draft' THEN 1 WHEN 'Rejected' THEN 2 ELSE 3 END,
            s_inner.id_sidang DESC
    ) s
) AS FullQuery
";

// Bangun klausa WHERE secara dinamis
$whereConditions = [];
$params_where = []; 

// Filter berdasarkan status
if ($statusFilter === 'Pending') {
    $whereConditions[] = "status_ajuan IN ('Pending', 'Draft', 'Belum Ada Pengajuan')";
} else if (in_array($statusFilter, ['Rejected', 'Approved', 'Semua'])) {
    if ($statusFilter !== 'Semua') {
        $whereConditions[] = "status_ajuan = ?";
        $params_where[] = $statusFilter;
    }
}

// [PENAMBAHAN] Tambahkan kondisi untuk filter jenis sidang
if ($jenisFilter !== 'Semua') {
    $whereConditions[] = "jenis_sidang = ?";
    $params_where[] = $jenisFilter;
}

// Filter berdasarkan pencarian
if (!empty($search)) {
    $whereConditions[] = "(judul LIKE ? OR nama_matkul LIKE ? OR status_ajuan LIKE ?)";
    $likeParam = "%" . $search . "%";
    array_push($params_where, $likeParam, $likeParam, $likeParam);
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Logika Paginasi
$rowsPerPage = 6;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $rowsPerPage;

$countSql = "SELECT COUNT(*) as total FROM " . $querySource . " " . $whereClause;
$countParams = array_merge([$nim], $params_where);
$countStmt = sqlsrv_query($conn, $countSql, $countParams);
if ($countStmt === false) { die("Error calculating total records: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>"); }
$totalCards = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'] ?? 0;
$totalPages = $totalCards > 0 ? ceil($totalCards / $rowsPerPage) : 1;

$mainSql = "SELECT * FROM " . $querySource . " " . $whereClause . "
            ORDER BY tahun_ajaran DESC, nama_matkul, nomor_kelompok ASC
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            
$mainParams = array_merge([$nim], $params_where, [$offset, $rowsPerPage]);
$stmt = sqlsrv_query($conn, $mainSql, $mainParams);
if ($stmt === false) { die("Error executing main query: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>"); }

$cardsToShow = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $cardsToShow[] = $row;
}

// Ambil jumlah notifikasi belum dibaca
$unread_count = 0;
if (isset($conn) && $conn) {
    $query_unread = "SELECT COUNT(*) as cnt FROM notifikasi WHERE penerima = ? AND (status_baca = 0 OR status_baca IS NULL)";
    $stmt_unread = sqlsrv_query($conn, $query_unread, array($nim));
    if ($stmt_unread && ($row = sqlsrv_fetch_array($stmt_unread, SQLSRV_FETCH_ASSOC))) {
        $unread_count = (int)$row['cnt'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswa - Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/mPengajuan.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
        .notif-badge {
            position: absolute;
            top: -2px;
            right: -8px;
            background: #4b68fb;
            color: white;
            border-radius: 50%;
            font-size: 0.55em;
            padding: 0 3px;
            z-index: 10;
            border: 2px solid white;
            font-weight: bold;
            min-width: 10px;
            text-align: center;
            line-height: 1.2;
            box-shadow: 0 0 2px #0002;
        }
    .position-relative { position: relative; }
</style>

</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons"><a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;"><i class="bi bi-bell-fill position-relative"><?php if ($unread_count > 0): ?><span class="notif-badge"> <?= $unread_count ?> </span><?php endif; ?></i></a>
                <div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;"><i class="bi bi-person-fill fs-5"></i></a></div>
            </div>
        </div>

        <main class="NavSide__main-content" id="mPengajuan">
            <div class="container-fluid">
                <div class="dashboard-header">
                    <h2 class="text-heading" style="color:black;">Pengajuan Sidang Anda</h2>
                    <div class="header-icons d-none d-md-flex"><a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill position-relative"><?php if ($unread_count > 0): ?><span class="notif-badge"> <?= $unread_count ?> </span><?php endif; ?></i></a>
                        <div class="profile-icon"><a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white;"></i></a></div>
                    </div>
                </div>
            
                <?php if ($flash_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flash_message) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <form action="mPengajuan.php" method="GET">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
                    <input type="hidden" name="jenis" value="<?= htmlspecialchars($jenisFilter) ?>">

                    <div class="d-flex align-items-center mb-3">
                        
                        <span class="fw-semibold me-2">Status:</span>
                        <div class="dropdown me-3"> <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                    if ($statusFilter === 'Pending') echo 'Pengajuan Aktif';
                                    elseif ($statusFilter === 'Approved') echo 'Disetujui';
                                    elseif ($statusFilter === 'Rejected') echo 'Ditolak';
                                    else echo 'Lihat Semua';
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?status=Pending&jenis=<?= urlencode($jenisFilter) ?>&search=<?= urlencode($search) ?>">Pengajuan Aktif</a></li>
                                <li><a class="dropdown-item" href="?status=Approved&jenis=<?= urlencode($jenisFilter) ?>&search=<?= urlencode($search) ?>">Disetujui</a></li>
                                <li><a class="dropdown-item" href="?status=Rejected&jenis=<?= urlencode($jenisFilter) ?>&search=<?= urlencode($search) ?>">Ditolak</a></li>
                                <li><a class="dropdown-item" href="?status=Semua&jenis=<?= urlencode($jenisFilter) ?>&search=<?= urlencode($search) ?>">Lihat Semua</a></li>
                            </ul>
                        </div>

                        <span class="fw-semibold me-2">Filter:</span>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                    if ($jenisFilter === 'Tugas Akhir') echo 'Tugas Akhir';
                                    elseif ($jenisFilter === 'Semester') echo 'Semester';
                                    else echo 'Semua';
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?jenis=Semua&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?jenis=Tugas Akhir&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>">Tugas Akhir</a></li>
                                <li><a class="dropdown-item" href="?jenis=Semester&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($search) ?>">Semester</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-0" name="search" placeholder="Cari berdasarkan judul, mata kuliah, atau status..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                    </div>

                    <button type="submit" style="display:none;"></button>
                </form>

                <div id="pengajuan-list">
                    <?php if (empty($cardsToShow)): ?>
                        <div class="text-center text-muted py-5 flex-grow-1">
                            <p>Tidak ada data pengajuan.</p>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($cardsToShow as $p): ?>
                                <div class="col pengajuan-card-wrapper">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= htmlspecialchars($p['judul'] ?? 'Belum Ada Judul') ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                Kelompok <?= htmlspecialchars($p['nomor_kelompok']) ?> - <?= htmlspecialchars($p['nama_matkul']) ?>
                                            </h6>
                                            <p class="card-text small">
                                                Tahun Ajaran: <?= htmlspecialchars($p['tahun_ajaran']) ?><br>
                                                Jenis Sidang: <?= htmlspecialchars($p['jenis_sidang']) ?>
                                            </p>
                                            
                                            <?php
                                            $status = $p['status_ajuan'];
                                            $badge_class = '';
                                            switch ($status) {
                                                case 'Draft': $badge_class = 'bg-info text-dark'; break;
                                                case 'Pending': $badge_class = 'bg-warning text-dark'; break;
                                                case 'Approved': $badge_class = 'bg-success'; break;
                                                case 'Rejected': $badge_class = 'bg-danger'; break;
                                                default: $badge_class = 'bg-secondary';
                                            }
                                            ?>
                                            <p class="mt-auto pt-2"><strong>Status:</strong> <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($status) ?></span></p>
                                        </div>
                                        <div class="card-footer bg-white border-top-0 text-end pb-3">
                                            <?php
                                            $link = "mKelolaPengajuan.php?nomor_kelompok=" . urlencode($p['nomor_kelompok']) .
                                                    "&tahun_ajaran=" . urlencode($p['tahun_ajaran']) .
                                                    "&jenis_sidang=" . urlencode($p['jenis_sidang']) .
                                                    "&id_matkul=" . urlencode($p['id_matkul']);
                                            switch ($p['status_ajuan']) {
                                                case 'Draft':
                                                    echo "<a href='{$link}' class='btn btn-primary btn-sm'><i class='fas fa-pencil-alt me-2'></i>Lanjutkan Pengajuan</a>";
                                                    break;
                                                case 'Rejected':
                                                    echo "<a href='{$link}' class='btn btn-warning btn-sm'><i class='fas fa-pencil-alt me-2'></i>Edit Pengajuan</a>";
                                                    break;
                                                case 'Belum Ada Pengajuan':
                                                    echo "<a href='{$link}' class='btn btn-success btn-sm'><i class='fas fa-plus me-2'></i>Buat Pengajuan</a>";
                                                    break;
                                                case 'Approved':
                                                case 'Pending':
                                                    echo "<a href='{$link}' class='btn btn-outline-info btn-sm'><i class='fas fa-eye me-2'></i>Lihat Detail</a>";
                                                    break;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php
                                // [PENYESUAIAN] Parameter 'jenis' ditambahkan ke link paginasi
                                $queryParams = "jenis=" . urlencode($jenisFilter) . "&status=" . urlencode($statusFilter) . "&search=" . urlencode($search);
                                ?>
                                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&<?= $queryParams ?>">«</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&<?= $queryParams ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&<?= $queryParams ?>">»</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar Toggle Logic
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            if (menuToggle) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            // JavaScript untuk sinkronisasi link dropdown dengan search input
            const searchInput = document.querySelector('input[name="search"]');
            const allDropdownLinks = document.querySelectorAll('.dropdown-menu a.dropdown-item');

            if (searchInput && allDropdownLinks.length > 0) {
                searchInput.addEventListener('input', function() {
                    const newSearchQuery = this.value;
                    allDropdownLinks.forEach(link => {
                        const url = new URL(link.href);
                        url.searchParams.set('search', newSearchQuery);
                        link.href = url.toString();
                    });
                });
            }
        });
    </script>
</body>
</html>