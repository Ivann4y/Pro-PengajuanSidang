<?php
session_start();
require_once '../security/security_helper.php';

// Set security headers
setSecurityHeaders();

$role = sanitizeInput($_GET['role'] ?? 'guest');

// Validasi role
if (!validateRole($role)) {
    logSuspiciousActivity('INVALID_ROLE_GET', "Invalid role in GET: $role");
    header("Location: ../index.php");
    exit();
}

// Generate CSRF token
$csrf_token = generateCSRFToken();

switch ($role) {
    case 'mahasiswa':
        $tableNama = 'Mahasiswa';
        $emailKolom = 'email';
        $judul = 'Lupa Kata Sandi Mahasiswa';
        break;
    case 'dosen':
        $tableNama = 'Dosen';
        $emailKolom = 'email';
        $judul = 'Lupa Kata Sandi Dosen';
        break;
    case 'admin':
        $tableNama = 'Admin';
        $emailKolom = 'email';
        $judul = 'Lupa Kata Sandi Admin';
        break;
    default:
        $judul = 'Lupa Kata Sandi';
        break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul) ?> - Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/button-styles.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <div class="fullscreen d-flex">
        <div class="bgBiru d-flex flex-column justify-content-center align-items-center">
            <img src="../assets/img/awan.png" class="background-awan position-absolute"
                style="object-fit: cover; z-index: 0; width: 60vw; height: 100vh;" alt="Background">
            <div class="position-absolute"
                style="top: 0; left: 0; width: 60vw; height: 100vh; background-color: rgba(0, 0, 100, 0.2); z-index: 1;">
            </div>
            <div class="row pt-5 text-white fs-2 fw-semibold text-center pt-5" style="z-index: 2;">
                <label for="">Sistem Pengajuan Sidang</label>
                <label for="">Politeknik Astra</label>
            </div>
            <div id="carouselExampleAutoplaying" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="2000" style="padding: 5% 10% 5% 10%;">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../assets/img/img6.png" class="imgPertama rounded-circle d-block mx-auto" alt="..." style="height: 50vh; width: 50vh;">
                    </div>
                    <div class="carousel-item">
                        <img src="../assets/img/img2.png" class="imgKedua rounded-circle d-block mx-auto" alt="..." style="height: 50vh; width: 50vh;">
                    </div>
                    <div class="carousel-item">
                        <img src="../assets/img/img5.png" class="imgKetiga rounded-circle d-block mx-auto" alt="..." style="height: 50vh; width: 50vh;">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="log row pt-5">
            <?php
            $error = sanitizeInput($_GET['error'] ?? '');
            $success = sanitizeInput($_GET['success'] ?? '');
            ?>
            <div class="col-md-7 d-flex justify-content-center align-items-center mt-5">
                <form action="authEmail.php" method="POST">
                    <div class="text-center pt-5 mb-4">
                        <h2 class="fs-2 fw-bold text-center"><?= htmlspecialchars($judul) ?></h2>
                    </div>
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <label for="emailAstra" class="mt-3">Masukkan Email Politeknik Astra</label>
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
                    <input type="hidden" name="tableNama" value="<?= htmlspecialchars($tableNama) ?>">
                    <input type="hidden" name="emailKolom" value="<?= htmlspecialchars($emailKolom) ?>">
                    
                    <?php if ($error === 'empty'): ?>
                        <input type="email" class="form-control form-control-lg border border-danger" id="emailAstra" name="emailAstra" maxlength="100">
                        <div class="text-danger">Email harus diisi!</div>
                    <?php elseif ($error === 'invalid'): ?>
                        <input type="email" class="form-control form-control-lg border border-danger" id="emailAstra" name="emailAstra" maxlength="100">
                        <div class="text-danger">Format email tidak valid!</div>
                    <?php elseif ($error === 'notfound'): ?>
                        <input type="email" class="form-control form-control-lg border border-danger" id="emailAstra" name="emailAstra" maxlength="100">
                        <div class="text-danger">Email tidak ditemukan!</div>
                    <?php elseif ($error === 'mail'): ?>
                        <input type="email" class="form-control form-control-lg border border-danger" id="emailAstra" name="emailAstra" maxlength="100">
                        <div class="text-danger">Gagal mengirim email reset password. Silakan coba lagi!</div>
                    <?php elseif ($error === 'rate_limit'): ?>
                        <input type="email" class="form-control form-control-lg border border-danger" id="emailAstra" name="emailAstra" maxlength="100" disabled>
                        <div class="text-danger">Terlalu banyak percobaan. Silakan coba lagi dalam 10 menit.</div>
                    <?php elseif ($error === 'system'): ?>
                        <input type="email" class="form-control form-control-lg border border-danger" id="emailAstra" name="emailAstra" maxlength="100">
                        <div class="text-danger">Terjadi kesalahan sistem. Silakan coba lagi!</div>
                    <?php elseif ($success === '1'): ?>
                        <input type="email" class="form-control form-control-lg border border-success" id="emailAstra" name="emailAstra" value="<?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>" readonly maxlength="100">
                        <div class="text-success">Email reset password telah dikirim. Silakan cek email Anda!</div>
                    <?php else: ?>
                        <input type="email" class="form-control form-control-lg" id="emailAstra" name="emailAstra" maxlength="100">
                    <?php endif; ?>
                    
                    <button class="btn btn-setujui float-end mt-2" id="btnKirim" <?= ($success === '1' || $error === 'rate_limit') ? 'disabled' : '' ?>>
                        Kirim
                    </button>
                </form>
            </div>
            <div class="back-button-container bottom-50">
                <button type="submit" class="btn btn-kembali" onclick="kembaliKeLogin()">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script>
        function kembaliKeLogin() {
            let role = "<?= htmlspecialchars($role) ?>";
            let url = "";

            switch (role) {
                case "mahasiswa":
                    url = "mahasiswa/mLogin.php";
                    break;
                case "dosen":
                    url = "dosen/dLogin.php";
                    break;
                case "admin":
                    url = "admin/aLogin.php";
                    break;
                default:
                    url = "../index.php";
            }

            window.location.href = url;
        }
    </script>
</body>

</html>