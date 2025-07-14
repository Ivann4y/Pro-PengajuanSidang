<?php
// Set page title
$title = $title ?? 'Dashboard Mahasiswa - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mahasiswa-dashboard.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Selamat Datang, <?= htmlspecialchars($mahasiswa['nama'] ?? '') ?></h2>
            <p class="text-muted">NIM: <?= htmlspecialchars($mahasiswa['nim'] ?? '') ?></p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Sidang Mendatang</h5>
                    <?php if (!empty($sidangMendatang)): ?>
                        <p class="mb-1"><strong><?= htmlspecialchars($sidangMendatang['judul']) ?></strong></p>
                        <p class="mb-1">Tanggal: <?= htmlspecialchars($sidangMendatang['tanggal_sidang']) ?></p>
                        <p class="mb-1">Ruangan: <?= htmlspecialchars($sidangMendatang['ruangan']) ?></p>
                        <a href="/mahasiswa/detail-sidang/<?= htmlspecialchars($sidangMendatang['id']) ?>" class="btn btn-outline-primary btn-sm mt-2">Detail</a>
                    <?php else: ?>
                        <p class="text-muted">Belum ada jadwal sidang.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Status Pengajuan</h5>
                    <?php if (!empty($statusPengajuan)): ?>
                        <p class="mb-1">Status: <span class="badge bg-info text-dark"><?= htmlspecialchars($statusPengajuan['status']) ?></span></p>
                        <a href="/mahasiswa/kelola-pengajuan" class="btn btn-outline-primary btn-sm mt-2">Kelola Pengajuan</a>
                    <?php else: ?>
                        <p class="text-muted">Belum ada pengajuan.</p>
                        <a href="/mahasiswa/pengajuan" class="btn btn-primary btn-sm mt-2">Ajukan Sidang</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Tanggungan</h5>
                    <?php if (!empty($tanggungan)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tanggungan as $item): ?>
                                <li class="list-group-item small">- <?= htmlspecialchars($item['deskripsi']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada tanggungan.</p>
                    <?php endif; ?>
                </div>
            </div>
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