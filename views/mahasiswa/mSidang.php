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

if (isset($_GET['action']) && $_GET['action'] === 'set_sidang_session' && isset($_GET['id_sidang']) && is_numeric($_GET['id_sidang'])) {
    $id_sidang_from_get = (int)$_GET['id_sidang'];

    $checkQuery = "SELECT id_sidang FROM Sidang WHERE id_sidang = ?";
    $checkStmt = sqlsrv_prepare($conn, $checkQuery, array($id_sidang_from_get));

    if ($checkStmt === false) {
        header("Location: mSidang.php?error=query_check");
        exit();
    }

    if (!sqlsrv_execute($checkStmt)) {
        header("Location: mSidang.php?error=execute_check");
        exit();
    }

    if (sqlsrv_has_rows($checkStmt)) {
        $_SESSION['selected_sidang_id'] = $id_sidang_from_get;
        header("Location: mdetailSidang.php");
        exit();
    } else {
        header("Location: mSidang.php?error=sidang_not_found");
        exit();
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = 10;
$countQuery = "SELECT COUNT(DISTINCT s.id_sidang) as total 
                FROM Sidang s
                JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang 
                JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
                JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen";

if ($filter === 'ta') {
    $countQuery .= " WHERE s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $countQuery .= " WHERE s.jenis_sidang = 1";
}

$countResult = sqlsrv_query($conn, $countQuery);

if ($countResult === false) {
    echo "Terjadi kesalahan saat mengeksekusi countQuery:<br>";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
            echo "Code: " . $error['code'] . "<br>";
            echo "Message: " . $error['message'] . "<br>";
        }
    }
    exit();
}

$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

$query = "SELECT s.id_sidang, s.judul, s.jenis_sidang, m.nama_matkul, MIN(d.nama_dosen) AS dosen 
          FROM Sidang s
          JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang 
          JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
          JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen";

if ($filter === 'ta') {
    $query .= " WHERE s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $query .= " WHERE s.jenis_sidang = 1";
}

$query .= " GROUP BY s.id_sidang, s.judul, s.jenis_sidang, m.nama_matkul ORDER BY s.id_sidang";

$query .= " OFFSET " . (($currentPage - 1) * $rowsPerPage) . " ROWS FETCH NEXT " . $rowsPerPage . " ROWS ONLY";

$result = sqlsrv_query($conn, $query);

if ($result === false) {
    echo "Terjadi kesalahan saat mengeksekusi main query:<br>";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
            echo "Code: " . $error['code'] . "<br>";
            echo "Message: " . $error['message'] . "<br>";
        }
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa - Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/msidang.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <style>
        body {
            background-color: #ffffff;
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
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
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

        <main class="NavSide__main-content" id="mSidang">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="dashboard-header">
                    <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
                    <div class="header-icons d-none d-md-flex">
                        <a href="aNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                        <div class="profile-icon">
                            <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                        </div>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 d-flex align-items-center"> 
                        <label for="ddMsidang" class="fw-semibold mb-0">Filter: </label>
                        <div class="dropdown ms-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php
                                switch($filter) {
                                    case 'ta':
                                        echo "Sidang TA";
                                        break;
                                    case 'semester':
                                        echo "Sidang Semester";
                                        break;
                                    default:
                                        echo "Semua";
                                }
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=all&page=1">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=ta&page=1">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=semester&page=1">Sidang Semester</a></li>
                            </ul>
                        </div>
                    </div>
                </div><br><br>
                <div class="row table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen Pembimbing</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = ($currentPage - 1) * $rowsPerPage + 1;
                            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): 
                            ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $counter ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td>
                                        <a href="?action=set_sidang_session&id_sidang=<?= $row['id_sidang'] ?>" class="detail-btn">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                            $counter++;
                            endwhile; 
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($totalPages > 1): ?>
                                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?filter=<?= $filter ?>&page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="?filter=<?= $filter ?>&page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?filter=<?= $filter ?>&page=<?= $currentPage + 1 ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        if (menuToggle) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>