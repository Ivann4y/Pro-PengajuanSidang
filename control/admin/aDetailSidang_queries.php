<?php
// ==============================
// FUNGSI 1: KONTROL SESI DAN KEAMANAN
// ==============================

// Cek dan mulai session jika belum aktif (agar bisa pakai $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path ke root aplikasi (untuk redirect)
$path_to_root = '../../';

// 1.1. Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Jika belum login, simpan pesan error dan redirect ke halaman login
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit();
}

// 1.2. Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Jika bukan admin, simpan pesan error dan redirect ke halaman login
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
// FUNGSI 3: AMBIL ID SIDANG DARI URL/SESSION
// ==============================

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['judul'])) {
    
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    // INI JANGAN DI HAPUS
    $_SESSION['judul'] = $_GET['judul'];

    // Redirect ke halaman yang sama TAPI TANPA parameter GET
    header("Location: aDetailSidang.php");
    exit();
}

// Ambil id sidang dari session, jika tidak ada redirect ke daftar sidang
if (isset($_SESSION['id_sidang_aktif']) && is_numeric($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {
    // Jika tidak ada id sidang valid, redirect ke daftar sidang
    $_SESSION['error_message'] = "ID Sidang tidak valid atau tidak ditemukan. Silakan pilih sidang dari daftar.";
    header("Location: aDaftarSidang.php");
    exit();
}

// ==============================
// FUNGSI 4: INISIALISASI VARIABEL
// ==============================
$dosen_pembimbing = [];
$dosen_penguji_data = [];
$dosen_pengampu_data = [];
$dosen_list_penguji = []; // Untuk autocomplete

// ==============================
// FUNGSI 5: AMBIL DATA SIDANG, JADWAL, PRODI, MATKUL
// ==============================
$sql_utama = "
    SELECT 
        s.id_sidang, s.judul, s.id_kelompok,
        k.jenis_sidang, k.nomor_kelompok, k.id_matkul, k.nim AS nim_perwakilan,
        CASE s.status_sidang WHEN 1 THEN 'Disetujui' WHEN 0 THEN 'Ditolak' ELSE 'Menunggu' END AS status_sidang_text,
        j.ruang_sidang, j.tanggal_sidang, j.jam_sidang, j.jam_selesai,
        m.prodi,
        mk.nama_matkul
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    LEFT JOIN Jadwal j ON s.id_sidang  = j.id_sidang
    LEFT JOIN Mahasiswa m ON k.nim = m.nim
    LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
    WHERE s.id_sidang = ?
";
$stmt_utama = sqlsrv_query($conn, $sql_utama, array($id_sidang));
if ($stmt_utama === false) { die("Error pada query utama: " . print_r(sqlsrv_errors(), true)); }
$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) { die("Data sidang tidak ditemukan."); }

$data_jadwal = $data_sidang;
$nama_prodi = $data_sidang['prodi'];
$data_matkul = ['nama_matkul' => $data_sidang['nama_matkul']];

// ==============================
// FUNGSI 6: AMBIL DATA DOSEN TERLIBAT BESERTA BOBOTNYA (VERSI ANTI-DUPLIKASI)
// ==============================
if ($data_sidang['jenis_sidang'] == 'Tugas Akhir') {
    // Ambil PEMBIMBING dari tabel Bimbingan
    $sql_pembimbing = "
        SELECT 
            d.nama_dosen, 
            -- Subquery Skalar untuk mengambil HANYA SATU bobot
            (SELECT TOP 1 p.bobot_penilaian FROM Penilaian p WHERE p.id_sidang = ? AND p.nomor_dosen = d.nomor_dosen) AS bobot
        FROM Bimbingan b
        JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
        WHERE b.id_kelompok = ? AND b.isPembimbing = 1
    ";
    $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, array($id_sidang, $data_sidang['id_kelompok']));
    if ($stmt_pembimbing) {
        while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
            $dosen_pembimbing[] = $row;
        }
    }

    // Ambil PENGUJI dari tabel Penjadwalan
    $sql_penguji = "
        SELECT 
            d.nama_dosen, 
            -- Subquery Skalar untuk mengambil HANYA SATU bobot
            (SELECT TOP 1 p.bobot_penilaian FROM Penilaian p WHERE p.id_sidang = ? AND p.nomor_dosen = d.nomor_dosen) AS bobot
        FROM Penjadwalan pj
        JOIN Dosen d ON pj.nomor_dosen = d.nomor_dosen
        WHERE pj.id_sidang = ? AND pj.peran_dosen = 0
    ";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, array($id_sidang, $id_sidang));
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosen_penguji_data[] = $row;
        }
    }

} elseif ($data_sidang['jenis_sidang'] == 'Semester') {
    // Ambil PENGAMPU dari tabel Pengampu_Kelas
    $sql_pengampu = "
        SELECT 
            d.nama_dosen,
            -- Subquery Skalar untuk mengambil HANYA SATU bobot
            (SELECT TOP 1 p.bobot_penilaian FROM Penilaian p WHERE p.id_sidang = ? AND p.nomor_dosen = d.nomor_dosen) AS bobot
        FROM Pengampu_Kelas pk
        JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
        WHERE pk.id_matkul = ? AND pk.id_kelas = (
            SELECT TOP 1 km.id_kelas FROM Kelas_Mahasiswa km WHERE km.nim = ?
        )
    ";
    $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, array($id_sidang, $data_sidang['id_matkul'], $data_sidang['nim_perwakilan']));
    if ($stmt_pengampu) {
        while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
            $dosen_pengampu_data[] = $row;
        }
    }
}
// ==============================
// FUNGSI 7: AMBIL DAFTAR DOSEN UNTUK AUTOCOMPLETE
// ==============================
$sql_all_dosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen ASC";
$stmt_all_dosen = sqlsrv_query($conn, $sql_all_dosen);
if ($stmt_all_dosen) {
    while ($row = sqlsrv_fetch_array($stmt_all_dosen, SQLSRV_FETCH_ASSOC)) {
        $dosen_list_penguji[] = ['nama' => $row['nama_dosen']];
    }
}
$dosen_list_json = json_encode($dosen_list_penguji);
?>
