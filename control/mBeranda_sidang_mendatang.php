<?php
session_start();
include '../koneksi/koneksiJoin.php';
header('Content-Type: application/json');

if (!isset($_SESSION['nim'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}
$nim = $_SESSION['nim'];

$query = "SELECT s.id_sidang, s.judul, j.tanggal_sidang
FROM Sidang s
JOIN Jadwal j ON s.id_sidang = j.id_sidang
WHERE s.id_kelompok IN (
    SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
)
AND s.status_sidang = 1
AND j.tanggal_sidang > GETDATE()
ORDER BY j.tanggal_sidang ASC;";
$params = [$nim];
$stmt = sqlsrv_query($conn, $query, $params);
if ($stmt === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}
$sidang_mendatang = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Format tanggal_sidang as Y-m-d
    if ($row['tanggal_sidang'] instanceof DateTime) {
        $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d');
    } else {
        $row['tanggal_sidang'] = date('Y-m-d', strtotime($row['tanggal_sidang']));
    }
    $sidang_mendatang[] = [
        'id_sidang' => $row['id_sidang'],
        'judul' => $row['judul'],
        'tanggal_sidang' => $row['tanggal_sidang']
    ];
}
echo json_encode(['sidang_mendatang' => $sidang_mendatang]); 