<?php
session_start();
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

if (!isset($_SESSION['nim'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}
$nim = $_SESSION['nim'];

$query = "SELECT COUNT(*) AS sidang_berlangsung
FROM Sidang s
JOIN Jadwal j ON s.id_sidang = j.id_sidang
WHERE s.id_kelompok IN (
    SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
)
AND s.status_sidang = 1
AND j.tanggal_sidang > GETDATE();";
$params = [$nim];
$stmt = sqlsrv_query($conn, $query, $params);
if ($stmt === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
echo json_encode(['sidang_berlangsung' => $row['sidang_berlangsung']]); 