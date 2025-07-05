<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nim'])) {
    die("KESALAHAN FATAL: NIM pengguna tidak ditemukan di sesi. Silakan login kembali.");
}
$nim_mahasiswa_logged_in = $_SESSION['nim'];

$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login atau bukan mahasiswa.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

include '../../koneksi/koneksiAndrew.php';
$success_message = '';
$error_message = '';

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_sidang']) && $_POST['delete_sidang'] === '1') {
    $id_sidang_to_delete = $_POST['id_sidang'];

    // Security check: Ensure the student is trying to delete their own draft
    $checkQuery = "
        SELECT COUNT(s.id_sidang) as total 
        FROM dbo.Sidang s
        JOIN dbo.Kelompok sg ON s.id_kelompok = sg.id_kelompok
        JOIN dbo.Kelompok k_student ON k_student.nomor_kelompok = sg.nomor_kelompok
            AND k_student.tahun_ajaran = sg.tahun_ajaran
            AND k_student.jenis_sidang = sg.jenis_sidang
            AND k_student.id_matkul = sg.id_matkul
        WHERE s.id_sidang = ? 
          AND k_student.nim = ?
          AND s.status_ajuan = 'Draft'
    ";
    $checkParams = [$id_sidang_to_delete, $nim_mahasiswa_logged_in];
    $checkStmt = sqlsrv_query($conn, $checkQuery, $checkParams);
    $can_delete = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)['total'] > 0;

    if ($can_delete) {
        sqlsrv_begin_transaction($conn);
        try {
            $sql_delete_detail = "DELETE FROM dbo.Detail_Sidang WHERE id_sidang = ?";
            sqlsrv_query($conn, $sql_delete_detail, [$id_sidang_to_delete]);

            $sql_delete_sidang = "DELETE FROM dbo.Sidang WHERE id_sidang = ?";
            sqlsrv_query($conn, $sql_delete_sidang, [$id_sidang_to_delete]);
            
            sqlsrv_commit($conn);
            $_SESSION['success_message'] = "Pengajuan draft berhasil dihapus.";

        } catch (Exception $e) {
            sqlsrv_rollback($conn);
            $_SESSION['error_message'] = "Error saat menghapus data.";
        }
    } else {
        $_SESSION['error_message'] = "Gagal menghapus: Anda tidak memiliki izin atau pengajuan bukan draft.";
    }
    
    header("Location: mPengajuan.php?filter=" . urlencode($_GET['filter'] ?? 'Semua') . "&page=" . urlencode($_GET['page'] ?? 1));
    exit();
}

// Pagination settings
$rowsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

// Filter settings
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Semua';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'Semua';

// Build filter conditions
$filterClause = '';
$statusClause = '';

if ($filter === 'TA') {
    $filterClause = " AND sg.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'Semester') {
    $filterClause = " AND sg.jenis_sidang = 'Semester'";
}

if ($status_filter === 'Draft') {
    $statusClause = " AND s.status_ajuan = 'Draft'";
} elseif ($status_filter === 'Pending') {
    $statusClause = " AND s.status_ajuan = 'Pending'";
} elseif ($status_filter === 'Approved') {
    $statusClause = " AND s.status_ajuan = 'Approved'";
} elseif ($status_filter === 'Rejected') {
    $statusClause = " AND s.status_ajuan = 'Rejected'";
} else {
    // Show all statuses
    $statusClause = "";
}

// Use the comprehensive approach that handles all student's groups
// Query untuk menghitung total baris
$countQuery = "
    SELECT COUNT(DISTINCT s.id_sidang) as total
    FROM dbo.Sidang s
    JOIN dbo.Kelompok sg ON s.id_kelompok = sg.id_kelompok
    JOIN dbo.Kelompok k_student ON k_student.nomor_kelompok = sg.nomor_kelompok
        AND k_student.tahun_ajaran = sg.tahun_ajaran
        AND k_student.jenis_sidang = sg.jenis_sidang
        AND k_student.id_matkul = sg.id_matkul
    WHERE k_student.nim = ?
    $filterClause
    $statusClause
";

$countParams = [$nim_mahasiswa_logged_in];
$countResult = sqlsrv_query($conn, $countQuery, $countParams);
if ($countResult === false) {
    die("Error in count query: " . print_r(sqlsrv_errors(), true));
}
$totalRows = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Debug: Log the query and parameters
error_log("mPengajuan Debug - NIM: $nim_mahasiswa_logged_in, Filter: $filter, Status: $status_filter, Total Rows: $totalRows");

