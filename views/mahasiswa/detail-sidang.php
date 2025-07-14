<?php
// Set page title
$title = $title ?? 'Detail Sidang - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mdetailsidang.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Detail Sidang</h2>
            <p class="text-muted">Informasi lengkap sidang tugas akhir Anda.</p>
        </div>
    </div>
    <?php if (!empty($sidang)): ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Informasi Sidang</div>
                <div class="card-body">
                    <p><strong>Judul:</strong> <?= htmlspecialchars($sidang['judul']) ?></p>
                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($sidang['tanggal_sidang']) ?></p>
                    <p><strong>Ruangan:</strong> <?= htmlspecialchars($sidang['ruangan']) ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-info text-dark"><?= htmlspecialchars($sidang['status']) ?></span></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Dosen Penguji</div>
                <div class="card-body">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($sidang['dosen_nama'] ?? '-') ?></p>
                    <p><strong>NIP:</strong> <?= htmlspecialchars($sidang['dosen_id'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">Penilaian</div>
                <div class="card-body">
                    <?php if (!empty($penilaian)): ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Presentasi: <strong><?= htmlspecialchars($penilaian['nilai_presentasi']) ?></strong></li>
                            <li class="list-group-item">Materi: <strong><?= htmlspecialchars($penilaian['nilai_materi']) ?></strong></li>
                            <li class="list-group-item">Metodologi: <strong><?= htmlspecialchars($penilaian['nilai_metodologi']) ?></strong></li>
                            <li class="list-group-item">Hasil: <strong><?= htmlspecialchars($penilaian['nilai_hasil']) ?></strong></li>
                            <li class="list-group-item">Keseluruhan: <strong><?= htmlspecialchars($penilaian['nilai_keseluruhan']) ?></strong></li>
                            <li class="list-group-item">Komentar: <span><?= htmlspecialchars($penilaian['komentar']) ?></span></li>
                            <li class="list-group-item">Status: <span class="badge bg-info text-dark"><?= htmlspecialchars($penilaian['status']) ?></span></li>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Belum ada penilaian.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">Detail Sidang</div>
                <div class="card-body">
                    <?php if (!empty($detailSidang)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($detailSidang as $detail): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars($detail['keterangan']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Belum ada detail tambahan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-danger text-center">Sidang tidak ditemukan.</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 