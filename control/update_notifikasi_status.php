<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Pastikan hanya request POST yang diterima
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit();
}

// Cek jika pengguna sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melakukan aksi ini.']);
    exit();
}

require_once '../koneksi/koneksiJoin.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id_notifikasi'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ID notifikasi tidak ditemukan.']);
    exit();
}

$id_notifikasi = $data['id_notifikasi'];
$penerima = $_SESSION['user_data']['username'];

// -- START DEBUGGING --
// Menulis variabel ke log error server untuk inspeksi
error_log("[DEBUG] Mencoba update notifikasi. ID: " . $id_notifikasi . ", Penerima: " . $penerima);
// -- END DEBUGGING --

// Update status_baca menjadi 1 (sudah dibaca)
$query = "UPDATE notifikasi SET status_baca = 1 WHERE id_notifikasi = ? AND penerima = ?";
$params = array($id_notifikasi, $penerima);
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt) {
    // Cek apakah ada baris yang terpengaruh untuk memastikan notifikasi milik pengguna yang benar
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        echo json_encode(['success' => true, 'message' => 'Notifikasi berhasil diperbarui.']);
    } else {
        // Tidak ada baris yang diperbarui, ini adalah penyebab masalah yang paling umum.
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false, 
            'message' => 'Tidak ada baris yang cocok ditemukan untuk diupdate. Cek apakah id_notifikasi dan penerima sudah benar.',
            'debug_info' => [
                'id_notifikasi_sent' => $id_notifikasi,
                'penerima_sent' => $penerima
            ]
        ]);
    }
} else {
    // Gagal eksekusi query, kemungkinan besar ada error SQL.
    http_response_code(500); // Internal Server Error
    $errors = sqlsrv_errors();
    error_log("[SQL_ERROR] " . print_r($errors, true)); // Log error ke server
    echo json_encode([
        'success' => false, 
        'message' => 'Gagal memperbarui status notifikasi karena error SQL.',
        'sql_error' => $errors
    ]);
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?> 