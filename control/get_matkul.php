<?php
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

$jenis_sidang = $_GET['jenis_sidang'] ?? '';

$data = [];

// Query untuk mengambil mata kuliah
if ($jenis_sidang === 'Tugas Akhir') {
    // For Tugas Akhir, only show the Tugas Akhir course
    $sql = "SELECT id_matkul, nama_matkul FROM MataKuliah WHERE nama_matkul = 'Tugas Akhir' ORDER BY nama_matkul ASC";
} elseif ($jenis_sidang === 'Semester') {
    // For Semester, show all courses except Tugas Akhir
    $sql = "SELECT id_matkul, nama_matkul FROM MataKuliah WHERE nama_matkul != 'Tugas Akhir' ORDER BY nama_matkul ASC";
} else {
    // Show all courses if no filter
    $sql = "SELECT id_matkul, nama_matkul FROM MataKuliah ORDER BY nama_matkul ASC";
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