<?php
$serverName = "sidang5.database.windows.net ";
$connectionOptions = [
    "Database" => "SistemSidang1",
    "Uid" => "sqladmin",  
    "PWD" => "RPLsidang5",  
    "TrustServerCertificate" => true,
];






$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
// echo "Koneksi Berhasil!<br>";
?>