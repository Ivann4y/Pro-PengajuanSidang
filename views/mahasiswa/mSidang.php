<?php
require_once '../../control/mahasiswa/mSidang_logic.php';
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
    <link rel="stylesheet" href="../../assets/css/breadcrumb.css">
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
            <?php 
            // Include the function file
            require_once '../../control/function.php'; 
            // Generate breadcrumb
            echo generateBreadcrumb(getPageTitle('mSidang'), 'mahasiswa'); 
            ?>
            <div class="container-fluid"> 
                <div class="row">
                    <div class="dashboard-header">
                        <h2 class="text-heading"><?php echo isset($_SESSION['user_data']['nama_mhs']) ? htmlspecialchars($_SESSION['user_data']['nama_mhs']) : 'Mahasiswa'; ?> (Mahasiswa)</h2>
                        <div class="header-icons d-none d-md-flex">
                            <a href="mNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                            <div class="profile-icon">
                                <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
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
                            foreach ($rows as $row): 
                            ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $counter ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td>
                                        <form action="mdetailSidang.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="id_sidang" value="<?= $row['id_sidang'] ?>">
                                            <button type="submit" class="detail-btn" style="border: none; background: none; padding: 0; cursor: pointer;">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php 
                            $counter++;
                            endforeach; 
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