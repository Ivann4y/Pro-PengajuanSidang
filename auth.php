<?php
session_start();
require "koneksi/koneksiAndrew.php";
require_once "security/security_helper.php";
require_once "security/session_middleware.php";

// Set security headers
setSecurityHeaders();

// Validasi method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logSuspiciousActivity('INVALID_METHOD', 'Non-POST request to auth.php');
    header("Location: index.php");
    exit();
}

// Rate limiting untuk login attempts
if (!checkRateLimit('login', 5, 300)) { // 5 attempts dalam 5 menit
    logSuspiciousActivity('RATE_LIMIT_EXCEEDED', 'Login attempts exceeded limit');
    $_SESSION['login_error'] = 'Terlalu banyak percobaan login. Silakan coba lagi dalam 5 menit.';
    header("Location: index.php");
    exit();
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    logSuspiciousActivity('CSRF_ATTACK', 'Invalid CSRF token');
    $_SESSION['login_error'] = 'Token keamanan tidak valid. Silakan coba lagi.';
    header("Location: index.php");
    exit();
}

// Sanitasi dan validasi input
$role = sanitizeInput($_POST['role'] ?? '');
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? ''; // Password tidak perlu htmlspecialchars

// Validasi role
if (!validateRole($role)) {
    logSuspiciousActivity('INVALID_ROLE', "Invalid role: $role");
    $_SESSION['login_error'] = 'Role tidak valid!';
    header("Location: index.php");
    exit();
}

// Validasi input tidak boleh kosong
if (empty($username) || empty($password)) {
    incrementRateLimit('login');
    header("Location: views/$role/{$role[0]}Login.php?error=empty&username=" . urlencode($username));
    exit();
}

// Validasi panjang username (mencegah buffer overflow)
if (strlen($username) > 50) {
    logSuspiciousActivity('LONG_USERNAME', "Username too long: " . strlen($username));
    incrementRateLimit('login');
    header("Location: views/$role/{$role[0]}Login.php?error=invalid&username=" . urlencode($username));
    exit();
}

// Mapping table berdasarkan role
$tableNama = '';
$usernameKolom = '';
$passwordKolom = 'password_hash';
$redirectPath = '';

switch ($role) {
    case 'mahasiswa':
        $tableNama = 'Mahasiswa';
        $usernameKolom = 'username';
        $redirectPath = 'views/mahasiswa/mBeranda.php';
        break;
    case 'dosen':
        $tableNama = 'Dosen';
        $usernameKolom = 'username';
        $redirectPath = 'views/dosen/dBeranda.php';
        break;
    case 'admin':
        $tableNama = 'Admin';
        $usernameKolom = 'username';
        $redirectPath = 'views/admin/aBeranda.php';
        break;
    default:
        logSuspiciousActivity('INVALID_ROLE_SWITCH', "Invalid role in switch: $role");
        $_SESSION['login_error'] = 'Role tidak valid!';
        header("Location: index.php");
        exit();
}

try {
    // Query dengan prepared statement
    $sql = "SELECT * FROM [dbo].[$tableNama] WHERE [$usernameKolom] = ?";
    $params = [$username];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        throw new Exception("Query execution failed: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Verifikasi password
        if (password_verify($password, $user[$passwordKolom])) {
            // Login berhasil - hapus password dari array
            unset($user[$passwordKolom]);

            // Set session data yang aman menggunakan middleware
            setSecureSessionData($user, $role);

            // Log successful login
            logSuspiciousActivity('LOGIN_SUCCESS', "User: $username, Role: $role, IP: " . $_SERVER['REMOTE_ADDR']);

            // Redirect ke dashboard
            header("Location: " . $redirectPath);
            exit();
        }
    }

    // Login gagal
    incrementRateLimit('login');
    logSuspiciousActivity('LOGIN_FAILED', "Failed login attempt for username: $username, role: $role");
    
    $_SESSION['login_error'] = 'Username atau Password salah!';
    header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username) . "&role=" . urlencode($role));
    exit();

} catch (Exception $e) {
    // Log error
    logSuspiciousActivity('LOGIN_ERROR', "Database error: " . $e->getMessage());
    error_log("Login error: " . $e->getMessage());
    
    $_SESSION['login_error'] = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username) . "&role=" . urlencode($role));
    exit();
}
?>
