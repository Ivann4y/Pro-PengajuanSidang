<?php
// Set page title
$title = $title ?? 'Notifikasi - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mNotifikasi.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Notifikasi</h2>
            <p class="text-muted">Daftar notifikasi terbaru untuk Anda.</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (!empty($notifikasi)): ?>
                        <ul class="list-group">
                            <?php foreach ($notifikasi as $notif): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center <?= $notif['is_read'] ? '' : 'fw-bold' ?>">
                                    <div>
                                        <span><?= htmlspecialchars($notif['title']) ?>:</span>
                                        <span><?= htmlspecialchars($notif['message']) ?></span>
                                    </div>
                                    <span class="badge bg-secondary"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info text-center">Tidak ada notifikasi.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 