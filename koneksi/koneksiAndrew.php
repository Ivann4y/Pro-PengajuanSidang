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
    "WDYASCONNECT\\SQLEXPRESS", // replaced "/" with "\\"
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
    "LoginTimeout" => 2, // Try 2 seconds per server
    // "UID" => "username", "PWD" => "password", // Uncomment if needed
];

$conn = false;

// 1. Try session server first
if (isset($_SESSION['working_server'])) {
    $serverName = $_SESSION['working_server'];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn !== false) {
        // Connected successfully using session server
        // echo "Connected using session server: $serverName<br>";
    } else {
        // Remove invalid session server
        unset($_SESSION['working_server']);
    }
}

// 2. If not connected, scan the list
if ($conn === false) {
    foreach ($serverList as $serverName) {
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn !== false) {
            // Save working server in session
            $_SESSION['working_server'] = $serverName;
            // echo "Connected to: $serverName<br>";
            break;
        }
    }
}

// 3. If still not connected, show error
if ($conn === false) {
    echo "Koneksi Gagal ke semua server:<br>";
    die(print_r(sqlsrv_errors(), true));
}

// lanjutkan dengan pemakaian $conn
?>
