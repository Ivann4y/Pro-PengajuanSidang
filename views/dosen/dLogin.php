<?php
require "../../koneksi/koneksiAndrew.php";
require_once "../../security/security_helper.php";

session_start();

// Set security headers
setSecurityHeaders();

// Validasi role dari session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header('Location: ../../index.php');
    exit();
}

$role = "dosen";

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Cleanup expired tokens
cleanupExpiredTokens($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dosen - Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/button-styles.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>

<body>
    <div class="fullscreen d-flex">
        <div class="bgBiru d-flex flex-column justify-content-center align-items-center">
            <img src="../../assets/img/awan.png"
                class="position-absolute"
                style="object-fit: cover; z-index: 0; width: 60vw; height: 100vh;"
                alt="Background">
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
                        <img src="../../assets/img/img6.png" class="imgPertama rounded-circle d-block mx-auto" alt="..." style="height: 50vh; width: 50vh;">
                    </div>
                    <div class="carousel-item">
                        <img src="../../assets/img/img2.png" class="imgKedua rounded-circle d-block mx-auto" alt="..." style="height: 50vh; width: 50vh;">
                    </div>
                    <div class="carousel-item">
                        <img src="../../assets/img/img5.png" class="imgKetiga rounded-circle d-block mx-auto" alt="..." style="height: 50vh; width: 50vh;">
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
            ?>
            <div class="col-md-7 d-flex justify-content-center align-items-center mt-5">

                <!-- Form login dengan CSRF protection -->
                <form action="../../auth.php" method="POST" novalidate>
                    <div class="text-center pt-5 mb-4">
                        <h2><strong>Masuk Akun</strong></h2>
                        <h2><strong>Dosen</strong></h2>
                    </div>
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <!-- Role (tidak bisa diedit user) -->
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control form-control-lg <?= ($error === 'empty' || $error === '1' || $error === 'invalid') ? 'border border-danger' : 'border border-dark' ?>"
                            id="username"
                            name="username"
                            placeholder="NIP"
                            maxlength="50"
                            value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
                        <?php if ($error === 'empty'): ?>
                            <small class="text-danger">NIP dan Kata Sandi harus diisi!</small>
                        <?php elseif ($error === '1'): ?>
                            <small class="text-danger">NIP atau Kata Sandi salah!</small>
                        <?php elseif ($error === 'invalid'): ?>
                            <small class="text-danger">Format NIP tidak valid!</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <div class="password-wrap">
                            <input type="password"
                                class="form-control form-control-lg password-masked <?= ($error === 'empty' || $error === '1') ? 'border border-danger' : 'border border-dark' ?>"
                                id="password" name="password" placeholder="Kata Sandi" maxlength="128">
                            <i class="bi bi-eye-slash-fill" id="togglePassword"></i>
                        </div>
                        <a href="#" class="float-end mt-1" onclick="toLupaPassword()"> Lupa kata sandi?</a>
                    </div>
                    <button type="submit" class="btnMasuk btn w-100 mt-5">Masuk</button>
                </form>

            </div>
            <div class="back-button-container bottom-50">
                <button type="submit" class="btn btn-kembali" onclick="kembaliKePilihRole()">
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
        function toLupaPassword() {
            window.location.href = "../../views/lupaPassword.php?role=<?= htmlspecialchars($role) ?>";
        }
    </script>
    <script src="../../assets/js/login.js"></script>
</body>

</html>