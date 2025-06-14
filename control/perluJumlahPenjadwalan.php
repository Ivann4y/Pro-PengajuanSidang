<?php
include '../koneksi.php';

$query = "SELECT judul FROM View_aPerluPenjadwalan ORDER BY id_sidang ASC";
$stmt = sqlsrv_query($conn, $query);

$sidangBelumTerjadwal = [];
$jumlah = 0;


while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $sidangBelumTerjadwal[] = $row;
     $jumlah++;
}

$response = [
    "jumlah" => $jumlah,
    "data" => $sidangBelumTerjadwal
];

header('Content-Type: application/json');
echo json_encode($response);
?>