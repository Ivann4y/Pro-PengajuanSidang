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

// [PERUBAHAN UTAMA: QUERY DISESUAIKAN UNTUK MENAMPILKAN JUDUL DAN MATKUL BERSAMAAN]
$baseQuery = "
    WITH FullSidangData AS (
        SELECT
            s.id_sidang,
            k.id_kelompok,
            k.nomor_kelompok,
            k.jenis_sidang,
            
            -- [DIUBAH] Mengambil judul langsung dari tabel Sidang
            s.judul AS judul_sidang,
            
            -- [DIUBAH] Mengambil nama mata kuliah langsung dari hasil LEFT JOIN
            mk.nama_matkul AS nama_matkul_sidang,
            
            -- Menentukan Penanggung Jawab (Pembimbing/Pengampu)
            CASE 
                WHEN k.jenis_sidang = 'Tugas Akhir' THEN 
                    (SELECT TOP 1 d.nama_dosen FROM [dbo].[Bimbingan] b JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = s.id_kelompok)
                ELSE 
                    (SELECT TOP 1 d.nama_dosen FROM [dbo].[Pengampu_Kelas] pk JOIN [dbo].[Dosen] d ON pk.nomor_dosen = d.nomor_dosen WHERE pk.id_matkul = k.id_matkul)
            END AS nama_penanggung_jawab
        FROM 
            [dbo].[Sidang] s
        JOIN 
            [dbo].[Kelompok] k ON s.id_kelompok = k.id_kelompok
        LEFT JOIN 
            [dbo].[MataKuliah] mk ON k.id_matkul = mk.id_matkul -- LEFT JOIN untuk mendapatkan nama matkul
    )
";

// --- LOGIKA PENYARINGAN (Tidak ada perubahan di sini) ---
$whereConditions = [];
$params = [];

// Kondisi utama: Tampilkan sidang hanya jika dosen yang login adalah penanggung jawab.
$mainFilterCondition = "
(
    (jenis_sidang = 'Tugas Akhir' AND EXISTS (
        SELECT 1 FROM [dbo].[Bimbingan] b WHERE b.id_kelompok = FullSidangData.id_kelompok AND b.nomor_dosen = ?
    ))
    OR
    (jenis_sidang = 'Semester' AND EXISTS (
        SELECT 1 FROM [dbo].[Pengampu_Kelas] pk JOIN [dbo].[Kelompok] k ON pk.id_matkul = k.id_matkul 
        WHERE k.id_kelompok = FullSidangData.id_kelompok AND pk.nomor_dosen = ?
    ))
)";
$whereConditions[] = $mainFilterCondition;
array_push($params, $nomor_dosen_login, $nomor_dosen_login);


// Filter jenis sidang (TA atau Semester)
if ($filter === 'ta') {
    $whereConditions[] = "jenis_sidang = ?";
    array_push($params, 'Tugas Akhir');
} elseif ($filter === 'semester') {
    $whereConditions[] = "jenis_sidang = ?";
    array_push($params, 'Semester');
}

// Filter pencarian sekarang mencari di kolom terpisah
if (!empty($search)) {
    $whereConditions[] = "(CAST(nomor_kelompok AS VARCHAR(255)) LIKE ? OR judul_sidang LIKE ? OR nama_matkul_sidang LIKE ? OR nama_penanggung_jawab LIKE ?)";
    $likeParam = "%" . $search . "%";
    array_push($params, $likeParam, $likeParam, $likeParam, $likeParam);
}

// Gabungkan semua kondisi WHERE
$whereClause = !empty($whereConditions) ? " WHERE " . implode(' AND ', $whereConditions) : "";

// --- QUERY PENGHITUNGAN TOTAL DATA ---
$countQuery = $baseQuery . "SELECT COUNT(id_sidang) as total FROM FullSidangData" . $whereClause;
$countStmt = sqlsrv_query($conn, $countQuery, $params);
if ($countStmt === false) {
    die("Error saat menghitung total data: " . print_r(sqlsrv_errors(), true));
}
$totalRecords = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'] ?? 0;
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $rowsPerPage) : 1;

if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $rowsPerPage;
}

// --- QUERY UTAMA UNTUK MENGAMBIL DATA ---
$mainQuery = $baseQuery . "SELECT id_sidang, nomor_kelompok, judul_sidang, nama_matkul_sidang, nama_penanggung_jawab FROM FullSidangData" . $whereClause . " ORDER BY nomor_kelompok ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
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