<?php
$serverName = "BALTO\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "SistemSidang",
    "TrustServerCertificate" => true,
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
} 
?>