<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../koneksi/koneksiAndrew.php';

// Check if user is logged in and is mahasiswa
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || 
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: ../../index.php");
    exit();
}

$nim_mahasiswa_logged_in = $_SESSION['nim'] ?? '';
if (empty($nim_mahasiswa_logged_in)) {
    die("Error: NIM mahasiswa tidak ditemukan dalam session. Silakan login kembali.");
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
                        <h2 class="text-heading" style="color:black;">
                            <?php echo isset($_SESSION['user_data']['nama_mhs']) ? htmlspecialchars($_SESSION['user_data']['nama_mhs']) : 'Mahasiswa'; ?> (Mahasiswa)
                        </h2>
                        <p class="text-subheading">Kelola pengajuan sidang Anda</p>
                    </div>
                </div>

                <!-- Alert Container -->
                <div id="alert-container"></div>

                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="search-pengajuan" placeholder="Cari pengajuan...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="Belum ada pengajuan">Belum ada pengajuan</option>
                            <option value="Draft">Draft</option>
                            <option value="Pending">Menunggu Review</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" onclick="loadPengajuanList()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div id="loading-spinner" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data pengajuan...</p>
                </div>

                <!-- Pengajuan List Container -->
                <div id="pengajuan-list">
                    <!-- Data will be loaded here via AJAX -->
                </div>
            </div>
        </main>
    </div>

    <!-- Pengajuan Detail Modal -->
    <div class="modal fade" id="pengajuanDetailModal" tabindex="-1" aria-labelledby="pengajuanDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pengajuanDetailModalLabel">Detail Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nomor Kelompok:</strong> <span id="modal-nomor-kelompok"></span></p>
                            <p><strong>Tahun Ajaran:</strong> <span id="modal-tahun-ajaran"></span></p>
                            <p><strong>Jenis Sidang:</strong> <span id="modal-jenis-sidang"></span></p>
                            <p><strong>Mata Kuliah:</strong> <span id="modal-nama-matkul"></span></p>
                            <p><strong>Status:</strong> <span id="modal-status"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Judul:</strong> <span id="modal-judul"></span></p>
                            <p><strong>Deskripsi:</strong> <span id="modal-deskripsi"></span></p>
                            <p><strong>Tanggal Pengajuan:</strong> <span id="modal-tanggal-pengajuan"></span></p>
                            <p><strong>Submitter:</strong> <span id="modal-submitter"></span></p>
                            <p><strong>File:</strong> <span id="modal-file"></span></p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Anggota Kelompok:</h6>
                        <div id="modal-anggota-list" class="border rounded p-2">
                            <!-- Anggota list will be populated here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="modal-action-buttons">
                        <!-- Action buttons will be populated here -->
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="logMBerandaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logMBerandaLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="../../logout.php" class="btn btn-primary">Ya, Keluar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/mPengajuan.js"></script>
    
    <script>
        // Sidebar Toggle Logic
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            
            if (menuToggle) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }
        });
    </script>
</body>
</html>