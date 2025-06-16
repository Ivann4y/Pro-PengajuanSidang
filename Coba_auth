<?php
session_start();
$serverName = "LAPTOP-7POM2U9J\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "ZIa",
    "TrustServerCertificate" => true
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = 'admin'; // karena dari login admin

if (empty($username) || empty($password)) {
    header("Location: views/admin/login.php?error=empty&username=$username");
    exit;
}

// Cek ke database
$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$params = [$username, $role];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: views/admin/dashboard.php");
        exit;
    }
}

// Kalau gagal login
header("Location: views/admin/login.php?error=1&username=$username");
exit;
