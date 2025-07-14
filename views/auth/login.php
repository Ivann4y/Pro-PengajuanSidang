<?php
// Set page title
$title = $title ?? 'Login - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/login.css'];
ob_start();
?>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="/assets/img/Logo_Astratech_White-8.png" alt="Logo" style="height: 60px;">
            <h2 class="mt-2 mb-0">Sistem Pengajuan Sidang</h2>
            <small class="text-muted">Politeknik Astra</small>
        </div>
        <form action="/login" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-3">
                <label for="role" class="form-label">Login Sebagai</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="mahasiswa" <?= ($role ?? '') === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                    <option value="dosen" <?= ($role ?? '') === 'dosen' ? 'selected' : '' ?>>Dosen</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username / NIM / NIP</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="/forgot-password" class="small">Lupa Password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            password.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 