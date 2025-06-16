<?php
session_start();
require 'users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi jika username atau password kosong
    if (empty($username) || empty($password)) {
        header("Location: views/$role/{$role[0]}Login.php?error=empty&username=" . urlencode($username));
        exit();
    }

    // Validasi jika role tidak sesuai
    if (!isset($users[$role])) {
           header("Location: views/$role/{$role[0]}Login.php?error=role");
        exit();
    }

    $found = false;
    foreach ($users[$role] as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['login'] = true;
            $found = true;
            break;
        }
    }

    if ($found) {
        header("Location: views/$role/{$role[0]}Beranda.php");
        exit();
    } else {
        // Validasi jika username atau password salah atau tidak ditemukan
        header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username));
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