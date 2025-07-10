<?php
require_once '../security/security_helper.php';

// Set security headers
setSecurityHeaders();

$success = '';
$errorType = '';
$judul = '';
$token = sanitizeInput($_GET['token'] ?? '');
$role = ''; // Akan diisi dari tabel password_resets jika token valid

// Validasi token di database
$reset = null;
if ($token) {
    // Validasi panjang token
    if (strlen($token) !== 64) { // 32 bytes = 64 hex characters
        logSuspiciousActivity('INVALID_TOKEN_LENGTH', "Token length: " . strlen($token));
        $reset = null;
    } else {
        // Gunakan prepared statement yang benar untuk sqlsrv
        $sql = "SELECT * FROM password_resets WHERE token = ? AND used = 0";
        $params = [$token];
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        if ($stmt) {
            $reset = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }
    }
}

// Logika untuk menentukan tanggal kadaluarsa token 
if ($reset) {
    $role = $reset['role']; // role dari database, BUKAN dari GET/POST

    // Mapping table dan kolom berdasarkan role
    switch ($role) {
        case 'mahasiswa':
            $tableNama = 'Mahasiswa';
            $emailKolom = 'email';
            break;
        case 'dosen':
            $tableNama = 'Dosen';
            $emailKolom = 'email';
            break;
        case 'admin':
            $tableNama = 'Admin';
            $emailKolom = 'email';
            break;
        default:
            // Jika role tidak valid, anggap token tidak valid
            logSuspiciousActivity('INVALID_ROLE_IN_TOKEN', "Invalid role in token: $role");
            $reset = null;
    }

    if ($reset) { // Cek lagi setelah switch-case
        date_default_timezone_set('Asia/Jakarta');
        $now = new DateTime();
        $expires_at = $reset['expires_at'];

        // Pastikan expires_at adalah objek DateTime untuk perbandingan
        if (!$expires_at instanceof DateTime) {
            $expires_at = new DateTime($expires_at);
        }

        if (isTokenExpired($expires_at)) {
            logSuspiciousActivity('TOKEN_EXPIRED', "Token expired for email: " . $reset['email']);
            $reset = null;
        }
    }
}

// Rate limiting untuk password reset attempts
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!checkRateLimit('password_change', 5, 300)) { // 5 attempts dalam 5 menit
        logSuspiciousActivity('PASSWORD_CHANGE_RATE_LIMIT', 'Password change attempts exceeded limit');
        header("Location: inputPasswordBaru.php?token=$token&error=rate_limit");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $reset) {
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validasi input tidak boleh kosong
    if (empty($newPassword) || empty($confirmPassword)) {
        incrementRateLimit('password_change');
        header("Location: inputPasswordBaru.php?token=$token&error=empty");
        exit;
    }
    
    // Validasi panjang password
    if (strlen($newPassword) > 128) {
        logSuspiciousActivity('PASSWORD_TOO_LONG', "Password too long: " . strlen($newPassword));
        incrementRateLimit('password_change');
        header("Location: inputPasswordBaru.php?token=$token&error=too_long");
        exit;
    }
    
    // Validasi password strength
    $passwordErrors = validatePasswordStrength($newPassword);
    if (!empty($passwordErrors)) {
        incrementRateLimit('password_change');
        $errorType = 'weak_password';
        $_SESSION['password_errors'] = $passwordErrors;
        header("Location: inputPasswordBaru.php?token=$token&error=weak_password");
        exit;
    }
    
    // Validasi konfirmasi password
    if ($newPassword !== $confirmPassword) {
        incrementRateLimit('password_change');
        header("Location: inputPasswordBaru.php?token=$token&error=mismatch");
        exit;
    }
    
    // Validasi password tidak boleh sama dengan yang lama
    try {
        $checkOldPasswordSql = "SELECT password_hash FROM dbo.[$tableNama] WHERE $emailKolom = ?";
        $checkStmt = sqlsrv_query($conn, $checkOldPasswordSql, [$reset['email']]);
        
        if ($checkStmt && sqlsrv_has_rows($checkStmt)) {
            $oldUser = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
            if (password_verify($newPassword, $oldUser['password_hash'])) {
                incrementRateLimit('password_change');
                header("Location: inputPasswordBaru.php?token=$token&error=same_password");
                exit;
            }
        }
    } catch (Exception $e) {
        logSuspiciousActivity('PASSWORD_CHECK_ERROR', "Error checking old password: " . $e->getMessage());
    }
    
    try {
        // Hash password baru dan update ke database
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateUserSql = "UPDATE dbo.[$tableNama] SET password_hash = ? WHERE $emailKolom = ?";
        $updateStmt = sqlsrv_query($conn, $updateUserSql, [$hash, $reset['email']]);
        
        if ($updateStmt === false) {
            throw new Exception("Failed to update password: " . print_r(sqlsrv_errors(), true));
        }

        // Tandai token sebagai sudah digunakan
        $updateTokenSql = "UPDATE password_resets SET used = 1 WHERE token = ?";
        sqlsrv_query($conn, $updateTokenSql, [$token]);

        // Hapus semua token yang sudah tidak berlaku untuk email ini
        $deleteOldTokensSql = "DELETE FROM password_resets WHERE (used = 1 OR expires_at < GETDATE()) AND email = ?";
        sqlsrv_query($conn, $deleteOldTokensSql, [$reset['email']]);

        // Log successful password change
        logSuspiciousActivity('PASSWORD_CHANGED_SUCCESS', "Password changed for email: " . $reset['email'] . ", Role: $role");
        
        $success = "Kata sandi berhasil diubah!";
        
        // Clear session data
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_role']);
        unset($_SESSION['reset_table']);
        unset($_SESSION['reset_time']);
        
    } catch (Exception $e) {
        logSuspiciousActivity('PASSWORD_UPDATE_ERROR', "Error updating password: " . $e->getMessage());
        error_log("Password update error: " . $e->getMessage());
        
        incrementRateLimit('password_change');
        header("Location: inputPasswordBaru.php?token=$token&error=system");
        exit;
    }
}

// Cek pesan sukses/error
if (isset($_GET['success'])) {
    $success = "Kata sandi berhasil diubah!";
}
$errorType = $_GET['error'] ?? '';

// Judul berdasarkan role
switch ($role) {
    case 'mahasiswa':
        $judul = 'Ubah Kata Sandi Mahasiswa';
        break;
    case 'dosen':
        $judul = 'Ubah Kata Sandi Dosen';
        break;
    case 'admin':
        $judul = 'Ubah Kata Sandi Admin';
        break;
    default:
        $judul = 'Ubah Kata Sandi';
        break;
}

// Cleanup expired tokens secara berkala
cleanupExpiredTokens($conn);
?>