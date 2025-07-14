<?php

class AuthController extends Controller {
    public function loginForm() {
        // Render login form (bisa gunakan view lama atau baru)
        $this->render('auth/login');
    }

    public function login() {
        $role = $this->post('role');
        $username = $this->post('username');
        $password = $this->post('password');

        // Pilih model sesuai role
        switch ($role) {
            case 'mahasiswa':
                $userModel = new Mahasiswa();
                break;
            case 'dosen':
                $userModel = new Dosen();
                break;
            case 'admin':
                $userModel = new Admin();
                break;
            default:
                $this->setFlash('error', 'Role tidak valid!');
                $this->redirect('/login');
        }

        $user = $userModel->authenticate($username, $password);
        if ($user) {
            $_SESSION['is_logged_in'] = true;
            $_SESSION['role'] = $role;
            $_SESSION['user_data'] = $user;
            // Redirect ke dashboard sesuai role
            switch ($role) {
                case 'mahasiswa':
                    $this->redirect('/mahasiswa/dashboard');
                case 'dosen':
                    $this->redirect('/dosen/dashboard');
                case 'admin':
                    $this->redirect('/admin/dashboard');
            }
        } else {
            $this->setFlash('error', 'Username atau Password salah!');
            $this->redirect('/login');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }

    public function forgotPasswordForm() {
        $this->render('auth/forgot-password');
    }

    public function forgotPassword() {
        // Implementasi pengiriman email reset password
        $email = $this->post('email');
        $role = $this->post('role');
        // Validasi dan generate token, kirim email, dsb
        $this->setFlash('success', 'Link reset password telah dikirim ke email Anda.');
        $this->redirect('/forgot-password');
    }

    public function resetPasswordForm() {
        $this->render('auth/reset-password');
    }

    public function resetPassword() {
        // Implementasi reset password
        $token = $this->post('token');
        $newPassword = $this->post('new_password');
        // Validasi token dan update password
        $this->setFlash('success', 'Password berhasil direset. Silakan login.');
        $this->redirect('/login');
    }
} 