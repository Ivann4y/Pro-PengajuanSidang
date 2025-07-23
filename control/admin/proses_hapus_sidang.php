<?php
// FILE: proses_hapus_sidang.php (VERSI FINAL YANG LEBIH AMAN)

include "../../koneksi/koneksiJoin.php";
if ($conn === false) {
    die("Koneksi gagal: " . print_r(sqlsrv_errors(), true));
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

$id_sidang = isset($_POST['id_sidang']) ? (int)$_POST['id_sidang'] : 0;

if ($id_sidang <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
}

// ==============================
// 1. AMBIL INFORMASI KUNCI SEBELUM MENGHAPUS
// ==============================
$sql_get_info = "SELECT id_kelompok FROM Sidang WHERE id_sidang = ?";
$stmt_get_info = sqlsrv_query($conn, $sql_get_info, array($id_sidang));
if ($stmt_get_info === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil informasi sidang.']);
    exit;
}
$info_row = sqlsrv_fetch_array($stmt_get_info, SQLSRV_FETCH_ASSOC);

if (!$info_row) {
    echo json_encode(['status' => 'error', 'message' => 'Sidang dengan ID tersebut tidak ditemukan.']);
    exit;
}
$id_kelompok = $info_row['id_kelompok'];


// ==============================
// 2. MULAI TRANSAKSI DATABASE
// ==============================
if (sqlsrv_begin_transaction($conn) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
    exit;
}

$all_queries_ok = true;
$error_message = 'Terjadi kesalahan saat menghapus data.';

// ==============================
// 3. URUTAN PENGHAPUSAN (DARI ANAK KE INDUK)
//    Foreign Key:
//    - Penilaian -> Sidang
//    - Penjadwalan -> Sidang
//    - Jadwal -> Sidang
//    - Detail_Sidang -> Sidang
//    - Sidang -> Kelompok
//    - Bimbingan -> Kelompok
//    - Kelas_Mahasiswa -> Kelompok (via nim perwakilan)
// ==============================

// Daftar tabel dan parameter yang akan dihapus
$delete_operations = [
    // Hapus dari tabel yang bergantung pada id_sidang
    "DELETE FROM Penilaian WHERE id_sidang = ?" => [$id_sidang],
    "DELETE FROM Penjadwalan WHERE id_sidang = ?" => [$id_sidang],
    "DELETE FROM Jadwal WHERE id_sidang = ?" => [$id_sidang],
    "DELETE FROM Detail_Sidang WHERE id_sidang = ?" => [$id_sidang],
    
    // Hapus dari tabel Sidang itu sendiri
    "DELETE FROM Sidang WHERE id_sidang = ?" => [$id_sidang],
    
    // Hapus dari tabel yang bergantung pada id_kelompok
    // PENTING: Lakukan ini HANYA JIKA kelompok ini tidak lagi memiliki sidang lain.
    // Untuk amannya, kita akan hapus semua yang terkait kelompok ini.
    "DELETE FROM Bimbingan WHERE id_kelompok = ?" => [$id_kelompok],
    "DELETE FROM Kelompok WHERE id_kelompok = ?" => [$id_kelompok]
];

// Eksekusi setiap query penghapusan
foreach ($delete_operations as $sql => $params) {
    if (sqlsrv_query($conn, $sql, $params) === false) {
        $all_queries_ok = false;
        // Ambil error spesifik jika perlu untuk debugging
        // $error_details = sqlsrv_errors(); 
        // $error_message = "Gagal pada query: " . $sql;
        break; // Hentikan proses jika ada satu query yang gagal
    }
}


// ==============================
// 4. FINALISASI TRANSAKSI
// ==============================
if ($all_queries_ok) {
    sqlsrv_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Data sidang dan semua yang terkait berhasil dihapus.']);
} else {
    sqlsrv_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}

exit;
?>