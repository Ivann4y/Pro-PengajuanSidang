<?php
// koneksiDhonnan.php

// Set up error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use the loopback IP address (127.0.0.1) and the specific port (59123)
// This is the most reliable way to connect locally.
$serverName = "127.0.0.1,59123"; 

$connectionOptions = [
    "Database" => "SistemSidang1",
    "Uid" => "sqladmin",      // The user we created
    "PWD" => "sistemsidang",  // The password for that user
    "TrustServerCertificate" => true,
    "ReturnDatesAsStrings" => true,
];

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    echo "<h1>Koneksi ke Database Gagal!</h1>";
    echo "<p>Gagal terhubung ke 127.0.0.1. Pastikan port 59123 benar, SQL Server service sedang berjalan, dan user/password di koneksiDhonnan.php sudah benar.</p>";
    echo "<pre>";
    die(print_r(sqlsrv_errors(), true));
    echo "</pre>";
}

// Connection is successful if the script proceeds.
?>