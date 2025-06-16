<?php
$serverName = "LAPTOP-7POM2U9J\\SQLEXPRESS"; 
$connectionOptions = [
    "Database" => "ZIa", 
    "Uid" => "sa",
    "PWD" => "mantap",
    "TrustServerCertificate" => true
];

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
echo "Koneksi Berhasil!<br>";
?>
