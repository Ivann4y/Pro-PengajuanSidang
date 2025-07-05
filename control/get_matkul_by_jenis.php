<?php
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

$jenis_sidang = $_GET['jenis_sidang'] ?? '';

if (empty($jenis_sidang)) {
    echo json_encode(['error' => 'jenis_sidang parameter is required']);
    exit();
}

$data = [];

if ($jenis_sidang === 'Tugas Akhir') {
    // For Tugas Akhir, only return the Tugas Akhir mata kuliah
    $sql = "SELECT id_matkul, nama_matkul FROM MataKuliah WHERE nama_matkul = 'Tugas Akhir' ORDER BY nama_matkul ASC";
} else {
    // For Semester, return all mata kuliah except Tugas Akhir
    $sql = "SELECT id_matkul, nama_matkul FROM MataKuliah WHERE nama_matkul != 'Tugas Akhir' ORDER BY nama_matkul ASC";
}

$result = sqlsrv_query($conn, $sql);

if ($result === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
sqlsrv_free_stmt($result);
sqlsrv_close($conn);
?> 