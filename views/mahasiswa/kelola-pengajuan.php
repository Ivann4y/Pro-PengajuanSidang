<?php
// Set page title
$title = $title ?? 'Kelola Pengajuan Sidang - Sistem Pengajuan Sidang';
$custom_css = ['/assets/css/mKelolaPengajuan.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Kelola Pengajuan Sidang</h2>
            <p class="text-muted">Daftar pengajuan sidang Anda.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?php if (!empty($pengajuan)): ?>
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Laporan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengajuan as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($p['status']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?></td>
                                <td>
                                    <?php if (!empty($p['laporan_path'])): ?>
                                        <a href="/uploads/laporan/<?= htmlspecialchars($p['laporan_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'pending'): ?>
                                        <a href="/mahasiswa/edit-pengajuan/<?= htmlspecialchars($p['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">Belum ada pengajuan sidang.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 