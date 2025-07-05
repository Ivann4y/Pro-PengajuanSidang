<?php

session_start();

// 1. Validasi Sesi Pengguna
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}

// 2. Menggunakan struktur session yang benar dan lebih aman
if (!isset($_SESSION['user_data']) || !isset($_SESSION['user_data']['nomor_dosen'])) {
    header("Location: ../../logout.php");
    exit();
}
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen'];

// --- KONEKSI DAN LOGIKA LAINNYA ---
include "../../koneksi/koneksiAndrew.php";
if ($conn === false) {
    die("Koneksi gagal: " . print_r(sqlsrv_errors(), true));
}

// --- LOGIKA FILTER & PAGINASI ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// [PERUBAHAN UTAMA: QUERY DISESUAIKAN DENGAN SKEMA BARU]
// Query dasar diubah untuk JOIN dengan tabel Kelompok
$baseQuery = "
    WITH FullSidangData AS (
        SELECT
            s.id_sidang,
            k.id_kelompok,      -- id_kelompok dari tabel Kelompok (Primary Key)
            k.nomor_kelompok,  -- nomor_kelompok untuk ditampilkan
            k.jenis_sidang,    -- jenis_sidang sekarang dari tabel Kelompok
            -- Menentukan Judul/Nama Mata Kuliah
            CASE 
                WHEN k.jenis_sidang = 'Tugas Akhir' THEN s.judul -- Jika Sidang TA, tampilkan judul
                ELSE (SELECT TOP 1 mk.nama_matkul FROM [dbo].[MataKuliah] mk WHERE mk.id_matkul = k.id_matkul) -- Jika Sidang Semester, tampilkan nama matkul dari Kelompok
            END AS display_title,
            -- Menentukan Penanggung Jawab (Pembimbing/Pengampu)
            CASE 
                WHEN k.jenis_sidang = 'Tugas Akhir' THEN -- Untuk Sidang TA, cari Dosen Pembimbing
                    (SELECT TOP 1 d.nama_dosen FROM [dbo].[Bimbingan] b JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = s.id_kelompok)
                ELSE -- Untuk Sidang Semester, cari Dosen Pengampu Mata Kuliah
                    (SELECT TOP 1 d.nama_dosen FROM [dbo].[Pengampu_Kelas] pk JOIN [dbo].[Dosen] d ON pk.nomor_dosen = d.nomor_dosen WHERE pk.id_matkul = k.id_matkul)
            END AS nama_penanggung_jawab
        FROM 
            [dbo].[Sidang] s
        JOIN 
            [dbo].[Kelompok] k ON s.id_kelompok = k.id_kelompok -- JOIN PENTING untuk mendapatkan data dari Kelompok
    )
";


// --- [PERUBAHAN: LOGIKA PENYARINGAN DISESUAIKAN] ---
$whereConditions = [];
$params = [];

// Kondisi utama: Tampilkan sidang hanya jika dosen yang login adalah penanggung jawab.
// Logika ini disederhanakan karena nama_penanggung_jawab sudah dihitung di CTE
$mainFilterCondition = "
(
    -- Kondisi 1: Dosen adalah pembimbing untuk Sidang TA
    (FullSidangData.jenis_sidang = 'Tugas Akhir' AND EXISTS (
        SELECT 1 FROM [dbo].[Bimbingan] b 
        WHERE b.id_kelompok = FullSidangData.id_kelompok AND b.nomor_dosen = ?
    ))
    OR
    -- Kondisi 2: Dosen adalah pengampu mata kuliah untuk Sidang Semester
    (FullSidangData.jenis_sidang = 'Semester' AND EXISTS (
        SELECT 1 FROM [dbo].[Pengampu_Kelas] pk 
        WHERE pk.id_matkul = (SELECT id_matkul FROM Kelompok WHERE id_kelompok = FullSidangData.id_kelompok) AND pk.nomor_dosen = ?
    ))
)";
$whereConditions[] = $mainFilterCondition;
// Tambahkan nomor dosen login untuk kedua kondisi di atas
array_push($params, $nomor_dosen_login, $nomor_dosen_login);


// Filter jenis sidang (TA atau Semester) sekarang menggunakan string
if ($filter === 'ta') {
    $whereConditions[] = "jenis_sidang = ?";
    array_push($params, 'Tugas Akhir'); // Menggunakan string 'Tugas Akhir'
} elseif ($filter === 'semester') {
    $whereConditions[] = "jenis_sidang = ?";
    array_push($params, 'Semester'); // Menggunakan string 'Semester'
}

// Filter pencarian sekarang mencari di 'nomor_kelompok'
if (!empty($search)) {
    // Mencari berdasarkan nomor kelompok, judul/matkul, atau nama penanggung jawab
    $whereConditions[] = "(CAST(nomor_kelompok AS VARCHAR(255)) LIKE ? OR display_title LIKE ? OR nama_penanggung_jawab LIKE ?)";
    $likeParam = "%" . $search . "%";
    array_push($params, $likeParam, $likeParam, $likeParam);
}