if ($totalRows == 0) {
    $dataSidang = [];
} else {
    // Query utama untuk mengambil data pengajuan
    $query = "
        SELECT DISTINCT
            s.id_sidang, 
            s.judul, 
            s.status_ajuan,
            sg.nomor_kelompok,
            sg.jenis_sidang,
            sg.tahun_ajaran,
            mk.nama_matkul,
            d.nama_dosen AS nama_pembimbing
        FROM dbo.Sidang AS s
        JOIN dbo.Kelompok AS sg ON s.id_kelompok = sg.id_kelompok
        JOIN dbo.MataKuliah AS mk ON sg.id_matkul = mk.id_matkul
        JOIN dbo.Kelompok AS k_student ON k_student.nomor_kelompok = sg.nomor_kelompok
            AND k_student.tahun_ajaran = sg.tahun_ajaran
            AND k_student.jenis_sidang = sg.jenis_sidang
            AND k_student.id_matkul = sg.id_matkul
        LEFT JOIN dbo.Bimbingan AS b ON sg.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
        LEFT JOIN dbo.Dosen AS d ON b.nomor_dosen = d.nomor_dosen
        WHERE k_student.nim = ?
        $filterClause
        $statusClause
        ORDER BY s.id_sidang DESC
        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
    ";
    $params_main = [$nim_mahasiswa_logged_in, $offset, $rowsPerPage];
    $result = sqlsrv_query($conn, $query, $params_main);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $dataSidang = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $dataSidang[] = [
            "id_sidang" => $row['id_sidang'],
            "judul" => $row['judul'],
            "status_ajuan" => $row['status_ajuan'],
            "nomor_kelompok" => $row['nomor_kelompok'],
            "jenis_sidang" => $row['jenis_sidang'],
            "tahun_ajaran" => $row['tahun_ajaran'],
            "matkul" => $row['nama_matkul'],
            "dosen" => $row['nama_pembimbing'] ?? 'N/A'
        ];
    }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <h2 class="text-heading" style="color:black;"><?php echo isset($_SESSION['user_data']['nama_mhs']) ? htmlspecialchars($_SESSION['user_data']['nama_mhs']) : 'Mahasiswa'; ?> (Mahasiswa)</h2>
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
                                <li><a class="dropdown-item" href="?filter=Semua&status=<?= urlencode($status_filter) ?>&page=1">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=TA&status=<?= urlencode($status_filter) ?>&page=1">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=Semester&status=<?= urlencode($status_filter) ?>&page=1">Sidang Semester</a></li>
                            </ul>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMStatus">
                                <?php 
                                $statusLabels = [
                                    'Semua' => 'Semua',
                                    'Draft' => 'Draft',
                                    'Pending' => 'Pending',
                                    'Approved' => 'Diterima',
                                    'Rejected' => 'Ditolak'
                                ];
                                echo htmlspecialchars($statusLabels[$status_filter] ?? 'Semua');
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=<?= urlencode($filter) ?>&status=Semua&page=1">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=<?= urlencode($filter) ?>&status=Draft&page=1">Draft</a></li>
                                <li><a class="dropdown-item" href="?filter=<?= urlencode($filter) ?>&status=Pending&page=1">Pending</a></li>
                                <li><a class="dropdown-item" href="?filter=<?= urlencode($filter) ?>&status=Approved&page=1">Diterima</a></li>
                                <li><a class="dropdown-item" href="?filter=<?= urlencode($filter) ?>&status=Rejected&page=1">Ditolak</a></li>
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
                                    <th scope="col">Kelompok</th>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Mata Kuliah</th>
                                    <th scope="col">Dosen Pembimbing</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mSidangTableBody">
                                <?php if (empty($dataSidang)): ?>
                                    <tr class="isiTabel">
                                        <td colspan="7" class="text-center py-4">Tidak ada data untuk ditampilkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($dataSidang as $index => $sidang): ?>
                                        <tr class="isiTabel jadiBiru">
                                            <td><?php echo ($page - 1) * $rowsPerPage + $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($sidang['nomor_kelompok']); ?></td>
                                            <td><?php echo htmlspecialchars($sidang['judul']); ?></td>
                                            <td><?php echo htmlspecialchars($sidang['matkul']); ?></td>
                                            <td><?php echo htmlspecialchars($sidang['dosen']); ?></td>
                                            <td>
                                                <?php 
                                                $statusClass = '';
                                                $statusText = '';
                                                switch($sidang['status_ajuan']) {
                                                    case 'Draft':
                                                        $statusClass = 'badge bg-secondary';
                                                        $statusText = 'Draft';
                                                        break;
                                                    case 'Pending':
                                                        $statusClass = 'badge bg-warning';
                                                        $statusText = 'Pending';
                                                        break;
                                                    case 'Approved':
                                                        $statusClass = 'badge bg-success';
                                                        $statusText = 'Diterima';
                                                        break;
                                                    case 'Rejected':
                                                        $statusClass = 'badge bg-danger';
                                                        $statusText = 'Ditolak';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-secondary';
                                                        $statusText = 'Unknown';
                                                }
                                                ?>
                                                <span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                            </td>
                                            <td class="text-center">
                                            <!-- The form now wraps the container for proper submission -->
                                            <form method="post" class="d-inline-block">
                                                <!-- Hidden inputs stay inside the form -->
                                                <input type="hidden" name="id_sidang" value="<?php echo $sidang['id_sidang']; ?>">
                                                <input type="hidden" name="delete_sidang" value="">

                                                <!-- Flex container for the buttons -->
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <?php if ($sidang['status_ajuan'] === 'Draft'): ?>
                                                        <!-- EDIT BUTTON - Only for Draft -->
                                                        <button type="submit" formaction="mEditPengajuan.php" class="btn btn-link p-0" title="Edit Pengajuan">
                                                            <i class="fa-solid fa-file-signature fs-5"></i>
                                                        </button>

                                                        <!-- DELETE BUTTON - Only for Draft -->
                                                        <button type="button" class="btn btn-link p-0 ms-3 delete-btn" title="Hapus Pengajuan">
                                                            <i class="fa-solid fa-trash fs-5"></i>
                                                        </button>
                                                    <?php elseif ($sidang['status_ajuan'] === 'Rejected'): ?>
                                                        <!-- CREATE NEW BUTTON - For Rejected -->
                                                        <button type="button" class="btn btn-link p-0" title="Buat Pengajuan Baru" onclick="createNewPengajuan(<?php echo $sidang['nomor_kelompok']; ?>, '<?php echo $sidang['jenis_sidang']; ?>', <?php echo $sidang['tahun_ajaran']; ?>)">
                                                            <i class="fa-solid fa-plus fs-5"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <!-- VIEW BUTTON - For other statuses -->
                                                        <button type="button" class="btn btn-link p-0" title="Lihat Detail" onclick="viewDetail(<?php echo $sidang['id_sidang']; ?>)">
                                                            <i class="fa-solid fa-eye fs-5"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
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
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?filter=<?php echo urlencode($filter); ?>&status=<?php echo urlencode($status_filter); ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
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
                                            echo "<li class='page-item'><a class='page-link' href='?filter=" . urlencode($filter) . "&status=" . urlencode($status_filter) . "&page=1'>1</a></li>";
                                            if ($startPage > 2) {
                                                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                                            }
                                        }

                                        // Page numbers
                                        for ($i = $startPage; $i <= $endPage; $i++) {
                                            echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'>";
                                            echo "<a class='page-link' href='?filter=" . urlencode($filter) . "&status=" . urlencode($status_filter) . "&page=$i'>$i</a>";
                                            echo "</li>";
                                        }

                                        // Last page
                                        if ($endPage < $totalPages) {
                                            if ($endPage < $totalPages - 1) {
                                                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                                            }
                                            echo "<li class='page-item'><a class='page-link' href='?filter=" . urlencode($filter) . "&status=" . urlencode($status_filter) . "&page=$totalPages'>$totalPages</a></li>";
                                        }
                                        ?>

                                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?filter=<?php echo urlencode($filter); ?>&status=<?php echo urlencode($status_filter); ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
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

            window.tambahData = function () {
                window.location.href = 'mTambahPengajuan.php';
            };
            
            window.createNewPengajuan = function (nomorKelompok, jenisSidang, tahunAjaran) {
                window.location.href = `mTambahPengajuan.php?nomor_kelompok=${nomorKelompok}&jenis_sidang=${jenisSidang}&tahun_ajaran=${tahunAjaran}`;
            };
            
            window.viewDetail = function (idSidang) {
                window.location.href = `mDetailPengajuan.php?id_sidang=${idSidang}`;
            };
            
            <?php if (isset($_SESSION['success_message'])): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?php echo addslashes($_SESSION['success_message']); ?>',
                icon: 'success',
                confirmButtonColor: '#4B68FB'
            });
            <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            Swal.fire({
                title: 'Gagal!',
                text: '<?php echo addslashes($_SESSION['error_message']); ?>',
                icon: 'error',
                confirmButtonColor: '#4B68FB'
            });
            <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            // Handle delete button clicks
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault(); 
                    const form = this.closest('form'); 
                    
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Pengajuan draft ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Dynamically set the form for deletion
                            form.action = ''; // Set action to submit to the current page
                            form.querySelector('input[name="delete_sidang"]').value = '1'; // Activate the delete flag
                            form.submit(); // Now submit the correctly configured form
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>