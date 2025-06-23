<?php
$serverName = "LAPTOP-6JC0DQKH\SQLEXPRESS";
$connectionOptions = [
    "Database" => "SistemSidang1",
    "TrustServerCertificate" => true,
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
} 
?>