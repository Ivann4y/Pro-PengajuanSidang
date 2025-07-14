<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($conn)) {
    // Pastikan $conn sudah di-include sebelum include file ini!
    require_once __DIR__ . '/../koneksi/koneksiAndrew.php';
}

$unread_notifications = [];
$unread_count = 0;

if (!isset($_SESSION['role']) || !isset($_SESSION['user_data'])) {
    // Tidak login, tidak ada notif
    return;
}

$role = $_SESSION['role'];
$user_data = $_SESSION['user_data'];

$penerima = null;
if ($role === 'admin' && isset($user_data['username'])) {
    $penerima = $user_data['username'];
} elseif ($role === 'dosen' && isset($user_data['nomor_dosen'])) {
    $penerima = (string)$user_data['nomor_dosen'];
} elseif ($role === 'mahasiswa' && isset($user_data['nim'])) {
    $penerima = $user_data['nim'];
}

if ($penerima) {
    $query_unread = "SELECT id_notifikasi FROM notifikasi WHERE penerima = ? AND (status_baca = 0 OR status_baca IS NULL)";
    $stmt_unread = sqlsrv_query($conn, $query_unread, array($penerima));
    if ($stmt_unread) {
        while ($row = sqlsrv_fetch_array($stmt_unread, SQLSRV_FETCH_ASSOC)) {
            $unread_notifications[] = $row;
        }
    }
    $unread_count = count($unread_notifications);
}
?> 