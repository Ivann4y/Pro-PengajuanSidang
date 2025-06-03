<?php
session_start();
require 'users.php';

$role = $_POST['role'];
$username = $_POST['username'];
$password = $_POST['password'];

if (!isset($users[$role])) {
    echo "Role tidak ditemukan!";
    exit();
}

$found = false;
foreach ($users[$role] as $user) {
    if ($user['username'] === $username && $user['password'] === $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $found = true;
        break;
    }
}

if ($found) {
    // Redirect ke dashboard sesuai role
    header("Location: views/$role/beranda_$role.php");
    exit();
} else {
    echo "Login gagal. <a href='index.php'>Kembali</a>";
}
