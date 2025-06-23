<?php
// <-- TETAP SAMA -->
session_start();
include "../../koneksi/koneksiArgha.php";
// require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

if ($conn === false) { die("Koneksi gagal: " . print_r(sqlsrv_errors(), true)); }

// --- SIMULASI LOGIN (TETAP SAMA) ---
$nomor_dosen_login = '1001'; 

// --- LOGIKA FILTER & PAGINASI (TETAP SAMA) ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// --- PERBAIKAN PADA BASEQUERY ---
// Mengadopsi prinsip "ambil data luas dulu".
// JOIN ke Penjadwalan di sini dihapus agar tidak memfilter data terlalu dini.
// Ini membuat base query lebih mirip dengan prinsip di aDaftarSidang.
$baseQuery = "
    WITH FullSidangData AS (
        SELECT
            s.id_sidang,
            s.id_kelompok,
            s.judul,
            s.jenis_sidang, -- Dipertahankan jika masih ada kegunaan lain
            
            (SELECT TOP 1 mk.nama_matkul
             FROM [dbo].[Detail_Sidang] ds
             JOIN [dbo].[MataKuliah] mk ON ds.id_matkul = mk.id_matkul
             WHERE ds.id_sidang = s.id_sidang) AS nama_matkul,
            
            (SELECT TOP 1 d.nama_dosen
             FROM [dbo].[Bimbingan] b
             JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen
             WHERE b.id_kelompok = s.id_kelompok AND d.isPembimbing = 0x01) AS pembimbing,
             
            (SELECT STRING_AGG(d.nama_dosen, ', ')
             FROM [dbo].[Penjadwalan] p
             JOIN [dbo].[Dosen] d ON p.nomor_dosen = d.nomor_dosen
             WHERE p.id_sidang = s.id_sidang AND d.isPenguji = 0x01) AS penguji
             
        FROM [dbo].[Sidang] s
    )
";

// --- PENYESUAIAN KLAUSA WHERE DINAMIS (Mengikuti Prinsip aDaftarSidang) ---
$whereConditions = [];
$params = [];

// 1. Tambahkan kondisi WAJIB terlebih dahulu (Fungsi yang tidak boleh hilang)
//    Dosen yang login harus terlibat sebagai Pembimbing ATAU Penguji.
$whereConditions[] = "
    (
        EXISTS (
            SELECT 1 
            FROM [dbo].[Bimbingan] b 
            JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen
            WHERE b.id_kelompok = FullSidangData.id_kelompok AND d.nomor_dosen = ?
        )
        OR
        EXISTS (
            SELECT 1 
            FROM [dbo].[Penjadwalan] p 
            WHERE p.id_sidang = FullSidangData.id_sidang AND p.nomor_dosen = ?
        )
    )";
array_push($params, $nomor_dosen_login, $nomor_dosen_login);

// 2. Tambahkan kondisi OPSIONAL dari Dropdown (Logika disesuaikan agar benar)
//    Ini meniru cara aDaftarSidang menambahkan WHERE, tapi dengan kondisi yang tepat.
if ($filter === 'ta') {
    // Tampilkan jika di kolom 'judul' ATAU 'nama_matkul' tertulis 'Tugas Akhir'.
    $whereConditions[] = "(judul = ? OR nama_matkul = ?)";
    array_push($params, 'Tugas Akhir', 'Tugas Akhir');

} elseif ($filter === 'semester') {
    // Tampilkan jika BUKAN 'Tugas Akhir' di kedua kolom.
    $whereConditions[] = "(ISNULL(judul, '') != ? AND ISNULL(nama_matkul, '') != ?)";
    array_push($params, 'Tugas Akhir', 'Tugas Akhir');
}
// Jika $filter = 'all', tidak ada kondisi yang ditambahkan, jadi semua jenis sidang ditampilkan.


// 3. Tambahkan kondisi OPSIONAL dari Search Box (Fungsi yang tidak boleh hilang)
if (!empty($search)) {
    $whereConditions[] = "(CAST(id_kelompok AS VARCHAR(255)) LIKE ? OR judul LIKE ? OR nama_matkul LIKE ? OR pembimbing LIKE ? OR penguji LIKE ?)";
    $likeParam = "%" . $search . "%";
    array_push($params, $likeParam, $likeParam, $likeParam, $likeParam, $likeParam);
}

