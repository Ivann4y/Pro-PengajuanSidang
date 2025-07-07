<?php
session_start();
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

$jenis_sidang = $_GET['jenis_sidang'] ?? '';
$current_dosen = $_SESSION['user_data']['nomor_dosen'] ?? null;
$tahun_ajaran = $_GET['tahun_ajaran'] ?? date('Y');

try {
    if ($jenis_sidang === 'Semester' && $current_dosen) {
        // Get mata kuliah for semester exams where current dosen is pengampu
        $sql = "SELECT DISTINCT mk.id_matkul, mk.nama_matkul 
                FROM MataKuliah mk
                JOIN Pengampu_Kelas pk ON mk.id_matkul = pk.id_matkul 
                WHERE mk.id_matkul != 2006 
                  AND pk.nomor_dosen = ? 
                  AND pk.tahun_ajaran = ?
                ORDER BY mk.nama_matkul ASC";
        $stmt = sqlsrv_query($conn, $sql, [$current_dosen, $tahun_ajaran]);
        
        $result = [];
        if ($stmt) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $result[] = [
                    'id_matkul' => $row['id_matkul'],
                    'nama_matkul' => $row['nama_matkul']
                ];
            }
        }
        echo json_encode($result);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode([]);
}
?> 