<?php
session_start();
require_once '../koneksi/koneksiAndrew.php'; 
require_once '../function/cobamailer.php';
require_once '../security/security_helper.php';

// Set security headers
setSecurityHeaders();

// Validasi method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logSuspiciousActivity('INVALID_METHOD', 'Non-POST request to authEmail.php');
    header("Location: lupaPassword.php?error=invalid&role=guest");
    exit();
}

// Rate limiting untuk password reset requests
if (!checkRateLimit('password_reset', 3, 600)) { // 3 attempts dalam 10 menit
    logSuspiciousActivity('RATE_LIMIT_EXCEEDED', 'Password reset attempts exceeded limit');
    header("Location: lupaPassword.php?error=rate_limit&role=guest");
    exit();
}

// Sanitasi dan validasi input
$email = sanitizeInput($_POST['emailAstra'] ?? '');
$role = sanitizeInput($_POST['role'] ?? '');
$tableNama = sanitizeInput($_POST['tableNama'] ?? '');
$emailKolom = sanitizeInput($_POST['emailKolom'] ?? 'email');

// Validasi role
if (!validateRole($role)) {
    logSuspiciousActivity('INVALID_ROLE', "Invalid role in password reset: $role");
    header("Location: lupaPassword.php?error=invalid&role=guest");
    exit();
}

// Validasi input tidak boleh kosong
if (empty($email)) {
    incrementRateLimit('password_reset');
    header("Location: lupaPassword.php?error=empty&role=$role");
    exit();
}

// Validasi format email
if (!validateEmail($email)) {
    incrementRateLimit('password_reset');
    logSuspiciousActivity('INVALID_EMAIL', "Invalid email format: $email");
    header("Location: lupaPassword.php?error=invalid&role=$role");
    exit();
}

// Validasi panjang email
if (strlen($email) > 100) {
    logSuspiciousActivity('LONG_EMAIL', "Email too long: " . strlen($email));
    incrementRateLimit('password_reset');
    header("Location: lupaPassword.php?error=invalid&role=$role");
    exit();
}

// Validasi table name untuk mencegah SQL injection
$validTables = ['Mahasiswa', 'Dosen', 'Admin'];
if (!in_array($tableNama, $validTables)) {
    logSuspiciousActivity('INVALID_TABLE', "Invalid table name: $tableNama");
    header("Location: lupaPassword.php?error=invalid&role=$role");
    exit();
}

// Validasi email kolom
$validEmailColumns = ['email'];
if (!in_array($emailKolom, $validEmailColumns)) {
    logSuspiciousActivity('INVALID_EMAIL_COLUMN', "Invalid email column: $emailKolom");
    header("Location: lupaPassword.php?error=invalid&role=$role");
    exit();
}

try {
    // Query untuk validasi email & role di database
    $sql = "SELECT * FROM [dbo].[$tableNama] WHERE [$emailKolom] = ? AND role = ?";
    $params = [$email, $role];
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        throw new Exception("Query execution failed: " . print_r(sqlsrv_errors(), true));
    }
    
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {
        // Generate secure token
        $token = generateSecureToken(32);
        
        // Set timezone dan expiry time
        date_default_timezone_set('Asia/Jakarta');
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Insert token ke database
        $insertSql = "INSERT INTO password_resets (email, role, token, expires_at, used) VALUES (?, ?, ?, ?, 0)";
        $insertParams = [$email, $role, $token, $expires];
        
        $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);
        
        if ($insertStmt === false) {
            throw new Exception("Failed to insert reset token: " . print_r(sqlsrv_errors(), true));
        }
        
        // Kirim email reset password
        $result = sendResetPasswordEmail($email, $user['username'], $token);

        if ($result['success']) {
            // Set session data
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_role'] = $role;
            $_SESSION['reset_table'] = $tableNama;
            $_SESSION['reset_time'] = time();
            
            // Log successful password reset request
            logSuspiciousActivity('PASSWORD_RESET_REQUESTED', "Email: $email, Role: $role, IP: " . $_SERVER['REMOTE_ADDR']);
            
            header("Location: lupaPassword.php?success=1&role=$role");
            exit();
        } else {
            // Log email sending failure
            logSuspiciousActivity('EMAIL_SEND_FAILED', "Failed to send email to: $email, Error: " . $result['message']);
            
            // Hapus token yang gagal dikirim
            $deleteSql = "DELETE FROM password_resets WHERE token = ?";
            sqlsrv_query($conn, $deleteSql, [$token]);
            
            incrementRateLimit('password_reset');
            header("Location: lupaPassword.php?error=mail&role=$role");
            exit();
        }
    } else {
        // Email tidak ditemukan
        incrementRateLimit('password_reset');
        logSuspiciousActivity('EMAIL_NOT_FOUND', "Email not found: $email, Role: $role");
        header("Location: lupaPassword.php?error=notfound&role=$role");
        exit();
    }
    
} catch (Exception $e) {
    // Log error
    logSuspiciousActivity('PASSWORD_RESET_ERROR', "Database error: " . $e->getMessage());
    error_log("Password reset error: " . $e->getMessage());
    
    incrementRateLimit('password_reset');
    header("Location: lupaPassword.php?error=system&role=$role");
    exit();
}
?>                                                                                                                                                                                                                                                                         