// 4. Gabungkan semua kondisi menjadi satu klausa WHERE (Sama seperti aDaftarSidang)
$whereClause = " WHERE " . implode(' AND ', $whereConditions);

// --- QUERY PENGHITUNGAN TOTAL DATA (TETAP SAMA) ---
$countQuery = $baseQuery . "SELECT COUNT(id_sidang) as total FROM FullSidangData" . $whereClause;
$countResult = sqlsrv_query($conn, $countQuery, $params);
if ($countResult === false) { die("Error saat menghitung total data: " . print_r(sqlsrv_errors(), true)); }
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// --- QUERY UTAMA UNTUK MENGAMBIL DATA (TETAP SAMA) ---
// DISTINCT tidak lagi diperlukan karena base query sudah benar
$mainQuery = $baseQuery . "SELECT id_sidang, id_kelompok, judul, jenis_sidang, nama_matkul, pembimbing, penguji FROM FullSidangData" . $whereClause . " ORDER BY id_kelompok ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
$mainParams = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $mainQuery, $mainParams);
if ($result === false) { die("Error pada query utama: " . print_r(sqlsrv_errors(), true)); }

$nomor = $offset + 1;
?>
<!DOCTYPE html>
<!-- KODE HTML LANJUTANNYA TETAP SAMA -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/dDaftarSidang.css">
    <title>Dosen - Daftar Sidang</title>

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
            <div class="header-icons d-flex d-md-none"><a href="mNotifikasi.php" title="Notifikasi" style="text-decoration:none;color:inherit"><i class="bi bi-bell-fill"></i></a><div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration:none;color:inherit"><i class="bi bi-person-fill fs-5"></i></a></div></div>
        </div>
        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="bodyHeading">Daftar Sidang</h2>
                <div class="header-icons d-none d-md-flex"><a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a><div class="profile-icon"><a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color:#fff"></i></a></div></div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <label class="fw-semibold mb-0">Filter:</label>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php if ($filter === 'ta') echo 'Sidang TA'; elseif ($filter === 'semester') echo 'Sidang Semester'; else echo 'Semua'; ?>
                            </button>
                            <ul class="dropdown-menu rounded shadow">
                                <li><a class="dropdown-item" href="?filter=all&page=1&search=<?= urlencode($search) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?filter=ta&page=1&search=<?= urlencode($search) ?>">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?filter=semester&page=1&search=<?= urlencode($search) ?>">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <form method="GET" action="" class="search-input-group ms-auto d-flex align-items-center">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Kelompok, Matkul, Pembimbing..." value="<?= htmlspecialchars($search) ?>">
                        </form>
                    </div>
                </div>
                <div class="row">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kelompok</th>
                                <th scope="col">Judul/Mata Kuliah</th>
                                <th scope="col">Pembimbing</th>
                                <th scope="col">Penguji</th>
                                <th scope="col" style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (sqlsrv_has_rows($result)): ?>
                                <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                                    <tr class="isiTabel jadiBiru">
                                        <td><?= $nomor++ ?></td>
                                        <td><?= htmlspecialchars($row['id_kelompok']) ?></td>
                                        <td><?= htmlspecialchars(($row['jenis_sidang'] == '0x00' || $row['judul'] == 'Tugas Akhir') ? $row['judul'] : ($row['nama_matkul'] ?? $row['judul'])) ?></td>
                                        <td><?= htmlspecialchars($row['pembimbing'] ?? 'Belum Ditentukan') ?></td>
                                        <td><?= htmlspecialchars($row['penguji'] ?? 'Belum Ditentukan') ?></td>
                                        <td style="text-align: center;">
                                            <a href="dEvaluasiSidang.php?id_sidang=<?= $row['id_sidang'] ?>" class="detail-btn">
                                                <i class="fa-solid fa-file-signature"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                <div style="background-color: rgb(67, 54, 240);"><div class="modal-header"><h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1></div></div>
                <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button></div>
            </div>
        </div>
    </div>
    <script>
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    </script>
</body>
</html>