// Gabungkan semua kondisi WHERE
$whereClause = !empty($whereConditions) ? " WHERE " . implode(' AND ', $whereConditions) : "";

// --- QUERY PENGHITUNGAN TOTAL DATA ---
$countQuery = $baseQuery . "SELECT COUNT(id_sidang) as total FROM FullSidangData" . $whereClause;
$countStmt = sqlsrv_query($conn, $countQuery, $params);
if ($countStmt === false) {
    // Cetak error yang lebih detail untuk debugging
    echo "Error saat menghitung total data: <pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    // Juga cetak query yang dijalankan untuk analisis
    echo "Query Gagal: " . $countQuery;
    die();
}
$totalRecords = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'] ?? 0;
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $rowsPerPage) : 1;

// Pastikan halaman saat ini tidak melebihi total halaman
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $rowsPerPage;
}

// --- QUERY UTAMA UNTUK MENGAMBIL DATA ---
// Mengambil nomor_kelompok untuk ditampilkan
$mainQuery = $baseQuery . "SELECT id_sidang, nomor_kelompok, display_title, nama_penanggung_jawab FROM FullSidangData" . $whereClause . " ORDER BY nomor_kelompok ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
$mainParams = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $mainQuery, $mainParams);
if ($result === false) {
    die("Error pada query utama: " . print_r(sqlsrv_errors(), true));
}

$nomor = $offset + 1;

// Logika untuk label header tabel tetap sama
$headerLabel = 'Pembimbing/Pengampu';
if ($filter === 'ta') {
    $headerLabel = 'Pembimbing';
} elseif ($filter === 'semester') {
    $headerLabel = 'Pengampu';
}
?>

<!DOCTYPE html>
<!-- ... Sisa kode HTML Anda ... -->
<!-- Jangan lupa ubah bagian ini di dalam <tbody> -->
<!-- 
    Ganti:
    <td><?= htmlspecialchars($row['pembimbing'] ?? 'Belum Ditentukan') ?></td>
    Menjadi:
    <td><?= htmlspecialchars($row['nama_penanggung_jawab'] ?? 'Belum Ditentukan') ?></td>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosen - Daftar Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/dDaftarSidang.css">
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons d-flex d-md-none"><a href="dNotifikasi.php" title="Notifikasi" style="text-decoration:none;color:inherit"><i class="bi bi-bell-fill"></i></a>
                <div class="profile-icon"><a href="dProfil.php" title="Profil" style="text-decoration:none;color:inherit"><i class="bi bi-person-fill fs-5"></i></a></div>
            </div>
        </div>
        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Daftar Sidang</h2>
                <div class="header-icons d-none d-md-flex"><a href="dNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon"><a href="dProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color:#fff"></i></a></div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <label class="fw-semibold mb-0">Filter:</label>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php if ($filter === 'ta') echo 'Sidang TA';
                                elseif ($filter === 'semester') echo 'Sidang Semester';
                                else echo 'Semua'; ?>
                            </button>
                            <ul class="dropdown-menu rounded shadow">
                                <li><a class="dropdown-item" href="?filter=all&search=<?= urlencode($search) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=ta&search=<?= urlencode($search) ?>">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=semester&search=<?= urlencode($search) ?>">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <form method="GET" action="" class="search-form ms-auto">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                            <div class="search-input-wrapper">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" name="search" class="form-control search-input" placeholder="Cari Kelompok, Judul..." value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="search-submit-btn"></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kelompok</th>
                                    <th scope="col">Judul/Mata Kuliah</th>
                                    <!-- [PERUBAHAN] Teks di sini diubah untuk menggunakan variabel dinamis -->
                                    <th scope="col"><?= htmlspecialchars($headerLabel) ?></th>
                                    <th scope="col" style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($totalRecords > 0): ?>
                                    <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                                        <tr class="isiTabel jadiBiru">
                                            <td data-label="No"><?= $nomor++ ?></td>
                                            <td data-label="Kelompok"><?= htmlspecialchars($row['nomor_kelompok'] ?? '-') ?></td>
                                            <td data-label="Judul/Mata Kuliah"><?= htmlspecialchars($row['display_title'] ?? 'N/A') ?></td>
                                            <!-- [PERUBAHAN] Atribut data-label diubah untuk menggunakan variabel dinamis -->
                                            <td data-label="<?= htmlspecialchars($headerLabel) ?>"><?= htmlspecialchars($row['nama_penanggung_jawab'] ?? 'Belum Ditentukan') ?></td>                                            <td data-label="Aksi" style="text-align: center;">
                                                <a href="dEvaluasiSidang.php?id=<?= $row['id_sidang'] ?>" class="detail-btn" title="Evaluasi Sidang">
                                                    <i class="fa-solid fa-file-signature"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data yang sesuai dengan filter atau pencarian Anda.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($totalPages > 1): ?>
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $currentPage - 1 ?>">«</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $currentPage + 1 ?>">»</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button></div>
            </div>
        </div>
    </div>
    <script src="/Projek/Pro-PengajuanSidang/assets/js/dDaftarSidang.js"></script>
</body>

</html>