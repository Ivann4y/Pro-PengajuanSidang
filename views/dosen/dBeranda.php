<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}
include "../../koneksi/koneksiAndrew.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <link rel="stylesheet" href="../../assets/css/dBeranda.css">

</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle" id="sidebarToggleMobile">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <a href="#" class="notification-icon" title="Notifikasi">
                    <i class="bi bi-bell-fill fs-5"></i>
                </a>
                <div class="profile-icon"><a href="dProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
            </div>
        </div>

        <div class="NavSide__main-content" id="mainContent">
            <div class="header-icons-desktop d-none d-lg-flex">
                <a href="#" class="notification-icon" title="Notifikasi">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon-desktop">
                    <div class="profile-icon"><a href="dProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
                    <!-- <i class="bi bi-person-fill"></i> -->
                </div>
            </div>
            <div class="dashboardTitle">Beranda Dosen</div>
            <h2 class="welcomeText">Selamat Datang, <?php echo isset($_SESSION['user_data']['nama_dosen']) ? htmlspecialchars($_SESSION['user_data']['nama_dosen']) : 'Dosen'; ?>!</h2>
            <div class="row gy-4">
                <div class="col-lg-3">
                    <div class="mb-4">
                        <a href="dpengajuan.php" style="text-decoration: none; color: inherit;">
                            <div class="dashboard-card card-pengajuan status-card-common hover-effect-card">
                                <div class="number">0</div>
                                <div class="text-content">
                                    <span class="title">Pengajuan</span>
                                    <span class="description">Menunggu Persetujuan</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="mb-4">
                        <a href="dDaftarSidang.php" style="text-decoration: none; color: inherit;">
                            <div class="dashboard-card card-perbaikan status-card-common hover-effect-card">
                                <div class="number">0</div>
                                <div class="text-content">
                                    <span class="title">Perbaikan</span>
                                    <span class="description">Menunggu untuk Dinilai</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a href="dDaftarSidang.php" style="text-decoration: none; color: inherit;">
                            <div class="dashboard-card card-penilaian status-card-common hover-effect-card">
                                <div class="number">0</div>
                                <div class="text-content">
                                    <span class="title">Penilaian</span>
                                    <span class="description">Menunggu untuk Dinilai</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color:#4B68FB;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/dosen-dashboard-ajax.js"></script>
</body>

</html>