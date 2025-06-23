<?php
$serverName = "DESKTOP-5SK4QO2\SQLEXPRESS";
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