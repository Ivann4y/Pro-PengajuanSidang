<?php
session_start();

require_once 'security/security_helper.php';
require_once 'security/session_middleware.php';

// Set security headers
setSecurityHeaders();

// Log logout activity sebelum menghapus session
if (isset($_SESSION['user_data']['username'])) {
    logSuspiciousActivity('LOGOUT', 'User logged out: ' . $_SESSION['user_data']['username'] . ', Role: ' . ($_SESSION['role'] ?? 'unknown'));
}

// Gunakan secure logout dari middleware
secureLogout();

// Redirect ke halaman utama
header("Location: index.php");
exit();
?>
