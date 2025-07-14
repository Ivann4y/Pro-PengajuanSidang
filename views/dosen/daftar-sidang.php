<?php
$title = $title ?? 'Daftar Sidang - Dosen';
$custom_css = ['/assets/css/dDaftarSidang.css'];
ob_start();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Daftar Sidang</h2>
            <p class="text-muted">Daftar sidang yang Anda bimbing atau uji.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?php if (!empty($sidang)): ?>
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Mahasiswa</th>
                            <th>Tanggal</th>
                            <th>Ruangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sidang as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['judul']) ?></td>
                                <td><?= htmlspecialchars($s['mahasiswa_nama']) ?></td>
                                <td><?= htmlspecialchars($s['tanggal_sidang']) ?></td>
                                <td><?= htmlspecialchars($s['ruangan']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($s['status']) ?></span></td>
                                <td>
                                    <a href="/dosen/evaluasi-sidang/<?= htmlspecialchars($s['id']) ?>" class="btn btn-sm btn-primary">Evaluasi</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">Tidak ada sidang.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; 