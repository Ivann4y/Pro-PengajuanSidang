<?php
// Set page title
$title = $title ?? 'Reset Password - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/inputPasswordBaru.css'];
ob_start();
?>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="/assets/img/Logo_Astratech_White-8.png" alt="Logo" style="height: 60px;">
            <h2 class="mt-2 mb-0">Reset Password</h2>
            <small class="text-muted">Sistem Pengajuan Sidang</small>
        </div>
        <?php if ($reset): ?>
        <form action="/reset-password?token=<?= htmlspecialchars($token) ?>" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-3">
                <label for="newPassword" class="form-label">Kata Sandi Baru</label>
                <div class="input-group">
                    <input type="password" class="form-control <?= in_array($errorType, ['empty', 'short', 'weak_password', 'too_long', 'same_password']) ? 'is-invalid' : '' ?>" id="newPassword" name="newPassword" maxlength="128" required autofocus>
                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword"><i class="bi bi-eye-slash"></i></button>
                </div>
                <?php if ($errorType === 'empty'): ?>
                    <div class="invalid-feedback">Kata sandi tidak boleh kosong.</div>
                <?php elseif ($errorType === 'short'): ?>
                    <div class="invalid-feedback">Kata sandi minimal 8 karakter.</div>
                <?php elseif ($errorType === 'weak_password'): ?>
                    <div class="invalid-feedback">
                        <strong>Kata sandi harus memenuhi:</strong>
                        <ul class="mt-1 mb-0">
                            <?php if (isset($_SESSION['password_errors'])): ?>
                                <?php foreach ($_SESSION['password_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                                <?php unset($_SESSION['password_errors']); ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php elseif ($errorType === 'too_long'): ?>
                    <div class="invalid-feedback">Kata sandi terlalu panjang (maksimal 128 karakter).</div>
                <?php elseif ($errorType === 'same_password'): ?>
                    <div class="invalid-feedback">Kata sandi baru tidak boleh sama dengan kata sandi lama.</div>
                <?php endif; ?>
                <div class="mt-2">
                    <small class="text-muted">Password harus mengandung minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan karakter khusus.</small>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
                <div class="input-group">
                    <input type="password" class="form-control <?= in_array($errorType, ['empty', 'mismatch']) ? 'is-invalid' : '' ?>" id="confirmPassword" name="confirmPassword" maxlength="128" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword"><i class="bi bi-eye-slash"></i></button>
                </div>
                <?php if ($errorType === 'empty'): ?>
                    <div class="invalid-feedback">Konfirmasi kata sandi tidak boleh kosong.</div>
                <?php elseif ($errorType === 'mismatch'): ?>
                    <div class="invalid-feedback">Kata sandi dan konfirmasi tidak cocok.</div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary w-100">Kirim</button>
            <div class="mt-3 text-center">
                <a href="/forgot-password?role=<?= htmlspecialchars($role) ?>" class="small"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle"></i> Token tidak valid atau sudah kadaluarsa.
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    document.getElementById('toggleNewPassword').addEventListener('click', function () {
        const password = document.getElementById('newPassword');
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
    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const password = document.getElementById('confirmPassword');
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