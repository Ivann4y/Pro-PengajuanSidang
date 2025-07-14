<?php

class PasswordReset extends Model {
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'email', 'role', 'token', 'expires_at', 'used'
    ];
    
    public function createResetToken($email, $role) {
        // Delete existing tokens for this email
        $this->deleteExistingTokens($email, $role);
        
        // Generate new token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $resetData = [
            'email' => $email,
            'role' => $role,
            'token' => $token,
            'expires_at' => $expiresAt,
            'used' => 0
        ];
        
        $result = $this->create($resetData);
        
        if ($result) {
            return $token;
        }
        
        return false;
    }
    
    public function validateToken($token, $email, $role) {
        $sql = "SELECT * FROM password_resets 
                WHERE token = ? AND email = ? AND role = ? 
                AND expires_at > NOW() AND used = 0";
        return $this->db->fetchOne($sql, [$token, $email, $role]);
    }
    
    public function markTokenAsUsed($token) {
        $sql = "UPDATE password_resets SET used = 1 WHERE token = ?";
        return $this->db->execute($sql, [$token]);
    }
    
    public function deleteExistingTokens($email, $role) {
        $sql = "DELETE FROM password_resets WHERE email = ? AND role = ?";
        return $this->db->execute($sql, [$email, $role]);
    }
    
    public function cleanupExpiredTokens() {
        $sql = "DELETE FROM password_resets WHERE expires_at < NOW()";
        return $this->db->execute($sql);
    }
    
    public function getResetByToken($token) {
        $sql = "SELECT * FROM password_resets WHERE token = ?";
        return $this->db->fetchOne($sql, [$token]);
    }
    
    public function isTokenExpired($token) {
        $sql = "SELECT COUNT(*) as count FROM password_resets 
                WHERE token = ? AND expires_at < NOW()";
        $result = $this->db->fetchOne($sql, [$token]);
        return $result && $result['count'] > 0;
    }
    
    public function isTokenUsed($token) {
        $sql = "SELECT COUNT(*) as count FROM password_resets 
                WHERE token = ? AND used = 1";
        $result = $this->db->fetchOne($sql, [$token]);
        return $result && $result['count'] > 0;
    }
    
    public function getExpiryTime($token) {
        $sql = "SELECT expires_at FROM password_resets WHERE token = ?";
        $result = $this->db->fetchOne($sql, [$token]);
        return $result ? $result['expires_at'] : null;
    }
    
    public function getTimeUntilExpiry($token) {
        $expiryTime = $this->getExpiryTime($token);
        if (!$expiryTime) {
            return 0;
        }
        
        $expiry = new DateTime($expiryTime);
        $now = new DateTime();
        $diff = $now->diff($expiry);
        
        return $diff->invert ? 0 : ($diff->days * 24 * 60 + $diff->h * 60 + $diff->i);
    }
    
    public function getExpiryDisplay($token) {
        $minutes = $this->getTimeUntilExpiry($token);
        
        if ($minutes <= 0) {
            return 'Token sudah kadaluarsa';
        }
        
        if ($minutes < 60) {
            return "{$minutes} menit tersisa";
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return "{$hours} jam tersisa";
        }
        
        return "{$hours} jam {$remainingMinutes} menit tersisa";
    }
} 