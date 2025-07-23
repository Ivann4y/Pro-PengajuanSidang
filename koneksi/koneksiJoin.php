<?php
// Load the developer's local server name
if (file_exists(__DIR__ . '/servername.php')) {
    require __DIR__ . '/servername.php';
} else {
    die("No local servername defined. Please create servername.php");
}

$connectionOptions = [
    "Database" => "SistemSidang1",
    "TrustServerCertificate" => true,
    "LoginTimeout" => 2,
    // "UID" => "username", "PWD" => "password",
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    echo "Koneksi Gagal ke server: $serverName<br>";
    die(print_r(sqlsrv_errors(), true));
}

// lanjutkan dengan pemakaian $conn
?>