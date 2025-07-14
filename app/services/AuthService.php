<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;
use App\Models\PasswordReset;

class AuthService
{
    private $mahasiswaModel;
    private $dosenModel;
    private $adminModel;
    private $passwordResetModel;

    public function __construct()
    {
        $this->mahasiswaModel = new Mahasiswa();
        $this->dosenModel = new Dosen();
        $this->adminModel = new Admin();
        $this->passwordResetModel = new PasswordReset();
    }

    /**
     * Authenticate user login
     */
    public function authenticate($username, $password, $role)
    {
        // Rate limiting check
        if ($this->isRateLimited($username)) {
            return ['success' => false, 'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit.'];
        }

        $user = null;
        $userModel = null;

        // Get user based on role
        switch ($role) {
            case 'mahasiswa':
                $user = $this->mahasiswaModel->getByNim($username);
                $userModel = $this->mahasiswaModel;
                break;
            case 'dosen':
                $user = $this->dosenModel->getByNip($username);
                $userModel = $this->dosenModel;
                break;
            case 'admin':
                $user = $this->adminModel->getByUsername($username);
                $userModel = $this->adminModel;
                break;
            default:
                return ['success' => false, 'message' => 'Role tidak valid'];
        }

        // Check if user exists
        if (!$user) {
            $this->logFailedLogin($username, $role);
            return ['success' => false, 'message' => 'Username atau password salah'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->logFailedLogin($username, $role);
            return ['success' => false, 'message' => 'Username atau password salah'];
        }

        // Check if account is active
        if (isset($user['status']) && $user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Akun tidak aktif'];
        }

        // Create session
        $this->createSession($user, $role);

        // Log successful login
        $this->logSuccessfulLogin($username, $role);

        // Clear failed login attempts
        $this->clearFailedLoginAttempts($username);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Create user session
     */
    private function createSession($user, $role)
    {
        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user['id'] ?? $user['nim'] ?? $user['nip'] ?? $user['username'];
        $_SESSION['username'] = $user['nama'] ?? $user['username'];
        $_SESSION['role'] = $role;
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Set session timeout (8 hours)
        $_SESSION['expires_at'] = time() + (8 * 60 * 60);
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['role']) && 
               isset($_SESSION['expires_at']) && 
               $_SESSION['expires_at'] > time();
    }

    /**
     * Get current user role
     */
    public function getCurrentRole()
    {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Get current user ID
     */
    public function getCurrentUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Log logout
        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
            $this->logLogout($_SESSION['user_id'], $_SESSION['role']);
        }

        // Destroy session
        session_destroy();

        // Clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }

    /**
     * Validate session security
     */
    public function validateSession()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Check IP address
        if ($_SESSION['ip_address'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
            $this->logout();
            return false;
        }

        // Check user agent
        if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $this->logout();
            return false;
        }

        // Extend session if needed
        if (time() > ($_SESSION['expires_at'] - (60 * 60))) { // 1 hour before expiry
            $_SESSION['expires_at'] = time() + (8 * 60 * 60);
        }

        return true;
    }

    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword, $role)
    {
        // Get user model
        $userModel = $this->getUserModel($role);
        if (!$userModel) {
            return ['success' => false, 'message' => 'Role tidak valid'];
        }

        // Get user
        $user = $this->getUserById($userId, $role);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Password saat ini salah'];
        }

        // Validate new password
        if (!$this->validatePasswordStrength($newPassword)) {
            return ['success' => false, 'message' => 'Password baru tidak memenuhi kriteria keamanan'];
        }

        // Check if new password is same as current
        if (password_verify($newPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Password baru tidak boleh sama dengan password lama'];
        }

        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $success = $userModel->updatePassword($userId, $hashedPassword);

        if ($success) {
            // Log password change
            $this->logPasswordChange($userId, $role);
            
            return ['success' => true, 'message' => 'Password berhasil diubah'];
        } else {
            return ['success' => false, 'message' => 'Gagal mengubah password'];
        }
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset($email, $role)
    {
        // Rate limiting for password reset
        if ($this->isPasswordResetRateLimited($email)) {
            return ['success' => false, 'message' => 'Terlalu banyak permintaan reset password. Silakan coba lagi dalam 1 jam.'];
        }

        // Get user by email
        $user = $this->getUserByEmail($email, $role);
        if (!$user) {
            // Don't reveal if email exists or not
            return ['success' => true, 'message' => 'Jika email terdaftar, link reset password akan dikirim'];
        }

        // Generate reset token
        $token = $this->generateResetToken();
        $expiresAt = date('Y-m-d H:i:s', time() + (60 * 60)); // 1 hour

        // Save reset token
        $resetData = [
            'email' => $email,
            'token' => $token,
            'role' => $role,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->passwordResetModel->create($resetData);

        if ($success) {
            // Send reset email
            $emailSent = $this->sendPasswordResetEmail($email, $token, $role);
            
            if ($emailSent) {
                $this->logPasswordResetRequest($email, $role);
                return ['success' => true, 'message' => 'Link reset password telah dikirim ke email Anda'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengirim email reset password'];
            }
        } else {
            return ['success' => false, 'message' => 'Gagal membuat token reset password'];
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword, $role)
    {
        // Validate token
        $resetRecord = $this->passwordResetModel->getByToken($token);
        
        if (!$resetRecord) {
            return ['success' => false, 'message' => 'Token tidak valid'];
        }

        if ($resetRecord['role'] !== $role) {
            return ['success' => false, 'message' => 'Token tidak valid untuk role ini'];
        }

        if (strtotime($resetRecord['expires_at']) < time()) {
            return ['success' => false, 'message' => 'Token sudah kadaluarsa'];
        }

        // Validate new password
        if (!$this->validatePasswordStrength($newPassword)) {
            return ['success' => false, 'message' => 'Password tidak memenuhi kriteria keamanan'];
        }

        // Get user
        $user = $this->getUserByEmail($resetRecord['email'], $role);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $userModel = $this->getUserModel($role);
        $success = $userModel->updatePassword($user['id'], $hashedPassword);

        if ($success) {
            // Delete reset token
            $this->passwordResetModel->deleteByToken($token);
            
            // Log password reset
            $this->logPasswordReset($resetRecord['email'], $role);
            
            return ['success' => true, 'message' => 'Password berhasil direset'];
        } else {
            return ['success' => false, 'message' => 'Gagal mereset password'];
        }
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password)
    {
        // Minimum 8 characters
        if (strlen($password) < 8) {
            return false;
        }

        // Maximum 128 characters
        if (strlen($password) > 128) {
            return false;
        }

        // Must contain at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Must contain at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Must contain at least one digit
        if (!preg_match('/\d/', $password)) {
            return false;
        }

        // Must contain at least one special character
        if (!preg_match('/[@$!%*?&]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Generate secure reset token
     */
    private function generateResetToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($email, $token, $role)
    {
        // This would integrate with your existing email service
        // For now, we'll just return true
        return true;
    }

    /**
     * Rate limiting for login attempts
     */
    private function isRateLimited($username)
    {
        $attempts = $this->getFailedLoginAttempts($username);
        return count($attempts) >= 5; // Max 5 attempts
    }

    /**
     * Rate limiting for password reset
     */
    private function isPasswordResetRateLimited($email)
    {
        $attempts = $this->getPasswordResetAttempts($email);
        return count($attempts) >= 3; // Max 3 attempts per hour
    }

    /**
     * Get failed login attempts
     */
    private function getFailedLoginAttempts($username)
    {
        // This would query a database table for failed login attempts
        // For now, we'll return an empty array
        return [];
    }

    /**
     * Get password reset attempts
     */
    private function getPasswordResetAttempts($email)
    {
        // This would query a database table for password reset attempts
        // For now, we'll return an empty array
        return [];
    }

    /**
     * Log failed login attempt
     */
    private function logFailedLogin($username, $role)
    {
        // This would log to a database table
        error_log("Failed login attempt: {$username} ({$role}) from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

    /**
     * Log successful login
     */
    private function logSuccessfulLogin($username, $role)
    {
        // This would log to a database table
        error_log("Successful login: {$username} ({$role}) from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

    /**
     * Log logout
     */
    private function logLogout($userId, $role)
    {
        // This would log to a database table
        error_log("Logout: {$userId} ({$role})");
    }

    /**
     * Log password change
     */
    private function logPasswordChange($userId, $role)
    {
        // This would log to a database table
        error_log("Password changed: {$userId} ({$role})");
    }

    /**
     * Log password reset request
     */
    private function logPasswordResetRequest($email, $role)
    {
        // This would log to a database table
        error_log("Password reset requested: {$email} ({$role})");
    }

    /**
     * Log password reset
     */
    private function logPasswordReset($email, $role)
    {
        // This would log to a database table
        error_log("Password reset: {$email} ({$role})");
    }

    /**
     * Clear failed login attempts
     */
    private function clearFailedLoginAttempts($username)
    {
        // This would clear failed login attempts from database
    }

    /**
     * Get user model by role
     */
    private function getUserModel($role)
    {
        switch ($role) {
            case 'mahasiswa':
                return $this->mahasiswaModel;
            case 'dosen':
                return $this->dosenModel;
            case 'admin':
                return $this->adminModel;
            default:
                return null;
        }
    }

    /**
     * Get user by ID
     */
    private function getUserById($userId, $role)
    {
        $userModel = $this->getUserModel($role);
        if (!$userModel) {
            return null;
        }

        switch ($role) {
            case 'mahasiswa':
                return $userModel->getByNim($userId);
            case 'dosen':
                return $userModel->getByNip($userId);
            case 'admin':
                return $userModel->getById($userId);
            default:
                return null;
        }
    }

    /**
     * Get user by email
     */
    private function getUserByEmail($email, $role)
    {
        $userModel = $this->getUserModel($role);
        if (!$userModel) {
            return null;
        }

        return $userModel->getByEmail($email);
    }
} 