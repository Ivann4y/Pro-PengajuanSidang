<?php

    $serverName = "CELSI\SQLEXPRESS"; // Ganti dengan nama server SQL Server Anda
$connectionOptions = [
    "Database" => "SistemSidang1",
    // "Uid" => "sqladmin",  
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