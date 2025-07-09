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

// Sertakan file koneksi ke database SQL Server
require "../../koneksi/koneksiAndrew.php";

// ==============================
// FUNGSI 3: AMBIL DAFTAR DOSEN PENGUJI (untuk autocomplete JS)
// ==============================

// Siapkan array kosong untuk daftar dosen penguji
$allDosenList = [];
// Query untuk mengambil semua nama dosen yang bisa jadi penguji
$queryAllDosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen";
// Jalankan query
$resultAllDosen = sqlsrv_query($conn, $queryAllDosen);
// Jika query berhasil, masukkan nama dosen ke array
if ($resultAllDosen) {
    while ($row = sqlsrv_fetch_array($resultAllDosen, SQLSRV_FETCH_ASSOC)) {
        $allDosenList[] = ['nama' => $row['nama_dosen']];
    }
}

// ==============================
// FUNGSI 4: AMBIL FILTER DARI URL
// ==============================

// Ambil filter tipe sidang dari URL, default 'semua'
$selectedTipe = $_GET['tipe'] ?? 'semua';
// Ambil filter prodi dari URL, default 'semua'
$selectedProdi = $_GET['prodi'] ?? 'semua';

// ==============================
// FUNGSI 5: AMBIL DAFTAR PRODI UNIK
// ==============================

// Siapkan array kosong untuk daftar prodi
$prodiList = [];
// Query untuk mengambil semua prodi unik
$queryProdi = "SELECT DISTINCT prodi FROM Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi";
// Jalankan query
$resultProdi = sqlsrv_query($conn, $queryProdi);
// Jika query berhasil, masukkan prodi ke array
if ($resultProdi) {
    while ($row = sqlsrv_fetch_array($resultProdi, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// ==============================
// FUNGSI 6: INISIALISASI VARIABEL TAMPILAN
// ==============================

// Teks default untuk tombol filter tipe sidang
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'Tugas Akhir') $tipeButtonText = 'Sidang TA';
elseif ($selectedTipe == 'Semester') $tipeButtonText = 'Sidang Semester';

// Teks default untuk tombol filter prodi
$prodiButtonText = 'Semua Prodi';
if ($selectedProdi !== 'semua') {
    // htmlspecialchars untuk keamanan output HTML
    $prodiButtonText = htmlspecialchars($selectedProdi);
}

// ==============================
// FUNGSI 7: SIAPKAN QUERY UTAMA UNTUK DAFTAR SIDANG
// ==============================

// Siapkan array parameter dan where clause untuk query
$params = [];
$whereClauses = [];

// Tambahkan filter status ajuan harus 'Approved'
$whereClauses[] = "s.status_ajuan = 'Approved'";
// Tambahkan filter tipe sidang jika dipilih
if ($selectedTipe == 'Tugas Akhir') {
    $whereClauses[] = "k.jenis_sidang = 'Tugas Akhir'";
} elseif ($selectedTipe == 'Semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}
// Tambahkan filter prodi jika dipilih
if ($selectedProdi !== 'semua') {
    $whereClauses[] = "m_perwakilan.prodi = ?"; 
    $params[] = $selectedProdi;
}

// ==============================
// FUNGSI 8: QUERY UTAMA DAFTAR SIDANG YANG BELUM DIJADWALKAN
// ==============================

// Query utama untuk mengambil daftar sidang yang belum dijadwalkan
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

// ==============================
// FUNGSI 9: EKSEKUSI QUERY DAN AMBIL DATA
// ==============================

// Jalankan query utama dengan parameter filter
$result = sqlsrv_query($conn, $sql, $params);
if ($result === false) {
    // Jika query gagal, tampilkan error dan hentikan script
    die("Query gagal. Error: " . print_r(sqlsrv_errors(), true));
}

// Siapkan array kosong untuk menampung data hasil query
$data = [];
// Loop setiap baris hasil query, masukkan ke array $data
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}

// ==============================
// FUNGSI 10: VARIABEL DINAMIS UNTUK TAMPILAN
// ==============================

// Teks dinamis untuk header tabel dan tombol, tergantung filter tipe sidang
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'Tugas Akhir') $tipeButtonText = 'Sidang TA';
elseif ($selectedTipe == 'Semester') $tipeButtonText = 'Sidang Semester';

// Teks dinamis untuk tombol filter prodi
$prodiButtonText = 'Semua Prodi';
if ($selectedProdi !== 'semua') {
    $prodiButtonText = htmlspecialchars($selectedProdi);
}

// Teks dinamis untuk header kolom tabel
$dynamicHeaderText = 'Judul/Mata Kuliah';
if ($selectedTipe == 'Tugas Akhir') $dynamicHeaderText = 'Judul Sidang';
elseif ($selectedTipe == 'Semester') $dynamicHeaderText = 'Mata Kuliah';

$dynamicDosenHeaderText = 'Pembimbing/Dosen';
if ($selectedTipe == 'Tugas Akhir') $dynamicDosenHeaderText = 'Pembimbing';
elseif ($selectedTipe == 'Semester') $dynamicDosenHeaderText = 'Dosen Pengampu';

?>