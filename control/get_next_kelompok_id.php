<?php
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

$tahun_ajaran = $_GET['tahun_ajaran'] ?? date('Y');
$jenis_sidang = $_GET['jenis_sidang'] ?? '';
$id_matkul = $_GET['id_matkul'] ?? '';

if (empty($jenis_sidang) || empty($id_matkul)) {
    echo json_encode(['error' => 'tahun_ajaran, jenis_sidang, dan id_matkul harus disediakan']);
    exit();
}

$sql = "SELECT ISNULL(MAX(nomor_kelompok), 0) + 1 AS next_nomor 
        FROM Kelompok 
        WHERE tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";

$result = sqlsrv_query($conn, $sql, [$tahun_ajaran, $jenis_sidang, $id_matkul]);

if ($result === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}

$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
echo json_encode(['next_nomor' => $row['next_nomor']]);

sqlsrv_free_stmt($result);
sqlsrv_close($conn);