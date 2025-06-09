<?php
session_start();
require_once '../users.php';  // Asumsi ini berisi data user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailAstra']);

    // Cek apakah email kosong
    if (empty($email)) {
        header("Location: lupaPassword.php?error=empty");
        exit();
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: lupaPassword.php?error=invalid");
        exit();
    }

    $emailFound = false;
    foreach ($users as $role => $userList) {
        foreach ($userList as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                $_SESSION['reset_user'] = $user;
                $_SESSION['reset_role'] = $role;

                header("Location: inputPasswordBaru.php?email=" . urlencode($email));
                exit();
            }
        }
    }

    // Kalau tidak ditemukan
    header("Location: lupaPassword.php?error=email");
    exit();
}
?>
