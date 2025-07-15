<?php
function kirimNotifikasi($penerima, $pesan, $pengirim, $conn) {
    if (!is_resource($conn)) return false;
    $query = "INSERT INTO notifikasi (penerima, pesan, waktu, status_baca, pengirim) VALUES (?, ?, GETDATE(), 0, ?)";
    $params = array($penerima, $pesan, $pengirim);
    $stmt = sqlsrv_query($conn, $query, $params);
    return $stmt !== false;
} 