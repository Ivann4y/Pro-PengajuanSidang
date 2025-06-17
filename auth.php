<?php

session_start();
require "koneksi.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi jika username atau password kosong
    if (empty($username) || empty($password)) {
        header("Location: views/$role/{$role[0]}Login.php?error=empty&username=" . urlencode($username));
        exit();
    }

    $sql = "SELECT username, role, password_hash FROM users WHERE username = ? AND role = ?";
    $params = [$username, $role];

    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        // Error handling jika query gagal
        die(print_r(sqlsrv_errors(), true));
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Cek apakah user ditemukan DAN apakah password yang diinput cocok dengan hash di database
    if ($user && password_verify($password, $user['password_hash'])) {
        // Jika berhasil:
        // Set session
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login'] = true;

        // Arahkan ke halaman beranda sesuai role
        header("Location: views/{$user['role']}/{$user['role'][0]}Beranda.php");
        exit();

    } else {
        // Jika gagal (username/password/role salah):
        // Kembalikan ke halaman login dengan pesan error
        header("Location: views/$role/{$role[0]}Login.php?error=invalid&username=" . urlencode($username));
        exit();
    }
}
// Jika data mengambil dari database

// <?php
// include 'koneksi.php';
// $stmt = sqlsrv_query($conn, "SELECT * FROM users WHERE username=? AND role=?", [$username, $role]);
// $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
// if ($user && password_verify($password, $user['password_hash'])) {
//     // Login sukses
// } else {
//     // Login gagal
// }

?>