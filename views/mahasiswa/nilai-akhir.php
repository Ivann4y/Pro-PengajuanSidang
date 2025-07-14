<?php
// Set page title
$title = $title ?? 'Nilai Akhir - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mNilaiakhir.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Nilai Akhir Sidang</h2>
            <p class="text-muted">Rekapitulasi nilai akhir sidang tugas akhir Anda.</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (!empty($nilai)): ?>
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Aspek Penilaian</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Presentasi</td><td><?= htmlspecialchars($nilai['nilai_presentasi']) ?></td></tr>
                                <tr><td>Materi</td><td><?= htmlspecialchars($nilai['nilai_materi']) ?></td></tr>
                                <tr><td>Metodologi</td><td><?= htmlspecialchars($nilai['nilai_metodologi']) ?></td></tr>
                                <tr><td>Hasil</td><td><?= htmlspecialchars($nilai['nilai_hasil']) ?></td></tr>
                                <tr><td>Keseluruhan</td><td><?= htmlspecialchars($nilai['nilai_keseluruhan']) ?></td></tr>
                                <tr><td>Status</td><td><span class="badge bg-info text-dark"><?= htmlspecialchars($nilai['status']) ?></span></td></tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <strong>Catatan:</strong>
                            <p><?= nl2br(htmlspecialchars($nilai['komentar'])) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">Belum ada nilai akhir sidang.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 