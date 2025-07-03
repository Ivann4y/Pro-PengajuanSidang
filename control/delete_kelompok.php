<?php
session_start();
include '../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_kelompok'])) {
    $id_kelompok = $_POST['id_kelompok'];
    $nomor_dosen = $_SESSION['user_data']['nomor_dosen'];
    // Only allow deletion if the logged-in dosen is pembimbing for this kelompok
    $check = sqlsrv_query($conn, "SELECT * FROM Bimbingan WHERE id_kelompok = ? AND nomor_dosen = ? AND isPembimbing = 1", [$id_kelompok, $nomor_dosen]);
    if (!sqlsrv_has_rows($check)) {
        echo json_encode(['success' => false, 'message' => 'Anda tidak berhak menghapus kelompok ini.']);
        exit;
    }
    // Delete from Bimbingan, Kelompok_Mahasiswa, and Kelompok
    sqlsrv_query($conn, "DELETE FROM Bimbingan WHERE id_kelompok = ?", [$id_kelompok]);
    sqlsrv_query($conn, "DELETE FROM Kelompok_Mahasiswa WHERE id_kelompok = ?", [$id_kelompok]);
    $result = sqlsrv_query($conn, "DELETE FROM Kelompok WHERE id_kelompok = ?", [$id_kelompok]);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus kelompok.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']); 