<?php
// ==============================
// FUNGSI 1: KONTROL SESI DAN AKSES
// ==============================

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path ke root aplikasi (untuk redirect)
$path_to_root = '../../';

// Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

// ==============================
// FUNGSI 2: KONEKSI DATABASE
// ==============================

require "../../koneksi/koneksiAndrew.php";

// ==============================
// FUNGSI 3: AMBIL DAFTAR DOSEN PENGUJI (untuk autocomplete JS)
// ==============================

$allDosenList = [];
$queryAllDosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen";
$resultAllDosen = sqlsrv_query($conn, $queryAllDosen);
if ($resultAllDosen) {
    while ($row = sqlsrv_fetch_array($resultAllDosen, SQLSRV_FETCH_ASSOC)) {
        $allDosenList[] = ['nama' => $row['nama_dosen']];
    }
}

// ==============================
// FUNGSI 4: AMBIL FILTER DARI URL
// ==============================

$selectedTipe = $_GET['tipe'] ?? 'semua';
$selectedProdi = $_GET['prodi'] ?? 'semua';
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// ==============================
// FUNGSI 5: AMBIL DAFTAR PRODI UNIK
// ==============================

$prodiList = [];
$queryProdi = "SELECT DISTINCT prodi FROM Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi";
$resultProdi = sqlsrv_query($conn, $queryProdi);
if ($resultProdi) {
    while ($row = sqlsrv_fetch_array($resultProdi, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// ==============================
// FUNGSI 6: INISIALISASI VARIABEL TAMPILAN
// ==============================

$tipeButtonText = 'Semua Sidang';
if ($selectedTipe == 'Tugas Akhir') $tipeButtonText = 'Sidang TA';
elseif ($selectedTipe == 'Semester') $tipeButtonText = 'Sidang Semester';

$prodiButtonText = 'Semua Prodi';
if ($selectedProdi !== 'semua') {
    $prodiButtonText = htmlspecialchars($selectedProdi);
}

// ==============================
// FUNGSI 7: SIAPKAN QUERY DINAMIS
// ==============================

$params = [];
$whereClauses = [];

$whereClauses[] = "s.status_ajuan = 'Approved'"; // Kondisi wajib

if ($selectedTipe == 'Tugas Akhir') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($selectedTipe == 'Semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

if ($selectedProdi !== 'semua') {
    // Gunakan subquery EXISTS untuk filter prodi, ini lebih efisien
    $whereClauses[] = "EXISTS (
        SELECT 1 
        FROM Mahasiswa m_check 
        WHERE m_check.nim = k.nim AND m_check.prodi = ?
    )";
    $params[] = $selectedProdi;
}

// --- PERUBAHAN UTAMA: Logika NOT EXISTS untuk Penjadwalan ---
// Untuk halaman Penjadwalan, kita ingin yang BELUM ADA di tabel Jadwal
$whereClauses[] = "NOT EXISTS (SELECT 1 FROM Jadwal j WHERE j.id_sidang = s.id_sidang)";


// Gabungkan semua klausa WHERE
$whereCondition = implode(' AND ', $whereClauses);

// ==============================
// FUNGSI 8: QUERY COUNT DAN UTAMA (Struktur sama persis dengan Daftar Sidang)
// ==============================

// Query untuk menghitung total record
$countQuery = "SELECT COUNT(s.id_sidang) as total
               FROM Sidang s
               JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
               WHERE $whereCondition";

$countResult = sqlsrv_query($conn, $countQuery, $params);
if ($countResult === false) {
    die("Count query gagal: " . print_r(sqlsrv_errors(), true));
}
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// Query utama (SAMA PERSIS dengan aDaftarSidang_queries.php)
$sql = "
    SELECT
        s.id_sidang,
        s.judul AS judulSidang,
        k.id_kelompok,
        k.jenis_sidang AS tipeSidang,
        m.prodi,
        mk.nama_matkul AS mataKuliah,
        
        -- Mengambil Dosen Pembimbing untuk TA
        pembimbing.nama_dosen_list AS pembimbingList,
        
        -- Mengambil Dosen Pengampu untuk Semester
        pengampu.nama_dosen_list AS dosenPengampuList
    
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    LEFT JOIN Mahasiswa m ON k.nim = m.nim
    LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul

    -- Mengambil daftar Dosen Pembimbing
    OUTER APPLY (
        SELECT STRING_AGG(d.nama_dosen, CHAR(13)+CHAR(10)) AS nama_dosen_list
        FROM Bimbingan b
        JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
        WHERE b.id_kelompok = s.id_kelompok AND b.isPembimbing = 1
    ) AS pembimbing

    -- Mengambil daftar Dosen Pengampu
    OUTER APPLY (
        SELECT STRING_AGG(d.nama_dosen, CHAR(13)+CHAR(10)) AS nama_dosen_list
        FROM Pengampu_Kelas pk
        JOIN Kelas_Mahasiswa km ON pk.id_kelas = km.id_kelas
        JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
        WHERE km.nim = k.nim AND pk.id_matkul = k.id_matkul
    ) AS pengampu

    WHERE $whereCondition

    ORDER BY s.id_sidang
    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
";

// ==============================
// FUNGSI 9: EKSEKUSI QUERY DAN AMBIL DATA
// ==============================

$params_final = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $sql, $params_final);
if ($result === false) {
    die("Query gagal. Error: " . print_r(sqlsrv_errors(), true));
}

$data = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}
?>