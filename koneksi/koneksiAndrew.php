<?php
$serverList = [
    "DESKTOP-5SK4QO2\\SQLEXPRESS",
    "DESKTOP-4E5I4LF\\SQLEXPRESS",
    "HCCSSYY\\SQLEXPRESS",
    "LAPTOP-7POM2U9J\\SQLEXPRESS",
    "LAPTOP-EEE076JR\\SQLEXPRESS",
    "INCOGNI-CAT\\SQLEXPRESS",
    "MYBOOKHYPE\\SQLEXPRESS",
    "LAPTOP-7B4A2GEF\\SQLEXPRESS",
    "LAPTOP-6JC0DQKH\\SQLEXPRESS",
    "WDYASCONNECT\\SQLEXPRESS", // replaced "/" with "\"
    "RAKHAA\\SQLEXPRESS",
    "LAPTOP-IF4VFNR5\\SQLEXPRESS",
    "DESKTOP-QOLBRVJ\\SQLEXPRESS",
    "DESKTOP-FEILR9J\\SQLEXPRESS,59123", // optional port
    "DESKTOP-M7H7C9C\\SQLEXPRESS01",
    "BALTO\\SQLEXPRESS",
];

$connectionOptions = [
    "Database" => "SistemSidang1",
    "TrustServerCertificate" => true,
    // "UID" => "username", "PWD" => "password", // Uncomment if needed
];

$conn = null;
foreach ($serverList as $serverName) {
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn !== false) {
        echo "Connected to: $serverName<br>";
        break; // stop once successful
    }
}

// If still not connected
if ($conn === false) {
    echo "Koneksi Gagal ke semua server:<br>";
    die(print_r(sqlsrv_errors(), true));
}

// lanjutkan dengan pemakaian $conn
?>
