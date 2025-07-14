<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Sistem Pengajuan Sidang') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (!empty($custom_css)): ?>
        <?php foreach ($custom_css as $css): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header>
        <!-- Shared header/navbar goes here -->
    </header>
    <main class="container-fluid p-0">
        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success text-center mt-2"> <?= htmlspecialchars($flash['success']) ?> </div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger text-center mt-2"> <?= htmlspecialchars($flash['error']) ?> </div>
        <?php endif; ?>
        <?= $content ?>
    </main>
    <footer class="text-center py-3 mt-5 bg-light border-top">
        <small>&copy; <?= date('Y') ?> Politeknik Astra - Sistem Pengajuan Sidang</small>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty($custom_js)): ?>
        <?php foreach ($custom_js as $js): ?>
            <script src="<?= htmlspecialchars($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 