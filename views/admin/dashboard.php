<?php
// Set page title
$title = $title ?? 'Dashboard Admin - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/aBeranda.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Selamat Datang, <?= htmlspecialchars($admin['nama'] ?? '') ?></h2>
            <p class="text-muted">Admin Sistem Pengajuan Sidang</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Mahasiswa</h5>
                    <p class="display-6 fw-bold mb-0"><?= htmlspecialchars($totalMahasiswa ?? 0) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Dosen</h5>
                    <p class="display-6 fw-bold mb-0"><?= htmlspecialchars($totalDosen ?? 0) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Sidang</h5>
                    <p class="display-6 fw-bold mb-0"><?= htmlspecialchars($totalSidang ?? 0) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Pengajuan Pending</h5>
                    <p class="display-6 fw-bold mb-0"><?= htmlspecialchars($pengajuanPending ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <h5>Aktivitas Terbaru</h5>
            <?php if (!empty($aktivitasTerbaru)): ?>
                <ul class="list-group">
                    <?php foreach ($aktivitasTerbaru as $aktivitas): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($aktivitas['deskripsi']) ?></span>
                            <span class="badge bg-secondary"><?= date('d/m/Y H:i', strtotime($aktivitas['created_at'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Belum ada aktivitas terbaru.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <h5>Notifikasi Terbaru</h5>
            <?php if (!empty($notifikasi)): ?>
                <ul class="list-group">
                    <?php foreach ($notifikasi as $notif): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($notif['title']) ?>: <?= htmlspecialchars($notif['message']) ?></span>
                            <span class="badge bg-secondary"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Tidak ada notifikasi.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 