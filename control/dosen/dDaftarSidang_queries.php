<?php

session_start();

// 1. Validasi Sesi Pengguna
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen' || !isset($_SESSION['user_data']['nomor_dosen'])) {
    header("Location: ../../logout.php");
    exit();
}
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen'];

// 2. Koneksi ke Database
include "../../koneksi/koneksiAndrew.php";
if ($conn === false) {
    die("Koneksi gagal: " . print_r(sqlsrv_errors(), true));
}

// 3. Logika Filter & Paginasi
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// =========================================================================
// === [SOLUSI FINAL & TEPAT] SESUAI STRUKTUR TABEL BIMBINGAN & PENJADWALAN ===
// =========================================================================

// [DIUBAH] Query untuk TUGAS AKHIR (dosen sebagai Pembimbing ATAU Penguji)
// Menggunakan isPembimbing = 0x01 untuk mencari Penanggung Jawab.
$queryTA = "
    SELECT DISTINCT
        s.id_sidang, 
        k.nomor_kelompok, 
        s.judul AS judul_sidang, 
        mk.nama_matkul AS nama_matkul_sidang, 
        d_pembimbing_utama.nama_dosen AS nama_penanggung_jawab,
        'Tugas Akhir' as jenis_sidang_filter
    FROM [dbo].[Sidang] s
    JOIN [dbo].[Kelompok] k ON s.id_kelompok = k.id_kelompok
    
    -- Blok ini hanya untuk MEMERIKSA apakah dosen yang login terlibat
    LEFT JOIN [dbo].[Bimbingan] b_check ON k.id_kelompok = b_check.id_kelompok
    LEFT JOIN [dbo].[Penjadwalan] pj_check ON s.id_sidang = pj_check.id_sidang

    -- Blok ini untuk MENCARI NAMA PENANGGUNG JAWAB (Pembimbing Utama)
    JOIN [dbo].[Bimbingan] b_utama ON k.id_kelompok = b_utama.id_kelompok
    JOIN [dbo].[Dosen] d_pembimbing_utama ON b_utama.nomor_dosen = d_pembimbing_utama.nomor_dosen
    
    LEFT JOIN [dbo].[MataKuliah] mk ON k.id_matkul = mk.id_matkul
    WHERE 
        -- Memastikan penanggung jawab adalah PEMBIMBING UTAMA
        b_utama.isPembimbing = 0x01 
        -- Dan memeriksa apakah dosen yang login terlibat sebagai pembimbing ATAU penguji
        AND (b_check.nomor_dosen = ? OR pj_check.nomor_dosen = ?)
";

// [TETAP] Query untuk SIDANG SEMESTER (dosen sebagai Pengampu)
$querySemester = "
    SELECT 
        s.id_sidang, k.nomor_kelompok, s.judul AS judul_sidang, 
        mk.nama_matkul AS nama_matkul_sidang, d.nama_dosen AS nama_penanggung_jawab,
        'Semester' as jenis_sidang_filter
    FROM [dbo].[Pengampu_Kelas] pk
    JOIN [dbo].[Dosen] d ON pk.nomor_dosen = d.nomor_dosen
    JOIN [dbo].[Kelas_Mahasiswa] km ON pk.id_kelas = km.id_kelas
    JOIN [dbo].[Mahasiswa] m ON km.nim = m.nim
    JOIN [dbo].[Kelompok] k ON m.nim = k.nim AND pk.id_matkul = k.id_matkul
    JOIN [dbo].[Sidang] s ON k.id_kelompok = s.id_kelompok
    LEFT JOIN [dbo].[MataKuliah] mk ON k.id_matkul = mk.id_matkul
    WHERE pk.nomor_dosen = ?
";


// --- Tentukan query mana yang akan dijalankan berdasarkan filter ---
$finalQuery = "";
$params = [];

if ($filter === 'ta') {
    $finalQuery = $queryTA;
    $params = [$nomor_dosen_login, $nomor_dosen_login];
} elseif ($filter === 'semester') {
    $finalQuery = $querySemester;
    $params = [$nomor_dosen_login];
} else { // filter 'all'
    $finalQuery = "($queryTA) UNION ALL ($querySemester)";
    $params = [$nomor_dosen_login, $nomor_dosen_login, $nomor_dosen_login];
}

// --- Menangani filter pencarian (Search) ---
$searchClause = "";
$likeParam = "";
if (!empty($search)) {
    $searchClause = " WHERE (CAST(nomor_kelompok AS VARCHAR(255)) LIKE ? OR judul_sidang LIKE ? OR nama_matkul_sidang LIKE ? OR nama_penanggung_jawab LIKE ?)";
    $likeParam = "%" . $search . "%";
}

// --- Gabungkan semua untuk query PENGHITUNGAN dan PENGAMBILAN DATA ---
$countQuery = "SELECT COUNT(*) as total FROM ($finalQuery) AS CombinedQuery" . $searchClause;
$mainQuery = "SELECT * FROM ($finalQuery) AS CombinedQuery" . $searchClause . " ORDER BY nomor_kelompok ASC, id_sidang ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";


// --- Eksekusi Query ---
// Siapkan parameter untuk query count
$countParams = $params;
if (!empty($search)) {
    array_push($countParams, $likeParam, $likeParam, $likeParam, $likeParam);
}

$countStmt = sqlsrv_query($conn, $countQuery, $countParams);
if ($countStmt === false) die("Error saat menghitung total data: " . print_r(sqlsrv_errors(), true));
$row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
$totalRecords = $row ? $row['total'] : 0;
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $rowsPerPage) : 1;

if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $rowsPerPage;
}

// Siapkan parameter untuk query utama (termasuk paginasi)
$mainParams = $params;
if (!empty($search)) {
    array_push($mainParams, $likeParam, $likeParam, $likeParam, $likeParam);
}
array_push($mainParams, $offset, $rowsPerPage);

$result = sqlsrv_query($conn, $mainQuery, $mainParams);
if ($result === false) die("Error pada query utama: " . print_r(sqlsrv_errors(), true));


// --- Logika Tampilan (Tetap Sama) ---
$nomor = $offset + 1;
$headerLabel = 'Pembimbing/Pengampu';
if ($filter === 'ta') $headerLabel = 'Pembimbing';
elseif ($filter === 'semester') $headerLabel = 'Pengampu';

// Ambil jumlah notifikasi belum dibaca untuk dosen
$unread_notifications = [];
if (isset($_SESSION['user_data']['nomor_dosen'])) {
    $nomor_dosen = (string)$_SESSION['user_data']['nomor_dosen'];
    $query_unread = "SELECT id_notifikasi FROM notifikasi WHERE penerima = ? AND (status_baca = 0 OR status_baca IS NULL)";
    $stmt_unread = sqlsrv_query($conn, $query_unread, array($nomor_dosen));
    if ($stmt_unread) {
        while ($row = sqlsrv_fetch_array($stmt_unread, SQLSRV_FETCH_ASSOC)) {
            $unread_notifications[] = $row;
        }
    }
}
$unread_count = count($unread_notifications);

?>