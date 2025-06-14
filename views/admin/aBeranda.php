<?php
// Mulai sesi dan cek apakah user adalah admin
session_start();
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, redirect ke halaman login
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <!-- Responsive viewport untuk mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Nama penulis halaman -->
    <meta name="author" content="JaisyuNurM-AliansiSidang_Kelompok5" />
    <title>Beranda Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css" />

    <style>
      
      /* Penjadwalan Style Card */
        .penjadwalan-status-card {
            background-color: #4B68FB;
            color: white;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;

        }

        .penjadwalan-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        }

        .penjadwalan-status-card .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            min-width: 50px;
            text-align: center;
        }

        .penjadwalan-status-card .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex:1;
            overflow-wrap: break-word;
            min-width: 0;
        }

        .penjadwalan-status-card .text .title {
            font-size: 0.95rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.1rem;
        }

        .penjadwalan-status-card .text .description {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* Pengajuan Style Card */

        .pengajuan-status-card {
            background-color: rgb(239, 239, 239);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .pengajuan-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .pengajuan-status-card .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            color: rgb(37, 44, 54);
            min-width: 50px;
            text-align: center;
        }

        .pengajuan-status-card .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex:1;
            overflow-wrap: break-word;
            min-width: 0;
        }

        .pengajuan-status-card .text .title {
            font-size: 0.95rem;
            font-weight: 500;
            color: #4B5563;
            display: block;
            margin-bottom: 0.1rem;
        }

        .pengajuan-status-card .text .description {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1F2937;
        }

    </style>
</head>

<body>
    <div id="NavSide">
        <!-- Sidebar Navigasi -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <!-- Menu aktif -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"> 
                     <b></b><b></b>      
                    <a href="aBeranda.php">
                        <span class="NavSide__sidebar-title fw-semibold">Beranda</span>
                    </a>
                </li>
                <!-- Menu lain -->
                <li class="NavSide__sidebar-item">
                     <b></b><b></b>
                    <a href="aPenjadwalan.php">
                        <span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                     <b></b><b></b>
                    <a href="aDaftarSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span>
                    </a>
                </li>
                <!-- Tombol keluar, memunculkan modal konfirmasi -->
                <li class="NavSide__sidebar-item">
                     <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda">
                        <span class="NavSide__sidebar-title fw-semibold">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Topbar untuk mobile -->
        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <!-- Judul Halaman -->
              <h2 class="text-heading-mobile flex-fill">Beranda Admin</h2>
            <div class="header-icons">
                <!-- tugas -->
                <a href="aNotifikasi.php" title="tugas" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <!-- Profil -->
                <div class="profile-icon">
                    <a href="aProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="text-heading">Beranda Admin</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="aNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>

            <h2 class="welcomeText">Selamat Datang, Admin!</h2>

            <div class="row">
                <!-- Statistik Penjadwalan & Pengajuan -->
    <div class="col-lg-7">
        <div class="row">
            <div class="col-12 col-md-6">
            <!-- Kartu Penjadwalan -->
            <a href="aPenjadwalan.php" style="text-decoration: none; color: inherit;">
                <div class="dashboard-card penjadwalan-status-card">
                    <div class="number">0</div>
                    <div class="text">
                        <span class="title">Penjadwalan</span>
                        <span class="description">Menunggu Dijadwalkan</span>
                    </div>
                </div>
            </a>
            </div>
            <div class="col-12 col-md-6">
            <!-- Kartu Pengajuan -->
            <a href="aPenjadwalan.php" style="text-decoration: none; color: inherit;">
                <div class="dashboard-card pengajuan-status-card">
                    <div class="number">0</div>
                    <div class="text">
                        <span class="title">Pengajuan</span>
                        <span class="description">Menunggu Persetujuan</span>
                    </div>
                </div>
            </a>
            </div>
        </div>

        <!-- Card Tanggungan dengan style tugas-card -->
        <div class="dashboard-card content-card tugas-card">
            <h3 class="section-title">Tugas</h3>
            <!-- Generate/render oleh dashboard.js -->
        </div>
    </div>

      <!-- Kalender dan Sidang Mendatang -->
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
                                  <!-- Kalender akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Sidang Mendatang -->
                    <div class="dashboard-card content-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
                        <!-- Daftar sidang mendatang, setiap item berisi tanggal, bulan, info, dan ikon -->
                        
                        <!-- ...item lain... -->
                    </div>
                </div>

              

                    
                </div>
            </div>
        </main>
    </div>
  
    <!-- Modal keluar (konfirmasi logout) -->
    <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-heading-color">
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

    <!-- Bootstrap JS Bundle (termasuk Popper) -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Dashboard.js -->
    <script src="../../assets/js/dashboard.js"></script>
    
    <script>
    </script>
</body>

</html>