<?php
// --- FUNGSI 3: KONTROL SESI DAN KEAMANAN ---

// Mulai session PHP jika belum aktif. Ini diperlukan untuk mengakses variabel $_SESSION.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path relatif ke folder root aplikasi.
$path_to_root = '../../';

// 3.1. Cek otentikasi: Apakah pengguna sudah login?
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Jika belum login, siapkan pesan error.
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Arahkan (redirect) pengguna ke halaman login utama.
    header("Location: " . $path_to_root . "index.php"); 
    exit(); // Hentikan eksekusi script setelah redirect.
}

// 3.2. Cek otorisasi: Apakah pengguna memiliki role 'admin'?
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Jika role tidak sesuai, siapkan pesan error.
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    // Arahkan (redirect) pengguna ke halaman login utama.
    header("Location: " . $path_to_root . "index.php");
    exit(); // Hentikan eksekusi script.
}

// --- FUNGSI 4: KONEKSI & PENGAMBILAN DATA DARI DATABASE ---

// 4.1. Sertakan file koneksi database.
require "../../koneksi/koneksiAndrew.php";

// 4.2. Ambil parameter dari URL (query string) untuk filter dan paginasi.
// Gunakan operator null coalescing (??) untuk memberikan nilai default jika parameter tidak ada.
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Filter jenis sidang (ta, semester, all)
$prodiFilter = isset($_GET['prodi']) ? $_GET['prodi'] : 'all'; // Filter program studi
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Halaman saat ini untuk paginasi
$rowsPerPage = 10; // Jumlah data yang ditampilkan per halaman.
$offset = ($currentPage - 1) * $rowsPerPage; // Hitung offset untuk query SQL.

// 4.3. Ambil daftar program studi unik untuk ditampilkan di dropdown filter.
$prodiList = [];
$prodiQuery = "SELECT DISTINCT prodi FROM dbo.Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi ASC";
$prodiResult = sqlsrv_query($conn, $prodiQuery);
if ($prodiResult) {
    while ($row = sqlsrv_fetch_array($prodiResult, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// --- FUNGSI 5: PEMBUATAN QUERY SQL DINAMIS ---

// 5.1. Persiapan
$params = [];
$whereClauses = [];

// 5.2. Tambahkan kondisi WHERE berdasarkan filter jenis sidang.
if ($filter === 'ta') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

// 5.3. Tambahkan kondisi WHERE berdasarkan filter prodi.
if ($prodiFilter !== 'all') {
    $whereClauses[] = "
        EXISTS (
            SELECT 1 
            FROM Kelompok k_inner
            JOIN Kelas_Mahasiswa km ON k_inner.nim = km.nim
            JOIN Mahasiswa m ON km.nim = m.nim
            WHERE k_inner.id_kelompok = k.id_kelompok AND m.prodi = ?
        )
    ";
    $params[] = $prodiFilter;
}

// 5.4. Query untuk menghitung total data (untuk paginasi).
$countBaseQuery = "
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    WHERE EXISTS (SELECT 1 FROM Jadwal j WHERE j.id_sidang = s.id_sidang)
";
$countQuery = "SELECT COUNT(s.id_sidang) as total " . $countBaseQuery;
if (!empty($whereClauses)) {
    $countQuery .= " AND " . implode(" AND ", $whereClauses);
}
$countResult = sqlsrv_query($conn, $countQuery, $params);
if($countResult === false) { die("Error di count query: " . print_r(sqlsrv_errors(), true)); }
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// 5.5. [PERBAIKAN UTAMA] Query utama yang sudah disederhanakan.
$query = "
    SELECT
        s.id_sidang,
        s.judul,
        k.id_kelompok,
        k.jenis_sidang,
        mk.nama_matkul,
        
        CASE 
            WHEN k.jenis_sidang = 'Tugas Akhir' THEN
                -- Untuk TA: Ambil nama Dosen Pembimbing (Bagian ini sudah benar)
                (
                    SELECT STRING_AGG(d.nama_dosen, CHAR(13)+CHAR(10)) 
                    FROM Bimbingan b
                    JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
                    WHERE b.id_kelompok = k.id_kelompok AND b.isPembimbing = 1
                )
            
            WHEN k.jenis_sidang = 'Semester' THEN
                -- [PERBAIKAN FINAL] Untuk Semester: Mengikuti alur yang benar
                (
                    SELECT STRING_AGG(d.nama_dosen, CHAR(13)+CHAR(10))
                    FROM Kelas_Mahasiswa km
                    JOIN Pengampu_Kelas pk ON km.id_kelas = pk.id_kelas
                    JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
                    WHERE km.nim = k.nim                 -- Cocokkan mahasiswa dari kelompok
                      AND pk.id_matkul = k.id_matkul     -- Cocokkan mata kuliah dari kelompok
                )
        END AS nama_dosen_terkait
    
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
    WHERE 
        EXISTS (SELECT 1 FROM Jadwal j WHERE j.id_sidang = s.id_sidang)
";
// Tambahkan klausa WHERE jika ada filter yang aktif.
if (!empty($whereClauses)) {
    $query .= " AND " . implode(' AND ', $whereClauses);
}

// Tambahkan klausa ORDER BY dan paginasi (OFFSET-FETCH).
$query .= " ORDER BY s.id_sidang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

$params_final = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $query, $params_final);
if ($result === false) {
    die("Error di main query: <pre>" . print_r(sqlsrv_errors(), true) . "</pre><br>Query:<br>" . htmlspecialchars($query));
}
?>