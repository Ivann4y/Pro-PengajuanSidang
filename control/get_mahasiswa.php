<?php

include '../koneksi/koneksiAndrew.php'; // Sesuaikan path jika perlu
header('Content-Type: application/json');

$data = [];

// Query untuk mengambil NIM, nama, dan prodi mahasiswa
$sql = "SELECT nim, nama_mhs, prodi FROM Mahasiswa ORDER BY nama_mhs ASC";
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