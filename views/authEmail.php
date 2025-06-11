<?php
session_start();
require_once '../users.php';  // Asumsi ini berisi data user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailAstra']);
    $role = $_POST['role'] ?? 'guest';

    // Cek apakah email kosong
    if (empty($email)) {
        header("Location: lupaPassword.php?error=empty&role=$role");
        exit();
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: lupaPassword.php?error=invalid&role=$role");
        exit();
    }

    $emailFound = false;
    foreach ($users as $userRole => $userList) {
        foreach ($userList as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                $_SESSION['reset_user'] = $user;
                $_SESSION['reset_role'] = $userRole;

                header("Location: inputPasswordBaru.php?email=" . urlencode($email) . "&role=$userRole");
                exit();
            }
        }
    }

    // Kalau tidak ditemukan
    header("Location: lupaPassword.php?error=email&role=$role");
    exit();
}
?>
