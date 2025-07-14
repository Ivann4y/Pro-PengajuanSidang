<?php
// Set page title
$title = $title ?? 'Pengajuan Sidang - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mPengajuan.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Pengajuan Sidang</h2>
            <p class="text-muted">Silakan lengkapi data berikut untuk mengajukan sidang.</p>
        </div>
    </div>
    <form action="/mahasiswa/submit-pengajuan" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="mb-3">
            <label for="judul" class="form-label">Judul Tugas Akhir</label>
            <input type="text" class="form-control" id="judul" name="judul" required value="<?= htmlspecialchars($existingPengajuan['judul'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="abstrak" class="form-label">Abstrak</label>
            <textarea class="form-control" id="abstrak" name="abstrak" rows="3" required><?= htmlspecialchars($existingPengajuan['abstrak'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="metodologi" class="form-label">Metodologi</label>
            <textarea class="form-control" id="metodologi" name="metodologi" rows="2" required><?= htmlspecialchars($existingPengajuan['metodologi'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="kelompok_id" class="form-label">Kelompok</label>
            <select class="form-select" id="kelompok_id" name="kelompok_id" required>
                <option value="">Pilih Kelompok</option>
                <?php foreach ($kelompok as $k): ?>
                    <option value="<?= htmlspecialchars($k['id']) ?>" <?= (isset($existingPengajuan['kelompok_id']) && $existingPengajuan['kelompok_id'] == $k['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kelompok']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="laporan" class="form-label">Upload Laporan (PDF/DOCX)</label>
            <input type="file" class="form-control" id="laporan" name="laporan" accept=".pdf,.doc,.docx" required>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Ajukan Sidang</button>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 