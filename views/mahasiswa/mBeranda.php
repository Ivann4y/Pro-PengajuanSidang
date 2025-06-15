<?php
session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/mahasiswa-dashboard.css">
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons"><a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;"><i class="bi bi-bell-fill"></i></a>
                <div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;"><i class="bi bi-person-fill fs-5"></i></a></div>
            </div>
        </div>

        <main class="NavSide__main-content" id="mBeranda">
            <div class="dashboard-header">
                <h2 class="page-title" style="color:#1F2937">Beranda - Mahasiswa</h2>
                <div class="header-icons d-none d-md-flex"><a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon"><a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
                </div>
            </div>

            <h1 class="welcome-text">Selamat Datang!</h1>

            <div class="row">
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <a href="mSidang.php" style="text-decoration: none; color: inherit;">
                                <div class="dashboard-card sidang-status-card w-100">
                                    <div class="number">0</div>
                                    <div class="text"><span class="title">Sidang</span><span class="description">Sedang Berlangsung</span></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="mPengajuan.php" style="text-decoration: none; color: inherit;">
                                <div class="dashboard-card penilaian-status-card">
                                    <div class="number">0</div>
                                    <div class="text"><span class="title">Penilaian</span><span class="description">Menunggu untuk Dinilai</span></div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="dashboard-card content-card tanggungan-card">
                        <h3 class="section-title">Tugas</h3>
                        <!-- Tugas list will be rendered by JavaScript -->
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="dashboard-card calendar-card">
                        <div class="section-title-container">
                            <div class="calendar-nav">
                                <i class="bi bi-chevron-left" id="prevMonth"></i>
                                <h3 class="section-title" id="currentMonthYear"></h3>
                                <i class="bi bi-chevron-right" id="nextMonth"></i>
                            </div>
                        </div>
                        <table class="calendar" id="calendarTable">
                            <thead>
                                <tr>
                                    <th>Min</th>
                                    <th>Sen</th>
                                    <th>Sel</th>
                                    <th>Rab</th>
                                    <th>Kam</th>
                                    <th>Jum</th>
                                    <th>Sab</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Calendar will be rendered by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="dashboard-card content-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
                        <!-- Sidang mendatang will be rendered by JavaScript -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal and JS remain the same -->
    <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color:#4B68FB;">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <?php include '../../assets/js/mahasiswa-dashboard.php'; ?>
</body>

</html>