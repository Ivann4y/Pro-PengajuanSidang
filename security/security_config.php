<?php
/**
 * Security Configuration
 * File: security/security_config.php
 * Konfigurasi keamanan yang dapat disesuaikan
 */

// Session Configuration
define('SESSION_TIMEOUT_MINUTES', 30);
define('SESSION_REGENERATE_INTERVAL', 300); // 5 menit
define('SESSION_NAME', 'SIDANG_SESSION');

// Rate Limiting Configuration
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_TIMEOUT_WINDOW', 300); // 5 menit
define('PASSWORD_RESET_MAX_ATTEMPTS', 3);
define('PASSWORD_RESET_TIMEOUT_WINDOW', 600); // 10 menit
define('PASSWORD_CHANGE_MAX_ATTEMPTS', 5);
define('PASSWORD_CHANGE_TIMEOUT_WINDOW', 300); // 5 menit

// Password Policy
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 128);
define('PASSWORD_REQUIRE_UPPERCASE', true);
define('PASSWORD_REQUIRE_LOWERCASE', true);
define('PASSWORD_REQUIRE_NUMBERS', true);
define('PASSWORD_REQUIRE_SPECIAL_CHARS', true);

// Token Configuration
define('RESET_TOKEN_LENGTH', 32);
define('RESET_TOKEN_EXPIRY_MINUTES', 15);
define('CSRF_TOKEN_LENGTH', 32);

// Security Headers
define('ENABLE_SECURITY_HEADERS', true);
define('ENABLE_CSP', true);
define('ENABLE_HSTS', true);
define('ENABLE_XSS_PROTECTION', true);

// Logging Configuration
define('ENABLE_SECURITY_LOGGING', true);
define('LOG_FILE_PATH', '../logs/security.log');
define('LOG_MAX_SIZE', 10485760); // 10MB
define('LOG_ROTATION_DAYS', 30);

// Database Security
define('DB_CONNECTION_TIMEOUT', 30);
define('DB_QUERY_TIMEOUT', 60);
define('MAX_QUERY_RESULTS', 1000);

// Input Validation
define('MAX_INPUT_LENGTH', 1000);
define('MAX_USERNAME_LENGTH', 50);
define('MAX_EMAIL_LENGTH', 100);
define('MAX_PASSWORD_LENGTH', 128);

// File Upload Security
define('ALLOWED_FILE_TYPES', ['pdf', 'docx', 'doc', 'pptx', 'zip']);
define('MAX_FILE_SIZE', 10485760); // 10MB
define('UPLOAD_PATH', '../uploads/');

// Error Reporting
define('DISPLAY_ERRORS', false);
define('LOG_ERRORS', true);
define('ERROR_LOG_PATH', '../logs/error.log');

// IP Whitelist (optional)
define('ENABLE_IP_WHITELIST', false);
define('ALLOWED_IPS', [
    '127.0.0.1',
    '::1',
    // Tambahkan IP yang diizinkan di sini
]);

// Maintenance Mode
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_ALLOWED_IPS', [
    '127.0.0.1',
    '::1',
]);

// Email Configuration
define('EMAIL_FROM_ADDRESS', 'sidangastra@gmail.com');
define('EMAIL_FROM_NAME', 'Admin Pengajuan');
define('EMAIL_REPLY_TO', 'sidangastra@gmail.com');

// Backup Configuration
define('ENABLE_AUTO_BACKUP', false);
define('BACKUP_INTERVAL_HOURS', 24);
define('BACKUP_RETENTION_DAYS', 7);
define('BACKUP_PATH', '../backups/');

// Monitoring Configuration
define('ENABLE_ACTIVITY_MONITORING', true);
define('MONITOR_LOGIN_ATTEMPTS', true);
define('MONITOR_PASSWORD_CHANGES', true);
define('MONITOR_FILE_UPLOADS', true);
define('MONITOR_DATABASE_QUERIES', false);

// Alert Configuration
define('ENABLE_SECURITY_ALERTS', false);
define('ALERT_EMAIL_ADDRESS', 'admin@astra.ac.id');
define('ALERT_ON_FAILED_LOGIN', true);
define('ALERT_ON_SUSPICIOUS_ACTIVITY', true);

// Session Security
define('SESSION_SECURE_COOKIES', false); // Set true untuk HTTPS
define('SESSION_HTTP_ONLY', true);
define('SESSION_SAME_SITE', 'Lax');

// CSRF Protection
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRY', 3600); // 1 jam

// Password History
define('PASSWORD_HISTORY_COUNT', 5);
define('PASSWORD_REUSE_INTERVAL_DAYS', 365);

// Account Lockout
define('ACCOUNT_LOCKOUT_ENABLED', true);
define('ACCOUNT_LOCKOUT_THRESHOLD', 5);
define('ACCOUNT_LOCKOUT_DURATION', 900); // 15 menit

// Two-Factor Authentication (future implementation)
define('ENABLE_2FA', false);
define('2FA_METHOD', 'email'); // email, sms, authenticator

// API Security (if applicable)
define('API_RATE_LIMIT', 100); // requests per hour
define('API_KEY_REQUIRED', false);
define('API_TIMEOUT', 30);

// Content Security Policy
define('CSP_DEFAULT_SRC', "'self'");
define('CSP_SCRIPT_SRC', "'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com");
define('CSP_STYLE_SRC', "'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com");
define('CSP_FONT_SRC', "'self' fonts.gstatic.com");
define('CSP_IMG_SRC', "'self' data:");
define('CSP_CONNECT_SRC', "'self'");

// Function to get configuration value
function getSecurityConfig($key, $default = null) {
    if (defined($key)) {
        return constant($key);
    }
    return $default;
}

// Function to validate configuration
function validateSecurityConfig() {
    $errors = [];
    
    // Validate required directories
    $requiredDirs = [
        dirname(LOG_FILE_PATH),
        dirname(ERROR_LOG_PATH),
        UPLOAD_PATH
    ];
    
    foreach ($requiredDirs as $dir) {
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            $errors[] = "Cannot create directory: $dir";
        }
    }
    
    // Validate file permissions
    if (is_file(LOG_FILE_PATH) && !is_writable(LOG_FILE_PATH)) {
        $errors[] = "Log file is not writable: " . LOG_FILE_PATH;
    }
    
    return $errors;
}

// Initialize security configuration
if (ENABLE_SECURITY_LOGGING && !is_dir(dirname(LOG_FILE_PATH))) {
    mkdir(dirname(LOG_FILE_PATH), 0755, true);
}

if (LOG_ERRORS && !is_dir(dirname(ERROR_LOG_PATH))) {
    mkdir(dirname(ERROR_LOG_PATH), 0755, true);
}

if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
?> 