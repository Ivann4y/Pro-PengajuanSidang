<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get nomor_dosen from form (autocomplete) or session
    $dosen_nomor_array = isset($_POST['dosen_nomor_hidden']) ? $_POST['dosen_nomor_hidden'] : [];
    $nomor_dosen = isset($_SESSION['user_data']['nomor_dosen']) ? $_SESSION['user_data']['nomor_dosen'] : null;
    
    // Ensure the creator dosen is always included
    if ($nomor_dosen) {
        // Remove empty values and add creator if not already present
        $dosen_nomor_array = array_filter($dosen_nomor_array, function($value) {
            return trim($value) !== '';
        });
        
        // Add creator if not already in the array
        if (!in_array($nomor_dosen, $dosen_nomor_array)) {
            $dosen_nomor_array[] = $nomor_dosen;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dosen pembimbing tidak ditemukan. Silakan login kembali.']);
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

    // 3. Insert into Bimbingan for each dosen (including creator)
    foreach ($dosen_nomor_array as $dosen_nomor) {
        if (trim($dosen_nomor) !== '') {
            $sql_bimbingan = "INSERT INTO Bimbingan (nomor_dosen, id_kelompok, isPembimbing) VALUES (?, ?, ?)";
            $stmt_bimbingan = sqlsrv_query($conn, $sql_bimbingan, [$dosen_nomor, $id_kelompok, 1]);
            
            if ($stmt_bimbingan === false) {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan dosen pembimbing']);
                exit();
            }

            // 4. Update each Dosen's isPembimbing status to 1
            $sql_update_dosen = "UPDATE Dosen SET isPembimbing = 1 WHERE nomor_dosen = ?";
            $stmt_update_dosen = sqlsrv_query($conn, $sql_update_dosen, [$dosen_nomor]);
            
            if ($stmt_update_dosen === false) {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status pembimbing dosen']);
                exit();
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Kelompok berhasil dibuat!']);
    exit();
}
?> 