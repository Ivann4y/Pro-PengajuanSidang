<?php
session_start();
require 'users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

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
        header("Location: views/$role/{$role[0]}Login.php?error=1");
        exit();
    }
}
