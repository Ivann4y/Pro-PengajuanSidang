<?php
// Set page title
$title = $title ?? 'Lupa Password - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/LupaPassword.css'];
ob_start();
?>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="/assets/img/Logo_Astratech_White-8.png" alt="Logo" style="height: 60px;">
            <h2 class="mt-2 mb-0">Lupa Password</h2>
            <small class="text-muted">Sistem Pengajuan Sidang</small>
        </div>
        <form action="/forgot-password" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-3">
                <label for="role" class="form-label">Reset Password Sebagai</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="mahasiswa" <?= ($role ?? '') === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                    <option value="dosen" <?= ($role ?? '') === 'dosen' ? 'selected' : '' ?>>Dosen</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
        </form>
        <div class="mt-3 text-center">
            <a href="/login" class="small"><i class="bi bi-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 