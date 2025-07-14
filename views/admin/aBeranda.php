<?php
// Letakkan ini di baris paling atas file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path ke root directory. Untuk file di dalam /views/admin/, path ini sudah benar.
$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. PERUBAHAN: Cek jika role pengguna BUKAN 'admin'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

require "../../koneksi/koneksiAndrew.php";

$admin_username = $_SESSION['user_data']['username'];
$unread_notifications = [];
$query_unread = "SELECT id_notifikasi FROM notifikasi WHERE penerima = ? AND (status_baca = 0 OR status_baca IS NULL)";
$stmt_unread = sqlsrv_query($conn, $query_unread, array($admin_username));
if ($stmt_unread) {
    while ($row = sqlsrv_fetch_array($stmt_unread, SQLSRV_FETCH_ASSOC)) {
        $unread_notifications[] = $row;
    }
}
$unread_count = count($unread_notifications);

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
    <link rel="stylesheet" href="../../assets/css/aBeranda.css" />
    
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
              <!-- <h2 class="text-heading-mobile flex-fill">Beranda Admin</h2> -->
            <div class="header-icons">
                <a href="aNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill position-relative">
                        <?php if ($unread_count > 0): ?>
                            <span class="notif-badge"> <?= $unread_count ?> </span>
                        <?php endif; ?>
                    </i>
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
                    <a href="aNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-bell-fill position-relative">
                            <?php if ($unread_count > 0): ?>
                                <span class="notif-badge"> <?= $unread_count ?> </span>
                            <?php endif; ?>
                        </i>
                    </a>
                    <div class="profile-icon">
                        <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white;"></i></a>
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