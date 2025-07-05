<?php
session_start();
include '../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomor_kelompok'])) {
    $nomor_kelompok = $_POST['nomor_kelompok'];
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? date('Y');
    $jenis_sidang = $_POST['jenis_sidang'] ?? '';
    $id_matkul = $_POST['id_matkul'] ?? '';
    
    $nomor_dosen = $_SESSION['user_data']['nomor_dosen'];
    
    // Only allow deletion if the logged-in dosen is pembimbing for this kelompok
    $check = sqlsrv_query($conn, 
        "SELECT COUNT(*) as count FROM Kelompok k 
         JOIN Bimbingan b ON k.id_kelompok = b.id_kelompok 
         WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ? 
         AND b.nomor_dosen = ? AND b.isPembimbing = 1", 
        [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul, $nomor_dosen]);
    
    if ($check && sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC)['count'] > 0) {
        // Get all id_kelompok for this group
        $get_ids = sqlsrv_query($conn, 
            "SELECT id_kelompok FROM Kelompok 
             WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?", 
            [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
        
        $id_kelompok_array = [];
        while ($row = sqlsrv_fetch_array($get_ids, SQLSRV_FETCH_ASSOC)) {
            $id_kelompok_array[] = $row['id_kelompok'];
        }
        
        // Delete from Bimbingan and Kelompok
        foreach ($id_kelompok_array as $id_kelompok) {
            sqlsrv_query($conn, "DELETE FROM Bimbingan WHERE id_kelompok = ?", [$id_kelompok]);
        }
        
        $result = sqlsrv_query($conn, 
            "DELETE FROM Kelompok 
             WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?", 
            [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus kelompok.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Anda tidak berhak menghapus kelompok ini.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']); 