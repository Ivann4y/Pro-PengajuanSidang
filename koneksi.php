<?php
// Kredentsial koneksi ke database
$serverName = "sidangdevenv.database.windows.net";
$connectionOptions = [
    "Database" => "SistemSidang",
    "Uid" => "SSidangDeveloper",  
    "PWD" => "timDeveloperSidang1",  
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