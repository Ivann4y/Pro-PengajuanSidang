<?php
session_start();
require_once '../function/cobamailer.php';

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

    // Send reset password email
    $result = sendResetPasswordEmail($email, $email);
    
    if ($result['success']) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_role'] = $role;
        // Tampilkan notifikasi sukses di lupaPassword.php
        header("Location: lupaPassword.php?success=1&role=$role");
        exit();
    } else {
        header("Location: lupaPassword.php?error=mail&role=$role");
        exit();
    }
}
?>
