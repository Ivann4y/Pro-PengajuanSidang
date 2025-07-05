<?php
include '../koneksi/koneksiAndrew.php';

$query = "SELECT COUNT(*) AS jumlah_pengajuan_perlu_aksi FROM Sidang WHERE status_ajuan = 'Pending'";
$stmt = sqlsrv_query($conn, $query);

$jumlah = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($jumlah);
?>