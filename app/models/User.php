<?php

abstract class User extends Model {
    protected $username;
    protected $email;
    protected $password_hash;
    protected $role;
    protected $created_at;
    
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM [dbo].[{$this->table}] WHERE [username] = ?";
        $user = $this->db->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM [dbo].[{$this->table}] WHERE [username] = ?";
        return $this->db->fetchOne($sql, [$username]);
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM [dbo].[{$this->table}] WHERE [email] = ?";
        return $this->db->fetchOne($sql, [$email]);
    }
    
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE [dbo].[{$this->table}] SET [password_hash] = ? WHERE [{$this->primaryKey}] = ?";
        return $this->db->execute($sql, [$hashedPassword, $id]);
    }
    
    public function createUser($data) {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        // Set created_at if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->create($data);
    }
    
    public function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[@$!%*?&]/', $password)) {
            $errors[] = 'Password must contain at least one special character (@$!%*?&)';
        }
        
        return $errors;
    }
    
    public function isPasswordValid($password) {
        return empty($this->validatePassword($password));
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    public function isDosen() {
        return $this->role === 'dosen';
    }
    
    public function isMahasiswa() {
        return $this->role === 'mahasiswa';
    }
} 