<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek login and role
if (
    !isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true ||
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa'
) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: ../../index.php");
    exit();
}

require_once '../../koneksi/koneksiAndrew.php';
$nim = $_SESSION['user_data']['nim'];
$pengajuan_list = [];

// Flash message check (for success/error messages after redirects)
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

// Query: get all groups and their submission status
$sql = "
    SELECT 
        k.nomor_kelompok,
        k.tahun_ajaran,
        k.jenis_sidang,
        k.id_matkul,
        m.nama_matkul,
        s.id_sidang,
        s.judul,
        s.status_ajuan,
        s.waktu_pengumpulan
    FROM (
        SELECT DISTINCT id_kelompok
        FROM Kelompok
        WHERE nim = ?
    ) AS k_unique
    JOIN (
        SELECT *, ROW_NUMBER() OVER(PARTITION BY id_kelompok ORDER BY nim) as rn
        FROM Kelompok
    ) k ON k_unique.id_kelompok = k.id_kelompok AND k.rn = 1
    JOIN MataKuliah m ON k.id_matkul = m.id_matkul
    LEFT JOIN Sidang s ON k.id_kelompok = s.id_kelompok
    ORDER BY k.tahun_ajaran DESC, m.nama_matkul, k.nomor_kelompok ASC;
";

$stmt = sqlsrv_query($conn, $sql, [$nim]);
if ($stmt === false) {
    die("Error executing query: " . print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    if (is_null($row['status_ajuan'])) {
        $row['status_ajuan'] = 'Belum Ada Pengajuan';
    }
    $pengajuan_list[] = $row;
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
    <style>
        .pengajuan-card-wrapper .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .pengajuan-card-wrapper .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
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
                     <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
        </div>

        <main class="NavSide__main-content" id="mPengajuan">
            <div class="container-fluid">
                <div class="dashboard-header">
                    <h2 class="text-heading" style="color:black;">Pengajuan Sidang Anda</h2>
                </div>

                <?php if ($flash_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flash_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Search Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0" id="search-pengajuan" placeholder="Cari berdasarkan judul, mata kuliah, atau status...">
                        </div>
                    </div>
                </div>

                <!-- Pengajuan List Container -->
                <div id="pengajuan-list">
                    <?php if (empty($pengajuan_list)): ?>
                        <div class="alert alert-info text-center">
                            <h4 class="alert-heading"><i class="fas fa-info-circle"></i> Tidak Ada Kelompok</h4>
                            <p>Anda saat ini tidak terdaftar dalam kelompok sidang manapun. Silakan hubungi admin prodi untuk informasi lebih lanjut.</p>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($pengajuan_list as $p): ?>
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
                                                case 'Draft':
                                                    $badge_class = 'bg-info text-dark';
                                                    break;
                                                case 'Pending':
                                                    $badge_class = 'bg-warning text-dark';
                                                    break;
                                                case 'Disetujui':
                                                case 'Approved':
                                                    $badge_class = 'bg-success';
                                                    break;
                                                case 'Ditolak':
                                                case 'Rejected':
                                                    $badge_class = 'bg-danger';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $status = 'Belum Ada Pengajuan';
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
                                                case 'Belum Ada Pengajuan':
                                                    echo "<a href='{$link}' class='btn btn-success btn-sm'><i class='fas fa-plus me-2'></i>Buat Pengajuan</a>";
                                                    break;
                                                case 'Disetujui':
                                                case 'Approved':
                                                case 'Ditolak':
                                                case 'Rejected':
                                                    echo "<a href='mSidang.php' class='btn btn-outline-info btn-sm'><i class='fas fa-eye me-2'></i>Lihat Detail Sidang</a>";
                                                    break;
                                                case 'Pending':
                                                    echo "<button class='btn btn-secondary btn-sm' disabled><i class='fas fa-clock me-2'></i>Menunggu Review</button>";
                                                    break;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        document.addEventListener("DOMContentLoaded", function() {
            // Client-side search filter
            const searchInput = document.getElementById("search-pengajuan");
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    const query = this.value.toLowerCase().trim();
                    const cards = document.querySelectorAll(".pengajuan-card-wrapper");

                    cards.forEach(card => {
                        const cardText = card.textContent.toLowerCase();
                        card.style.display = cardText.includes(query) ? "" : "none";
                    });
                });
            }

            // Sidebar Toggle Logic
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