<?php
// Set up error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use the server name AND the dynamic port number you found
$serverName = "DESKTOP-FEILR9J\SQLEXPRESS,59123"; 

$connectionOptions = [
    "Database" => "SistemSidang1",
    "Uid" => "sqladmin",
    "PWD" => "sistemsidang",
    "TrustServerCertificate" => true,
    "ReturnDatesAsStrings" => true,
];

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    echo "<h1>Koneksi ke Database Gagal!</h1>";
    echo "<p>Periksa kembali detail koneksi di `koneksiDhonnan.php`.</p>";
    echo "<pre>";
    die(print_r(sqlsrv_errors(), true));
    echo "</pre>";
}

// If the script reaches here, the connection is successful. No "echo" needed.
?>