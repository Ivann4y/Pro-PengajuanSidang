<?php
$serverName = "celsi.tail7d1f1d.ts.net,1433";
$connectionOptions = [
    "Database" => "SistemSidang1",
    "Uid" => "sqladmin",  
    "PWD" => "sistemsidang",  
    "TrustServerCertificate" => true,
];

// Menjalankan koneksi ke database
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
// echo "Koneksi Berhasil!<br>";
?>