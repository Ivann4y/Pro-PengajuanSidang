<?php
// Set page title
$title = $title ?? 'Pengajuan Revisi/Perbaikan - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mPerbaikan.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Pengajuan Revisi/Perbaikan</h2>
            <p class="text-muted">Unggah dokumen revisi/perbaikan sidang Anda.</p>
        </div>
    </div>
    <?php if (!empty($sidang)): ?>
    <form action="/mahasiswa/submit-perbaikan/<?= htmlspecialchars($sidang['id']) ?>" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="mb-3">
            <label for="file_revisi" class="form-label">Upload File Revisi (PDF/DOCX/ZIP)</label>
            <input type="file" class="form-control" id="file_revisi" name="file_revisi" accept=".pdf,.doc,.docx,.zip" required>
        </div>
        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan (Opsional)</label>
            <textarea class="form-control" id="catatan" name="catatan" rows="2"></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Kirim Revisi</button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-danger text-center">Sidang tidak ditemukan.</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 