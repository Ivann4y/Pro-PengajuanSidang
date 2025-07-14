<?php
$title = $title ?? 'Daftar Pengajuan Sidang - Dosen';
$custom_css = ['/assets/css/dDaftarSidang.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Daftar Pengajuan Sidang</h2>
            <p class="text-muted">Daftar pengajuan sidang yang perlu Anda evaluasi.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?php if (!empty($pengajuan)): ?>
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Status</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengajuan as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><?= htmlspecialchars($p['mahasiswa_nama']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($p['status']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?></td>
                                <td>
                                    <a href="/dosen/detail-pengajuan/<?= htmlspecialchars($p['id']) ?>" class="btn btn-sm btn-primary">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">Tidak ada pengajuan sidang.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 