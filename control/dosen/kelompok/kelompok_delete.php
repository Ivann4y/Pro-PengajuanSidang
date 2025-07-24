<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include '../../../koneksi/koneksiJoin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']); exit();
}

// Pastikan hanya dosen yang boleh hapus kelompok
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']); exit();
}

$current_dosen = $_SESSION['user_data']['nomor_dosen'] ?? null;
if ($current_dosen === null) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor dosen tidak ditemukan di session.']); exit();
}

// Get data from JSON or POST
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    // Fallback to POST data
    $data = $_POST;
}

$nomor_kelompok = $data['nomor_kelompok'] ?? null;
$tahun_ajaran = $data['tahun_ajaran'] ?? null;
$jenis_sidang = $data['jenis_sidang'] ?? null;
$id_matkul = $data['id_matkul'] ?? null;

// Debug logging
error_log("Delete kelompok data: " . json_encode($data));

// Validation
if (!$nomor_kelompok || !$tahun_ajaran || !$jenis_sidang || !$id_matkul) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']); exit();
}

// Set id_matkul for Tugas Akhir
if ($jenis_sidang === 'Tugas Akhir') {
    $id_matkul = 2006;
}

// Validate dosen permissions for Semester
if ($jenis_sidang === 'Semester') {
    $sql = "SELECT 1 FROM Pengampu_Kelas WHERE nomor_dosen = ? AND id_matkul = ? AND tahun_ajaran = ?";
    $stmt = sqlsrv_query($conn, $sql, [$current_dosen, $id_matkul, $tahun_ajaran]);
    if (!$stmt || !sqlsrv_fetch($stmt)) {
        echo json_encode(['status'=>'error','message'=>'Anda bukan pengampu mata kuliah ini di tahun ajaran ini']); exit();
    }
}

// Lock check: cannot delete if group has Approved Pengajuan
$sql = "SELECT 1 FROM Kelompok k
         JOIN Sidang s ON k.id_kelompok = s.id_kelompok
        WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?
          AND s.status_ajuan = 'Approved'";
$stmt = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
if ($stmt && sqlsrv_fetch($stmt)) {
    echo json_encode(['status' => 'error', 'message' => 'Kelompok tidak bisa dihapus: Sudah ada Pengajuan Approved.']); exit();
}

try {
    sqlsrv_begin_transaction($conn);
    $success = true;
    
    // Get all id_kelompok
    $id_kelompok_list = [];
    $sql = "SELECT id_kelompok FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
    $stmt = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
    
    if (!$stmt) {
        throw new Exception("Error querying kelompok data");
    }
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $id_kelompok_list[] = $row['id_kelompok'];
    }
    
    if (empty($id_kelompok_list)) {
        echo json_encode(['status' => 'error', 'message' => 'Kelompok tidak ditemukan.']); exit();
    }
    
    error_log("Deleting kelompok with IDs: " . json_encode($id_kelompok_list));
    
    // Delete Bimbingan hanya untuk Tugas Akhir
    if ($jenis_sidang === 'Tugas Akhir') {
        foreach ($id_kelompok_list as $idk) {
            $sql2 = "DELETE FROM Bimbingan WHERE id_kelompok = ?";
            $deleteBimbinganResult = sqlsrv_query($conn, $sql2, [$idk]);
            if ($deleteBimbinganResult === false) {
                $errors = sqlsrv_errors();
                error_log("Failed to delete Bimbingan for id_kelompok {$idk}: " . json_encode($errors));
                $success = false; 
                break; 
            }
            
            // Check how many records were deleted
            $deletedRows = sqlsrv_rows_affected($deleteBimbinganResult);
            error_log("Successfully deleted {$deletedRows} Bimbingan records for id_kelompok {$idk}");
        }
    } else {
        error_log("Skipping Bimbingan delete for Semester kelompok {$nomor_kelompok}");
    }
    
    // Delete Kelompok records
    if ($success) {
        foreach ($id_kelompok_list as $idk) {
            $sql3 = "DELETE FROM Kelompok WHERE id_kelompok = ?";
            $deleteKelompokResult = sqlsrv_query($conn, $sql3, [$idk]);
            if ($deleteKelompokResult === false) {
                $errors = sqlsrv_errors();
                error_log("Failed to delete Kelompok with id_kelompok {$idk}: " . json_encode($errors));
                $success = false; 
                break; 
            }
            error_log("Successfully deleted Kelompok with id_kelompok {$idk}");
        }
    }
    
    if ($success) {
        sqlsrv_commit($conn);
        echo json_encode(['status' => 'ok', 'message' => 'Kelompok berhasil dihapus']);
    } else {
        sqlsrv_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Error saat hapus, data di-rollback']);
    }
} catch (Exception $e) {
    sqlsrv_rollback($conn);
    error_log("Kelompok Delete Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>