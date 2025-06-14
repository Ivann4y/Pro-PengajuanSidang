<?php
include '../koneksi.php';

$query = "SELECT tanggal_sidang, judul, link_detail FROM sidang WHERE tanggal_sidang >= CONVERT(date, GETDATE()) ORDER BY tanggal_sidang ASC";
$stmt = sqlsrv_query($conn, $query);

$sidang = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $sidang[] = $row;
}
header('Content-Type: application/json');
echo json_encode($sidang);
?>