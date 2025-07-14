<?php
// Set page title
$title = $title ?? 'Profil Mahasiswa - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/profil.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Profil Mahasiswa</h2>
            <p class="text-muted">Lihat dan perbarui data profil Anda.</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="/mahasiswa/update-profil" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <div class="mb-3 text-center">
                            <img src="<?= !empty($mahasiswa['foto']) ? '/uploads/profil/' . htmlspecialchars($mahasiswa['foto']) : '/assets/img/profil_default.jpg' ?>" alt="Foto Profil" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Ubah Foto Profil</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($mahasiswa['nama'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($mahasiswa['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($mahasiswa['no_hp'] ?? '') ?>">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 