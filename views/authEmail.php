<?php
session_start();
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailAstra']);
    $role = $_POST['role'] ?? 'guest';  // Ambil role dari POST

    // Validasi input
    if (empty($email)) {
        header("Location: lupaPassword.php?error=empty&role=$role");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: lupaPassword.php?error=invalid&role=$role");
        exit();
    }

    // Cek hanya di role saat ini
    if (!isset($users[$role])) {
        header("Location: lupaPassword.php?error=email&role=$role");
        exit();
    }

    $emailFound = false;
    foreach ($users[$role] as $user) {
        if (isset($user['email']) && $user['email'] === $email) {
            $_SESSION['reset_user'] = $user;
            $_SESSION['reset_role'] = $role;

            header("Location: inputPasswordBaru.php?role=$role&email=" . urlencode($email));
            exit();
        }
    }

    header("Location: lupaPassword.php?error=email&role=$role");
    exit();
}
?>
