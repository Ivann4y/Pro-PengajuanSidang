<?php
session_start();
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['emailAstra'];
    $emailFound = false;

    foreach ($users as $role => $userList) {
        foreach ($userList as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                // Simpan ke session (optional)
                $_SESSION['reset_user'] = $user;
                $_SESSION['reset_role'] = $role;

                // Arahkan ke halaman ganti password
                header("Location: inputPasswordBaru.php");
                exit();
            }
        }
    }

    // Kalau tidak ditemukan
    header("Location: lupaPassword.php?error=email");
    exit();
}
?>
