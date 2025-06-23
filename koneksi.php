<?php
$serverName = "DESKTOP-M7H7C9C\SQLEXPRESS01";
$connectionOptions = [
    "Database" => "SistemSidang1",
    // "Uid" => "sqladmin",  
    // "PWD" => "sistemsidang",  
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