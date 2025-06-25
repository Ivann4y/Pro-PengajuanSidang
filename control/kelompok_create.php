<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get nomor_dosen from session
    if (isset($_SESSION['user_data']['nomor_dosen'])) {
        $nomor_dosen = $_SESSION['user_data']['nomor_dosen'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Session dosen tidak ditemukan. Silakan login ulang.']);
        exit();
    }

    $prodi = $_POST['kelompok_prodi'];
    $anggota_nims = $_POST['anggota_nim'];

    // 1. Insert into Kelompok (auto-increment id_kelompok)
    $sql_kelompok = "INSERT INTO Kelompok DEFAULT VALUES";
    $stmt_kelompok = sqlsrv_query($conn, $sql_kelompok);

    if ($stmt_kelompok === false) {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat kelompok']);
        exit();
    }

    // Get the new id_kelompok
    $sql_get_id = "SELECT SCOPE_IDENTITY() AS id_kelompok";
    $stmt_id = sqlsrv_query($conn, $sql_get_id);
    $row = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC);
    $id_kelompok = $row['id_kelompok'];

    // 2. Insert anggota into Kelompok_Mahasiswa
    foreach ($anggota_nims as $nim) {
        if (trim($nim) !== '') {
            $sql_anggota = "INSERT INTO Kelompok_Mahasiswa (id_kelompok, nim) VALUES (?, ?)";
            sqlsrv_query($conn, $sql_anggota, [$id_kelompok, $nim]);
        }
    }

    // 3. Insert into Bimbingan
    $sql_bimbingan = "INSERT INTO Bimbingan (nomor_dosen, id_kelompok, isPembimbing) VALUES (?, ?, ?)";
    sqlsrv_query($conn, $sql_bimbingan, [$nomor_dosen, $id_kelompok, 1]);

    echo json_encode(['success' => true, 'message' => 'Kelompok berhasil dibuat!']);
    exit();
}
?> 