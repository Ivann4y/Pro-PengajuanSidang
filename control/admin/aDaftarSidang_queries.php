<?php
// Mulai session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

//Cek jika role pengguna BUKAN 'admin'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}
// Include file koneksi ke database
require "../../koneksi/koneksiAndrew.php";

// Ambil parameter filter, prodi, dan halaman dari URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$prodiFilter = isset($_GET['prodi']) ? $_GET['prodi'] : 'all';
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$rowsPerPage = 10;
$offset = ($currentPage - 1) * $rowsPerPage;

// Ambil daftar prodi unik dari database
$prodiList = [];
$prodiQuery = "SELECT DISTINCT prodi FROM dbo.Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi ASC";
$prodiResult = sqlsrv_query($conn, $prodiQuery);
if ($prodiResult) {
    while ($row = sqlsrv_fetch_array($prodiResult, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}



// --- Persiapan Filter ---
$params = [];
$whereClauses = [];

// JOIN disederhanakan dan diperbaiki
$joins = "
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    JOIN Jadwal j ON s.id_sidang = j.id_sidang
";

// Filter jenis sidang
if ($filter === 'ta') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

// Cara filter prodi disesuaikan dengan ERD baru
if ($prodiFilter !== 'all') {
    // Tambahkan JOIN ke Mahasiswa langsung dari Kelompok
    $joins .= "
        JOIN Mahasiswa m_prodi ON k.nim = m_prodi.nim
    ";
    $whereClauses[] = "m_prodi.prodi = ?";
    $params[] = $prodiFilter;
}

// --- Query untuk menghitung total data ---
$countQuery = "SELECT COUNT(DISTINCT s.id_sidang) as total FROM Sidang s {$joins}";
if (!empty($whereClauses)) {
    $countQuery .= " WHERE " . implode(" AND ", $whereClauses);
}

$countResult = sqlsrv_query($conn, $countQuery, $params);
if($countResult === false) { die("Error di count query: " . print_r(sqlsrv_errors(), true)); }
$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

// --- Query utama untuk mengambil data sidang ---
// --- Query utama untuk mengambil data sidang ---
$query = "SELECT DISTINCT
        s.id_sidang,
        s.judul,
        s.id_kelompok,
        k.jenis_sidang,
        (SELECT TOP 1 mk.nama_matkul 
         FROM Detail_Sidang ds
         JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
         WHERE ds.id_sidang = s.id_sidang) AS nama_matkul,
        CASE 
            WHEN k.jenis_sidang = 'Tugas Akhir' THEN
                -- [DIPERBAIKI] Menggunakan STRING_AGG untuk menangani lebih dari satu pembimbing
                (SELECT STRING_AGG(d.nama_dosen, ', ')
                 FROM Bimbingan b
                 JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
                 WHERE b.id_kelompok = s.id_kelompok AND b.isPembimbing = 0x01)

            WHEN k.jenis_sidang = 'Semester' THEN
                -- [DIPERBAIKI] Menyeragamkan pemisah menjadi koma spasi
                (SELECT STRING_AGG(d.nama_dosen, ', ')
                 FROM Pengampu_Kelas pk
                 JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
                 WHERE 
                    pk.id_matkul = (SELECT TOP 1 ds.id_matkul FROM Detail_Sidang ds WHERE ds.id_sidang = s.id_sidang)
                    AND pk.id_kelas IN (
                        SELECT k_mhs.id_kelas
                        FROM Kelompok klp
                        JOIN Mahasiswa mhs ON klp.nim = mhs.nim
                        JOIN Kelas_Mahasiswa k_mhs ON mhs.nim = k_mhs.nim
                        WHERE klp.id_kelompok = s.id_kelompok
                    )
                )
        END AS nama_dosen_terkait
    FROM Sidang s
    {$joins}
";

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(' AND ', $whereClauses);
}

$query .= " ORDER BY s.id_sidang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

// Tambahkan parameter untuk OFFSET dan FETCH
$params_final = array_merge($params, [$offset, $rowsPerPage]);

// Eksekusi query utama
$result = sqlsrv_query($conn, $query, $params_final);
if ($result === false) {
    die("Error di main query: " . print_r(sqlsrv_errors(), true));
}
?>