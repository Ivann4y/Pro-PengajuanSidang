<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}

// Pastikan data user dan nomor_dosen ada di session
if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    die("Error: Data dosen tidak ditemukan di session. Silakan login kembali.");
}
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];

include '../../koneksi/koneksiAndrew.php';
if ($conn === false) {
    die("Koneksi gagal: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

// --- KONFIGURASI UNTUK PAGINASI, PENCARIAN, DAN FILTER ---
$rowsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Semua';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($currentPage - 1) * $rowsPerPage;


$baseQuery = "
    WITH SidangData AS (
        SELECT DISTINCT
            s.id_sidang,
            s.id_kelompok,
            s.judul,
            s.jenis_sidang,
            d.nomor_dosen,
            d.nama_dosen,
            mk.nama_matkul
        FROM
            Sidang s
        JOIN
            Bimbingan b ON s.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
        JOIN
            Dosen d ON b.nomor_dosen = d.nomor_dosen
        LEFT JOIN
            Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        LEFT JOIN
            MataKuliah mk ON ds.id_matkul = mk.id_matkul
        WHERE
            s.status_ajuan = 0
    )
";

// Kumpulan kondisi WHERE dan parameternya
$whereConditions = [];
$params = [];

// Kondisi dasar: Dosen yang login
$whereConditions[] = "nomor_dosen = ?";
array_push($params, $nomorDosen);

// Terapkan kondisi filter
if ($filter === 'TA') {
    $whereConditions[] = "jenis_sidang = 0";
} elseif ($filter === 'Semester') {
    $whereConditions[] = "jenis_sidang = 1";
}

// Terapkan kondisi pencarian
if (!empty($search)) {
    $whereConditions[] = "(
        CAST(id_kelompok AS VARCHAR(255)) LIKE ? OR 
        ISNULL(judul, '') LIKE ? OR 
        ISNULL(nama_matkul, '') LIKE ?
    )";
    $likeParam = "%" . $search . "%";
    array_push($params, $likeParam, $likeParam, $likeParam);
}

// Gabungkan semua kondisi menjadi satu string WHERE clause
$whereClause = "WHERE " . implode(" AND ", $whereConditions);


// --- QUERY UNTUK MENGHITUNG TOTAL DATA UNTUK PAGINASI ---
$countSql = $baseQuery . "SELECT COUNT(*) as total FROM SidangData " . $whereClause;

$countStmt = sqlsrv_query($conn, $countSql, $params);
if ($countStmt === false) {
    die("Error saat menghitung data: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$totalRecords = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage) ?: 1;

// Sesuaikan halaman saat ini jika melebihi total halaman
if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $rowsPerPage;
}


// --- QUERY UTAMA UNTUK MENGAMBIL DATA SESUAI HALAMAN ---
$mainSql = $baseQuery . "
    SELECT id_sidang, id_kelompok, judul, nama_dosen, nama_matkul, jenis_sidang 
    FROM SidangData 
    " . $whereClause . "
    ORDER BY id_sidang DESC
    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
";

// Tambahkan parameter paginasi (offset, rowsPerPage) ke array parameter utama
$mainParams = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $mainSql, $mainParams);

if ($result === false) {
    die("Error saat mengambil data: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

// Set nomor awal untuk tabel
$nomor = $offset + 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/dPengajuan.css">
    <title>Dosen - Pengajuan</title>
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
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
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
            <div class="header-icons d-flex d-md-none">
                <a href="dNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon">
                    <a href="dProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="dBeranda">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Pengajuan Sidang</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="dNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="dProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                </div><br><br>
                <div class="row">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <label for="ddMsidang" class="fw-semibold mb-0">Filter:</label>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php
                                if ($filter === 'TA') echo 'Sidang TA';
                                elseif ($filter === 'Semester') echo 'Sidang Semester';
                                else echo 'Semua';
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=Semua&search=<?= urlencode($search) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=TA&search=<?= urlencode($search) ?>">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=Semester&search=<?= urlencode($search) ?>">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <form method="GET" action="" class="search-input-group ms-auto d-flex align-items-center">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Kelompok, Judul, Matkul..." value="<?= htmlspecialchars($search) ?>">
                        </form>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn kelompok-btn" style="max-width:300px;" onclick="openKelompokModal()" id="kelompokBtn">
                            <i class="bi bi-people-fill me-2"></i>Kelompok
                        </button>
                    </div>
                </div>
                <div class="row">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kelompok</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen Pembimbing</th>
                                <th scope="col">Jenis Sidang</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalRecords > 0 && sqlsrv_has_rows($result)): ?>
                                <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                                    <?php
                                    $jenisSidangTampilan = '';

                                    // Cek apakah mata kuliahnya adalah 'Tugas Akhir'
                                    if (isset($row['nama_matkul']) && $row['nama_matkul'] === 'Tugas Akhir') {
                                        $jenisSidangTampilan = 'TA';
                                    } else {
                                        $jenisSidangTampilan = ($row['jenis_sidang'] == 0) ? 'TA' : 'Semester';
                                    }
                                    ?>
                                    <tr class="isiTabel jadiBiru">
                                        <td><?= $nomor++; ?></td>
                                        <td><?= htmlspecialchars($row['id_kelompok']); ?></td>
                                        <td><?= htmlspecialchars($row['judul'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($row['nama_matkul'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($row['nama_dosen']); ?></td>

                                        <td><?= $jenisSidangTampilan; ?></td>

                                        <td style="text-align: center;">
                                            <button class="detail-btn" onclick="goToDetail('<?= $row['id_sidang']; ?>', '<?= $jenisSidangTampilan; ?>')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data ditemukan.</td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                    <div class="pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($totalPages > 1): ?>
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>">«</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>">»</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- MODAL LOGOUT-->
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

            <div class="modal fade" id="kelompokModal" tabindex="-1" aria-labelledby="kelompokModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title" id="kelompokModalLabel">Kelompok Mahasiswa</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-tab-container">
                                <button class="modal-tab active" onclick="switchTab('tambah')">Tambah Kelompok</button>
                                <button class="modal-tab" onclick="switchTab('daftar')">Daftar Kelompok</button>
                            </div>
                            <div id="tambah-tab" class="modal-tab-content active">
                                <div class="kelompok-form-container">
                                    <form id="kelompokForm">
                                        <div class="kelompok-form-group"> <label for="kelompok_id">ID Kelompok:</label>
                                            <input type="text" id="kelompok_id" name="kelompok_id" readonly />
                                        </div>
                                        <div class="kelompok-form-group">
                                            <label for="kelompok_prodi">Prodi:</label>
                                            <select id="kelompok_prodi" name="kelompok_prodi" onchange="filterMahasiswaByProdi()">
                                                <option value="">Pilih Prodi</option>
                                                <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                                <option value="Manajemen Informatika">Manajemen Informatika</option>
                                            </select>
                                        </div>
                                        <!-- Dosen Pembimbing Input (Multiple) -->
                                        <div class="form-section-card">
                                            <div class="form-section-title">Dosen Pembimbing <span class="text-muted">(Opsional)</span></div>
                                            <div class="dosen-wrapper" id="dosen-wrapper">
                                                <div class="anggota-form-group" id="dosen-form-1">
                                                    <label for="dosen_pembimbing_1">Dosen Pembimbing 1:</label>
                                                    <div class="anggota-input-group">
                                                        <div class="input-container">
                                                            <input type="text" id="dosen_pembimbing_1" name="dosen_pembimbing[]" placeholder="Masukkan NIP atau nama dosen" autocomplete="off" oninput="searchDosen(this, 1)" />
                                                            <div class="autocomplete-dropdown" id="autocomplete_dosen_1" style="display: none;"></div>
                                                        </div>
                                                        <div class="anggota-nama-display" id="dosen_nama_display_1">Nama akan muncul otomatis</div>
                                                        <div class="form-toggle-buttons">
                                                            <button type="button" onclick="addDosen()">+</button>
                                                            <button type="button" onclick="removeDosen()" style="display: none;">-</button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="dosen_nomor_hidden_1" name="dosen_nomor_hidden[]" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-section-card">
                                            <div class="form-section-title">Anggota Mahasiswa</div>
                                            <div class="anggota-wrapper" id="anggota-wrapper">
                                                <div class="anggota-form-group" id="anggota-form-1">
                                                    <label for="anggota_nim_1">Mahasiswa 1:</label>
                                                    <div class="anggota-input-group">
                                                        <div class="input-container">
                                                            <input type="text" id="anggota_nim_1" name="anggota_nim[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" />
                                                            <div class="autocomplete-dropdown" id="autocomplete_1" style="display: none;"></div>
                                                        </div>
                                                        <div class="anggota-nama-display" id="anggota_nama_1">Nama akan muncul otomatis</div>
                                                        <div class="form-toggle-buttons">
                                                            <button type="button" onclick="addAnggota()">+</button>
                                                            <button type="button" onclick="removeAnggota()" style="display: none;">-</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="kelompok-form-actions modal-footer border-0">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div id="daftar-tab" class="modal-tab-content">
                                <div class="kelompok-list-container" id="kelompok-list-container">
                                    <p class="text-center text-muted">Memuat daftar kelompok...</p>
                                </div>
                                <div class="kelompok-form-actions modal-footer justify-content-center border-0">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Sidebar Toggle Logic
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    </script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/kelompokModal.js"></script>
</body>

</html>