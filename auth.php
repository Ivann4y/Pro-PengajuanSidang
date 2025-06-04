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
    if ($role === 'mahasiswa') {
        header("Location: views/mahasiswa/mBeranda.php");
        exit();
    }
    if ($role === 'admin') {
        header("Location: views/admin/aBeranda.php");
        exit();
    }
    if ($role === 'dosen') {
        header("Location: views/dosen/dBeranda.php");
        exit(); 
    }
} else {
    echo "Login gagal. <a href='index.php'>Kembali</a>";
}
