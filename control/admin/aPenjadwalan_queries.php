<?php
// Letakkan ini di baris paling atas file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path ke root directory. Untuk file di dalam /views/admin/, path ini sudah benar.
$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. PERUBAHAN: Cek jika role pengguna BUKAN 'admin'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

require "../../koneksi/koneksiAndrew.php";
$allDosenList = [];
$queryAllDosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen";
$resultAllDosen = sqlsrv_query($conn, $queryAllDosen);
if ($resultAllDosen) {
    while ($row = sqlsrv_fetch_array($resultAllDosen, SQLSRV_FETCH_ASSOC)) {
        $allDosenList[] = ['nama' => $row['nama_dosen']];
    }
}

// --- Baca parameter filter dari URL ---
$selectedTipe = $_GET['tipe'] ?? 'semua';
$selectedProdi = $_GET['prodi'] ?? 'semua';

// --- Query untuk mengambil daftar prodi unik ---
$prodiList = [];
$queryProdi = "SELECT DISTINCT prodi FROM Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi";
$resultProdi = sqlsrv_query($conn, $queryProdi);
if ($resultProdi) {
    while ($row = sqlsrv_fetch_array($resultProdi, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// --- Inisialisasi variabel untuk tampilan ---
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'Tugas Akhir') $tipeButtonText = 'Sidang TA';
elseif ($selectedTipe == 'Semester') $tipeButtonText = 'Sidang Semester';

$prodiButtonText = 'Semua Prodi';
if ($selectedProdi !== 'semua') {
    $prodiButtonText = htmlspecialchars($selectedProdi);
}


// --- Query Utama ---
$params = [];
$whereClauses = [];

$whereClauses[] = "s.status_ajuan = 'Approved'";
if ($selectedTipe == 'Tugas Akhir') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($selectedTipe == 'Semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

if ($selectedProdi !== 'semua') {
    $whereClauses[] = "m_perwakilan.prodi = ?"; 
    $params[] = $selectedProdi;
}

// =========================================================================
// PERBAIKAN UTAMA DI SINI: Subquery disesuaikan dengan struktur tabel yang benar
// =========================================================================
$sql = "SELECT 
            s.id_sidang,
            s.id_kelompok,
            s.judul AS judulSidang,
            k.jenis_sidang AS tipeSidang,
            MAX(m.prodi) AS prodi,
            (SELECT STRING_AGG(ma.nama_mhs, ', ') FROM Mahasiswa ma JOIN Kelas_Mahasiswa km ON ma.nim = km.nim WHERE km.id_kelas = (SELECT TOP 1 id_kelas FROM Kelas_Mahasiswa WHERE nim = k.nim)) AS namaList,
            MAX(mk.nama_matkul) AS mataKuliah,
            (SELECT TOP 1 d.nama_dosen FROM Bimbingan b JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = s.id_kelompok) AS pembimbing,
            (SELECT STRING_AGG(d.nama_dosen, CHAR(13)+CHAR(10)) 
             FROM Pengampu_Kelas pk 
             JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen 
             WHERE pk.id_matkul = MAX(ds.id_matkul) AND pk.id_kelas = (SELECT TOP 1 id_kelas FROM Kelas_Mahasiswa WHERE nim = k.nim)) AS dosenPengampuList
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        -- Join ke Mahasiswa via NIM perwakilan untuk filter prodi
        JOIN Mahasiswa m ON k.nim = m.nim 
        -- Left Join ke tabel lain untuk mendapatkan detail
        LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        LEFT JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
        WHERE " . implode(' AND ', $whereClauses) . "
        AND NOT EXISTS (SELECT 1 FROM Jadwal j WHERE j.id_sidang = s.id_sidang)
        GROUP BY s.id_sidang, s.id_kelompok, s.judul, k.jenis_sidang, k.nim
        ORDER BY s.id_sidang";


$result = sqlsrv_query($conn, $sql, $params);
if ($result === false) {
    die("Query gagal. Error: " . print_r(sqlsrv_errors(), true));
}

$data = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}
// --- MODIFIKASI: Variabel untuk teks tombol dan header ---
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'Tugas Akhir') $tipeButtonText = 'Sidang TA';
elseif ($selectedTipe == 'Semester') $tipeButtonText = 'Sidang Semester';

// BARU: Teks untuk tombol filter prodi
$prodiButtonText = 'Semua Prodi';
if ($selectedProdi !== 'semua') {
    $prodiButtonText = htmlspecialchars($selectedProdi);
}

$dynamicHeaderText = 'Judul/Mata Kuliah';
if ($selectedTipe == 'Tugas Akhir') $dynamicHeaderText = 'Judul Sidang';
elseif ($selectedTipe == 'Semester') $dynamicHeaderText = 'Mata Kuliah';

$dynamicDosenHeaderText = 'Pembimbing/Dosen';
if ($selectedTipe == 'Tugas Akhir') $dynamicDosenHeaderText = 'Pembimbing';
elseif ($selectedTipe == 'Semester') $dynamicDosenHeaderText = 'Dosen Pengampu';

?>