<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. Cek jika role pengguna BUKAN 'mahasiswa'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

include '../../koneksi/koneksiAndrew.php';
$success_message = '';
$error_message = '';
$nim_mahasiswa_logged_in = $_SESSION['user_data']['nim'];

// Pagination settings
$rowsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

// Filter settings
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Semua';
$filterClause = '';
if ($filter === 'TA') {
    $filterClause = " AND s.jenis_sidang = 0";
} elseif ($filter === 'Semester') {
    $filterClause = " AND s.jenis_sidang = 1";
}

// Count total rows for pagination 
$countQuery = "
    SELECT COUNT(DISTINCT s.id_sidang) as total
    FROM Sidang s
    JOIN Bimbingan b ON s.id_kelompok = b.id_kelompok
    JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
    WHERE b.isPembimbing = 0x01
    $filterClause
";
$countResult = sqlsrv_query($conn, $countQuery);
if ($countResult === false) {
    die(print_r(sqlsrv_errors(), true));
}
$totalRows = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Main query with pagination and filter
$query = "
    SELECT DISTINCT
        s.id_sidang, 
        s.judul, 
        s.jenis_sidang, 
        s.id_kelompok, 
        d.nama_dosen, 
        m.nama_matkul
    FROM Sidang s
    JOIN Bimbingan b ON s.id_kelompok = b.id_kelompok
    JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
    LEFT JOIN MataKuliah m ON ds.id_matkul = m.id_matkul
    WHERE b.isPembimbing = 0x01
    $filterClause
    ORDER BY s.id_sidang
    OFFSET $offset ROWS FETCH NEXT $rowsPerPage ROWS ONLY
";

$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

$dataSidang = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $dataSidang[] = [
        "id_sidang" => $row['id_sidang'],
        "judul" => $row['judul'],
        "matkul" => $row['nama_matkul'] ?? 'Tidak ada mata kuliah',
        "dosen" => $row['nama_dosen'],
        "jenis_sidang" => is_null($row['jenis_sidang']) ? null : (int)$row['jenis_sidang']
    ];
}
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
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php echo htmlspecialchars($filter === 'TA' ? 'Sidang TA' : ($filter === 'Semester' ? 'Sidang Semester' : 'Semua')); ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=Semua&page=1">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=TA&page=1">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=Semester&page=1">Sidang Semester</a></li>
                            </ul>
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
                            <tbody id="mSidangTableBody">
                                <?php if (empty($dataSidang)): ?>
                                    <tr class="isiTabel">
                                        <td colspan="5" class="text-center py-4">Tidak ada data untuk ditampilkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($dataSidang as $index => $sidang): ?>
                                        <tr class="isiTabel jadiBiru">
                                            <td><?php echo ($page - 1) * $rowsPerPage + $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($sidang['judul']); ?></td>
                                            <td><?php echo htmlspecialchars($sidang['matkul']); ?></td>
                                            <td><?php echo htmlspecialchars($sidang['dosen']); ?></td>
                                            <td>
                                                <i class="fa-solid fa-file-signature" style="cursor: pointer;" onclick="editData(<?php echo $sidang['id_sidang']; ?>, '<?php echo htmlspecialchars($sidang['matkul']); ?>', '<?php echo htmlspecialchars($sidang['judul']); ?>')"></i>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody> 
                        </table>
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($totalPages > 1): ?>
                                        <!-- Previous Button -->
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?filter=<?php echo urlencode($filter); ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                                <span aria-hidden="true">«</span>
                                            </a>
                                        </li>

                                        <?php
                                        // Calculate page range
                                        $startPage = max(1, $page - 1);
                                        $endPage = min($totalPages, $page + 1);

                                        if ($page <= 2) {
                                            $endPage = min($totalPages, 3);
                                        } elseif ($page >= $totalPages - 1) {
                                            $startPage = max(1, $totalPages - 2);
                                        }

                                        // First page
                                        if ($startPage > 1) {
                                            echo "<li class='page-item'><a class='page-link' href='?filter=" . urlencode($filter) . "&page=1'>1</a></li>";
                                            if ($startPage > 2) {
                                                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                                            }
                                        }

                                        // Page numbers
                                        for ($i = $startPage; $i <= $endPage; $i++) {
                                            echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'>";
                                            echo "<a class='page-link' href='?filter=" . urlencode($filter) . "&page=$i'>$i</a>";
                                            echo "</li>";
                                        }

                                        // Last page
                                        if ($endPage < $totalPages) {
                                            if ($endPage < $totalPages - 1) {
                                                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                                            }
                                            echo "<li class='page-item'><a class='page-link' href='?filter=" . urlencode($filter) . "&page=$totalPages'>$totalPages</a></li>";
                                        }
                                        ?>

                                        <!-- Next Button -->
                                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?filter=<?php echo urlencode($filter); ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                                                <span aria-hidden="true">»</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
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

            // Edit and Add functions
            window.editData = function (idSidang, matkul, judul) {
                window.location.href = `mEditPengajuan.php?id_sidang=${idSidang}&matkul=${encodeURIComponent(matkul)}&judul=${encodeURIComponent(judul)}`;
            };

            window.tambahData = function () {
                window.location.href = 'mTambahPengajuan.php';
            };
        });
    </script>
</body>
</html>