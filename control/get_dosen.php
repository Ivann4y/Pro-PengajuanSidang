<?php
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

$data = [];
$sql = "SELECT nomor_dosen, nama_dosen, prodi FROM Dosen ORDER BY nama_dosen ASC";
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