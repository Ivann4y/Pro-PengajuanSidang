<?php
// Set page title
$title = $title ?? 'Edit Pengajuan Sidang - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mEditPengajuan.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Edit Pengajuan Sidang</h2>
            <p class="text-muted">Perbarui data pengajuan sidang Anda.</p>
        </div>
    </div>
    <form action="/mahasiswa/update-pengajuan/<?= htmlspecialchars($pengajuan['id']) ?>" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="mb-3">
            <label for="judul" class="form-label">Judul Tugas Akhir</label>
            <input type="text" class="form-control" id="judul" name="judul" required value="<?= htmlspecialchars($pengajuan['judul'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="abstrak" class="form-label">Abstrak</label>
            <textarea class="form-control" id="abstrak" name="abstrak" rows="3" required><?= htmlspecialchars($pengajuan['abstrak'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="metodologi" class="form-label">Metodologi</label>
            <textarea class="form-control" id="metodologi" name="metodologi" rows="2" required><?= htmlspecialchars($pengajuan['metodologi'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="laporan" class="form-label">Upload Laporan Baru (Opsional)</label>
            <input type="file" class="form-control" id="laporan" name="laporan" accept=".pdf,.doc,.docx">
            <?php if (!empty($pengajuan['laporan_path'])): ?>
                <small class="form-text">Laporan saat ini: <a href="/uploads/laporan/<?= htmlspecialchars($pengajuan['laporan_path']) ?>" target="_blank">Lihat</a></small>
            <?php endif; ?>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 