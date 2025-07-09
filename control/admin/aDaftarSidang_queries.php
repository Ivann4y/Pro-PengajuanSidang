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

// 5.1. Persiapan untuk membangun query secara dinamis.
$params = []; // Array untuk menampung parameter query (mencegah SQL Injection).
$whereClauses = []; // Array untuk menampung klausa WHERE.

// 5.2. Definisikan klausa JOIN utama.
$joins = "
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    JOIN Jadwal j ON s.id_sidang = j.id_sidang
";

// 5.3. Tambahkan kondisi WHERE berdasarkan filter jenis sidang.
if ($filter === 'ta') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

// 5.4. Tambahkan kondisi WHERE berdasarkan filter prodi.
if ($prodiFilter !== 'all') {
    // Tambahkan JOIN tambahan ke tabel Mahasiswa untuk mendapatkan prodi.
    $joins .= "
        JOIN Mahasiswa m_prodi ON k.nim = m_prodi.nim
    ";
    // Tambahkan klausa WHERE untuk prodi dan siapkan parameternya.
    $whereClauses[] = "m_prodi.prodi = ?";
    $params[] = $prodiFilter;
}

// 5.5. Query untuk menghitung total data (untuk paginasi).
// Gabungkan query dasar, join, dan klausa where yang sudah dibuat.
$countQuery = "SELECT COUNT(DISTINCT s.id_sidang) as total FROM Sidang s {$joins}";
if (!empty($whereClauses)) {
    $countQuery .= " WHERE " . implode(" AND ", $whereClauses);
}
// Eksekusi query hitung.
$countResult = sqlsrv_query($conn, $countQuery, $params);
if($countResult === false) { die("Error di count query: " . print_r(sqlsrv_errors(), true)); }
// Ambil hasilnya dan hitung total halaman.
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// 5.6. Query utama untuk mengambil data sidang sesuai halaman dan filter.
$query = "SELECT DISTINCT
        s.id_sidang, s.judul, s.id_kelompok, k.jenis_sidang,
        -- Subquery untuk mengambil nama mata kuliah terkait sidang.
        (SELECT TOP 1 mk.nama_matkul 
         FROM Detail_Sidang ds JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
         WHERE ds.id_sidang = s.id_sidang) AS nama_matkul,
        -- Logika CASE untuk mengambil nama dosen yang berbeda tergantung jenis sidang.
        CASE 
            WHEN k.jenis_sidang = 'Tugas Akhir' THEN
                -- Jika TA, ambil nama dosen pembimbing dari tabel Bimbingan.
                (SELECT d.nama_dosen FROM Bimbingan b JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = s.id_kelompok AND b.isPembimbing = 0x01)
            WHEN k.jenis_sidang = 'Semester' THEN
                -- Jika Semester, gabungkan semua nama dosen pengampu mata kuliah tersebut.
                (SELECT STRING_AGG(d.nama_dosen, CHAR(13) + CHAR(10)) -- STRING_AGG untuk menggabungkan nama dengan baris baru
                 FROM Pengampu_Kelas pk JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
                 WHERE 
                    pk.id_matkul = (SELECT TOP 1 ds.id_matkul FROM Detail_Sidang ds WHERE ds.id_sidang = s.id_sidang)
                    AND pk.id_kelas = (
                        -- Subquery untuk mencari id_kelas mahasiswa dalam kelompok sidang ini.
                        SELECT TOP 1 k_mhs.id_kelas FROM Kelompok klp
                        JOIN Mahasiswa mhs ON klp.nim = mhs.nim
                        JOIN Kelas_Mahasiswa k_mhs ON mhs.nim = k_mhs.nim
                        WHERE klp.id_kelompok = s.id_kelompok
                    )
                )
        END AS nama_dosen_terkait
    FROM Sidang s
    {$joins} -- Gabungkan dengan JOIN yang sudah dibuat.
";

// Tambahkan klausa WHERE jika ada filter yang aktif.
if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(' AND ', $whereClauses);
}

// Tambahkan klausa ORDER BY dan paginasi (OFFSET-FETCH).
$query .= " ORDER BY s.id_sidang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

// Gabungkan parameter filter dengan parameter paginasi.
$params_final = array_merge($params, [$offset, $rowsPerPage]);

// Eksekusi query utama.
$result = sqlsrv_query($conn, $query, $params_final);
if ($result === false) {
    die("Error di main query: " . print_r(sqlsrv_errors(), true));
}
?>