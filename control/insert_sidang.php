<?php
session_start();
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'dosen') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include '../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelompok = $_POST['id_kelompok'] ?? '';
    $judul = $_POST['judul'] ?? '';
    $jenis_sidang = $_POST['jenis_sidang'] ?? '';
    $nomor_dosen = $_POST['nomor_dosen'] ?? '';
    
    // Validation
    if (empty($id_kelompok) || empty($judul) || empty($jenis_sidang) || empty($nomor_dosen)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi!']);
        exit();
    }
    
    try {
        // Check if kelompok exists
        $check_kelompok = "SELECT id_kelompok FROM Kelompok WHERE id_kelompok = ?";
        $stmt_check = sqlsrv_query($conn, $check_kelompok, [$id_kelompok]);
        
        if (!$stmt_check || !sqlsrv_has_rows($stmt_check)) {
            echo json_encode(['success' => false, 'message' => 'Kelompok tidak ditemukan!']);
            exit();
        }
        
        // Check if dosen exists
        $check_dosen = "SELECT nomor_dosen FROM Dosen WHERE nomor_dosen = ?";
        $stmt_check_dosen = sqlsrv_query($conn, $check_dosen, [$nomor_dosen]);
        
        if (!$stmt_check_dosen || !sqlsrv_has_rows($stmt_check_dosen)) {
            echo json_encode(['success' => false, 'message' => 'Dosen tidak ditemukan!']);
            exit();
        }
        
        // Insert data ke tabel Sidang
        $sql = "INSERT INTO Sidang (id_kelompok, judul, jenis_sidang, nomor_dosen, status, tanggal_pengajuan) 
                VALUES (?, ?, ?, ?, 'Pending', GETDATE())";
        $params = [$id_kelompok, $judul, $jenis_sidang, $nomor_dosen];
        
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        if ($stmt) {
            echo json_encode(['success' => true, 'message' => 'Data sidang berhasil ditambahkan!']);
        } else {
            $errors = sqlsrv_errors();
            $error_message = 'Gagal menambahkan data sidang!';
            if ($errors) {
                $error_message .= ' Error: ' . $errors[0]['message'];
            }
            echo json_encode(['success' => false, 'message' => $error_message]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 