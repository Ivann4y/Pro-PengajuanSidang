<?php
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');
$sql = "SELECT ISNULL(MAX(id_kelompok), 5000) + 1 AS next_id FROM Kelompok";
$result = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
echo json_encode(['next_id' => $row['next_id']]);
sqlsrv_free_stmt($result);
sqlsrv_close($conn); 