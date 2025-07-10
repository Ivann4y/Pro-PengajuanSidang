<?php
/**
 * Security Helper Functions
 * File: security/security_helper.php
 * Fungsi-fungsi keamanan yang digunakan di seluruh aplikasi
 */

// Fungsi untuk generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi untuk validasi CSRF token
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

// Fungsi untuk rate limiting
function checkRateLimit($action, $maxAttempts = 5, $timeWindow = 300) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_{$action}_{$ip}";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    $rateLimit = &$_SESSION[$key];
    
    // Reset jika sudah lewat time window
    if (time() - $rateLimit['first_attempt'] > $timeWindow) {
        $rateLimit = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    // Cek apakah sudah melebihi batas
    if ($rateLimit['attempts'] >= $maxAttempts) {
        return false;
    }
    
    $rateLimit['attempts']++;
    return true;
}

// Fungsi untuk increment rate limit
function incrementRateLimit($action) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_{$action}_{$ip}";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    $_SESSION[$key]['attempts']++;
}

// Fungsi untuk validasi role
function validateRole($role) {
    $validRoles = ['mahasiswa', 'dosen', 'admin'];
    return in_array($role, $validRoles);
}

// Fungsi untuk validasi session
function validateSession($requiredRole = null) {
    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        return false;
    }
    
    if ($requiredRole && $_SESSION['role'] !== $requiredRole) {
        return false;
    }
    
    return true;
}

// Fungsi untuk sanitasi input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Fungsi untuk validasi password strength
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password harus mengandung huruf besar';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password harus mengandung huruf kecil';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password harus mengandung angka';
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password harus mengandung karakter khusus';
    }
    
    return $errors;
}

// Fungsi untuk log aktivitas mencurigakan
function logSuspiciousActivity($action, $details = '') {
    $logEntry = date('Y-m-d H:i:s') . " - IP: " . $_SERVER['REMOTE_ADDR'] . 
                " - Action: " . $action . " - Details: " . $details . "\n";
    error_log($logEntry, 3, '../logs/security.log');
}

// Fungsi untuk set security headers
function setSecurityHeaders() {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:;");
}

// Fungsi untuk generate secure token
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Fungsi untuk validasi token expiry
function isTokenExpired($expiresAt) {
    $now = new DateTime();
    $expires = $expiresAt instanceof DateTime ? $expiresAt : new DateTime($expiresAt);
    return $now > $expires;
}

// Fungsi untuk cleanup expired tokens
function cleanupExpiredTokens($conn) {
    $sql = "DELETE FROM password_resets WHERE expires_at < GETDATE() OR used = 1";
    sqlsrv_query($conn, $sql);
}
?> 