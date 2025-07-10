<?php
/**
 * Session Middleware
 * File: security/session_middleware.php
 * Middleware untuk validasi session dan timeout
 */

require_once __DIR__ . '/security_helper.php';

// Set security headers
setSecurityHeaders();

// Fungsi untuk validasi session timeout
function validateSessionTimeout($timeoutMinutes = 30) {
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }
    
    $timeout = $timeoutMinutes * 60; // Convert to seconds
    if (time() - $_SESSION['last_activity'] > $timeout) {
        // Session expired
        session_destroy();
        return false;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    return true;
}

// Fungsi untuk validasi session hijacking
function validateSessionIntegrity() {
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return true;
    }
    
    if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
        // Possible session hijacking
        logSuspiciousActivity('SESSION_HIJACKING', 'User agent mismatch');
        session_destroy();
        return false;
    }
    
    return true;
}

// Fungsi untuk validasi IP address
function validateSessionIP() {
    if (!isset($_SESSION['ip_address'])) {
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        return true;
    }
    
    if ($_SESSION['ip_address'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
        // IP address changed
        logSuspiciousActivity('IP_CHANGE', 'IP address changed during session');
        session_destroy();
        return false;
    }
    
    return true;
}

// Fungsi utama untuk validasi session
function validateSession($requiredRole = null, $timeoutMinutes = 30) {
    // Cek apakah user sudah login
    if (!validateSession($requiredRole)) {
        return false;
    }
    
    // Cek session timeout
    if (!validateSessionTimeout($timeoutMinutes)) {
        return false;
    }
    
    // Cek session integrity
    if (!validateSessionIntegrity()) {
        return false;
    }
    
    // Cek IP address
    if (!validateSessionIP()) {
        return false;
    }
    
    return true;
}

// Fungsi untuk logout yang aman
function secureLogout() {
    // Log logout activity
    if (isset($_SESSION['user_data']['username'])) {
        logSuspiciousActivity('LOGOUT', 'User logged out: ' . $_SESSION['user_data']['username']);
    }
    
    // Clear all session data
    $_SESSION = array();
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

// Fungsi untuk regenerate session ID secara berkala
function regenerateSessionIfNeeded($regenerateInterval = 300) { // 5 menit
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
        return;
    }
    
    if (time() - $_SESSION['last_regeneration'] > $regenerateInterval) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Fungsi untuk set session data yang aman
function setSecureSessionData($userData, $role) {
    // Clear existing session
    $_SESSION = array();
    
    // Set session data
    $_SESSION['user_data'] = $userData;
    $_SESSION['role'] = $role;
    $_SESSION['is_logged_in'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['last_regeneration'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Set NIM untuk mahasiswa
    if ($role === 'mahasiswa' && isset($userData['nim'])) {
        $_SESSION['nim'] = $userData['nim'];
    }
    
    // Regenerate session ID untuk mencegah session fixation
    session_regenerate_id(true);
}
?> 