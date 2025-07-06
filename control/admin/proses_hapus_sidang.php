<?php
// proses_hapus_sidang.php

// Sertakan file koneksi
require "../../koneksi/koneksiAndrew.php";

// Set header sebagai JSON
header('Content-Type: application/json');

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

// Ambil ID sidang dari data POST
$id_sidang = isset($_POST['id_sidang']) ? (int)$_POST['id_sidang'] : 0;

if ($id_sidang <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
}

// Memulai transaksi database
if (sqlsrv_begin_transaction($conn) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
    exit;
}

$all_queries_ok = true;

// Definisikan urutan penghapusan dari tabel anak ke tabel induk
// Ini penting untuk menjaga integritas referensial (foreign key constraints)
$delete_queries = [
    // 1. Hapus dari tabel yang memiliki foreign key ke Jadwal atau Penjadwalan (jika ada)
    //    Contoh: Jika ada tabel 'Log_Jadwal', hapus dari sana dulu.

    // 2. Hapus dari tabel Penilaian (tergantung pada Penjadwalan dan Sidang)
    "DELETE FROM Penilaian WHERE id_sidang = ?",
    
    // 3. Hapus dari tabel Penjadwalan (tergantung pada Sidang)
    "DELETE FROM Penjadwalan WHERE id_sidang = ?",
    
    // 4. Hapus dari tabel Jadwal (tergantung pada Sidang)
    "DELETE FROM Jadwal WHERE id_sidang = ?",
    
    // 5. Hapus dari tabel Detail_Sidang (tergantung pada Sidang)
    "DELETE FROM Detail_Sidang WHERE id_sidang = ?",

    // 6. Hapus dari tabel Bimbingan (asumsi 1 kelompok bisa bimbingan & sidang)
    //    Kita perlu id_kelompok dari Sidang dulu
    //    Ini akan kita handle secara terpisah.

    // 7. Hapus dari tabel Sidang itu sendiri (tabel induk utama)
    //    Akan dijalankan terakhir.
];

// Eksekusi query penghapusan yang tidak bergantung pada data lain
foreach ($delete_queries as $sql) {
    $stmt = sqlsrv_query($conn, $sql, array($id_sidang));
    if ($stmt === false) {
        $all_queries_ok = false;
        // Hentikan loop jika ada satu query yang gagal
        break; 
    }
}

// Handle penghapusan Bimbingan secara terpisah
if ($all_queries_ok) {
    // Ambil id_kelompok dari sidang yang akan dihapus
    $sql_get_kelompok = "SELECT id_kelompok FROM Sidang WHERE id_sidang = ?";
    $stmt_get_kelompok = sqlsrv_query($conn, $sql_get_kelompok, array($id_sidang));
    
    if ($stmt_get_kelompok && $row = sqlsrv_fetch_array($stmt_get_kelompok, SQLSRV_FETCH_ASSOC)) {
        $id_kelompok = $row['id_kelompok'];
        
        // Hapus bimbingan yang terkait dengan kelompok tersebut
        $sql_delete_bimbingan = "DELETE FROM Bimbingan WHERE id_kelompok = ?";
        $stmt_delete_bimbingan = sqlsrv_query($conn, $sql_delete_bimbingan, array($id_kelompok));
        
        if ($stmt_delete_bimbingan === false) {
            $all_queries_ok = false;
        }
    }
}


// Langkah terakhir: Hapus dari tabel Sidang
if ($all_queries_ok) {
    $sql_delete_sidang = "DELETE FROM Sidang WHERE id_sidang = ?";
    $stmt_delete_sidang = sqlsrv_query($conn, $sql_delete_sidang, array($id_sidang));
    if ($stmt_delete_sidang === false) {
        $all_queries_ok = false;
    }
}

// Finalisasi Transaksi
if ($all_queries_ok) {
    // Jika semua query berhasil, commit transaksi
    sqlsrv_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Data sidang dan semua yang terkait berhasil dihapus.']);
} else {
    // Jika ada satu saja yang gagal, rollback semua perubahan
    sqlsrv_rollback($conn);
    // Beri pesan error yang lebih spesifik jika memungkinkan
    // echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data. Terjadi kesalahan pada database.', 'details' => sqlsrv_errors()]);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data. Terjadi kesalahan pada database.']);
}

exit;
?>