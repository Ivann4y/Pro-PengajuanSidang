<?php
// --- FUNGSI 3: KONTROL SESI DAN KEAMANAN ---

// Cek status sesi saat ini. Jika tidak ada sesi yang aktif (PHP_SESSION_NONE),
// maka mulai sesi baru. Ini mencegah error jika session_start() dipanggil lebih dari sekali.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path relatif ke folder root aplikasi.
$path_to_root = '../../';

// 3.1. Cek Otentikasi: Memastikan pengguna sudah login.
// `!isset($_SESSION['is_logged_in'])` -> Cek apakah variabel sesi 'is_logged_in' ada.
// `$_SESSION['is_logged_in'] !== true` -> Cek apakah nilainya adalah `true`.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) { 
    // Jika salah satu kondisi di atas benar (pengguna belum login), siapkan pesan error.
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
   // Mengarahkan pengguna kembali ke halaman login (index.php) menggunakan header HTTP.
    header("Location: " . $path_to_root . "index.php"); 
    exit(); // Hentikan eksekusi script ini agar tidak ada kode lain yang dijalankan setelah redirect.
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
include "../../koneksi/koneksiJoin.php";
if ($conn === false) {
    die("Koneksi gagal: " . print_r(sqlsrv_errors(), true));
}

// 4.2. Ambil parameter dari URL untuk filter dan paginasi.
// Menggunakan `isset()` untuk mengecek apakah parameter ada di URL. Jika ada, gunakan nilainya. Jika tidak, berikan nilai default.
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Filter jenis sidang (ta, semester, all)
$prodiFilter = isset($_GET['prodi']) ? $_GET['prodi'] : 'all'; // Filter program studi
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Halaman saat ini untuk paginasi
$rowsPerPage = 10; // Jumlah data yang ditampilkan per halaman.
$offset = ($currentPage - 1) * $rowsPerPage; // Menghitung berapa baris data yang harus dilewati (offset) untuk halaman saat ini.

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
$params = []; // Array untuk menampung nilai parameter yang akan diikat ke query (mencegah SQL Injection).
$whereClauses = []; // Array untuk menampung semua kondisi WHERE yang akan digabungkan nanti.


// 5.2. Tambahkan kondisi WHERE berdasarkan filter jenis sidang.
if ($filter === 'ta') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

// 5.3. Tambahkan kondisi WHERE berdasarkan filter prodi.
if ($prodiFilter !== 'all') {
     // Menggunakan subquery dengan EXISTS. Ini lebih efisien daripada JOIN untuk sekadar mengecek keberadaan data.
    // Artinya: "Pilih sidang HANYA JIKA ADA (EXISTS) mahasiswa di kelompok tersebut yang prodinya cocok dengan filter".
    $whereClauses[] = "
        EXISTS (
            SELECT 1 
            FROM Kelompok k_inner
            JOIN Kelas_Mahasiswa km ON k_inner.nim = km.nim
            JOIN Mahasiswa m ON km.nim = m.nim
            WHERE k_inner.id_kelompok = k.id_kelompok AND m.prodi = ?
        )
    ";
    // Tambahkan nilai prodi ke array parameter. Tanda tanya (?) adalah placeholder.
    $params[] = $prodiFilter;
}

// 5.4. Query untuk menghitung total data yang sesuai filter (diperlukan untuk paginasi).
// Ini adalah bagian dasar dari query yang sama dengan query utama, tanpa pemilihan kolom.
$countBaseQuery = "
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    WHERE EXISTS (SELECT 1 FROM Jadwal j WHERE j.id_sidang = s.id_sidang)
";
// Gabungkan query dasar dengan SELECT COUNT.
$countQuery = "SELECT COUNT(s.id_sidang) as total " . $countBaseQuery;
if (!empty($whereClauses)) {// Jika ada filter yang aktif,
    // Gabungkan semua kondisi WHERE dengan "AND" dan tambahkan ke query hitung.
    $countQuery .= " AND " . implode(" AND ", $whereClauses);
}
// Jalankan query hitung dengan parameter filter.
$countResult = sqlsrv_query($conn, $countQuery, $params);
if($countResult === false) { die("Error di count query: " . print_r(sqlsrv_errors(), true)); }
// Ambil hasilnya, yaitu jumlah total record.
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
// Hitung jumlah total halaman yang dibutuhkan. `ceil` membulatkan ke atas.
$totalPages = ceil($totalRecords / $rowsPerPage);

// 5.5. [PERBAIKAN UTAMA] Query utama yang sudah disederhanakan.
$query = "
    SELECT
        s.id_sidang,
        s.judul,
        k.id_kelompok,
        k.nomor_kelompok,
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