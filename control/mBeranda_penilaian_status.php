<?php
session_start();
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

if (!isset($_SESSION['nim'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}
$nim = $_SESSION['nim'];

$query = "SELECT COUNT(DISTINCT s.id_sidang) AS menunggu_penilaian
FROM Sidang s
JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
WHERE s.id_kelompok IN (
    SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
)
AND s.status_sidang = 1
AND ds.status_revisi != 1;";
$params = [$nim];
$stmt = sqlsrv_query($conn, $query, $params);
if ($stmt === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
echo json_encode(['menunggu_penilaian' => $row['menunggu_penilaian']]); 