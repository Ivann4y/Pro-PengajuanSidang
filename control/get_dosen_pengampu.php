<?php
include '../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

$id_matkul = $_GET['id_matkul'] ?? '';

if (empty($id_matkul)) {
    echo json_encode(['error' => 'id_matkul parameter is required']);
    exit();
}

$data = [];

// Get dosen pengampu for the specified mata kuliah
$sql = "
    SELECT DISTINCT
        d.nomor_dosen,
        d.nama_dosen,
        d.prodi
    FROM Pengampu_Kelas pk
    JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
    WHERE pk.id_matkul = ?
    ORDER BY d.nama_dosen ASC
";

$result = sqlsrv_query($conn, $sql, [$id_matkul]);

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