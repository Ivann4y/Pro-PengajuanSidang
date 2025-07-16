<?php
session_start();
require_once '../koneksi/koneksiAndrew.php';
include '../control/inputPasswordBaru_queries.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul ?> - Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/inputPasswordBaru.css">
</head>

<body>
    <div class="fullscreen d-flex">
        <div class="bgBiru d-flex flex-column justify-content-center align-items-center">
            <img src="../assets/img/awan.png"
                class="position-absolute"
                style="object-fit: cover; z-index: 0; width: 60vw; height: 100vh;"
                alt="Background">
            <div class="position-absolute shadow-rectangle"
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

        <div class="right-column-wrapper">
            <div class="log">
                <?php if ($reset): ?>
                    <form action="inputPasswordBaru.php?token=<?= htmlspecialchars($token) ?>" method="POST">
                        <div class="text-center pt-5 mb-4">
                            <h2 class="fs-2 fw-bold"><?= $judul ?></h2>
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success mt-3">
                                    <?= $success ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                        <div class="mb-3">
                            <label for="newPassword">Masukkan Kata Sandi Baru</label>
                            <div class="password-wrap">
                                <input type="password"
                                    id="newPassword"
                                    class="form-control form-control-lg <?= in_array($errorType, ['empty', 'short']) ? 'border border-danger' : 'border border-dark' ?>"
                                    name="newPassword">
                                <i class="bi bi-eye-slash-fill toggle-password" id="toggleNewPassword"></i>
                            </div>
                            <?php if ($errorType === 'empty'): ?>
                                <div class="text-danger">Kata sandi tidak boleh kosong.</div>
                            <?php elseif ($errorType === 'short'): ?>
                                <div class="text-danger">Kata sandi minimal 8 karakter.</div>
                            <?php elseif ($errorType === 'weak'): ?>
                                <div class="text-danger">Kata sandi harus mengandung huruf besar, huruf kecil, angka, dan simbol.</div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword">Konfirmasi Kata Sandi Baru</label>
                            <div class="password-wrap">
                                <input type="password"
                                    id="confirmPassword"
                                    class="form-control form-control-lg <?= in_array($errorType, ['empty', 'mismatch']) ? 'border border-danger' : 'border border-dark' ?>"
                                    name="confirmPassword">
                                <i class="bi bi-eye-slash-fill toggle-password" id="toggleConfirmPassword"></i>
                            </div>
                            <?php if ($errorType === 'empty'): ?>
                                <div class="text-danger">Konfirmasi kata sandi tidak boleh kosong.</div>
                            <?php elseif ($errorType === 'mismatch'): ?>
                                <div class="text-danger">Kata sandi dan konfirmasi tidak cocok.</div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-setujui" id="btnKirim">
                                Kirim
                            </button>
                        </div>

                       
                    </form>
                <?php endif; ?>

                <?php if (!$reset): ?>
                    <div class="token-alert">
                        <span class="icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        Token tidak valid atau sudah kadaluarsa.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <?php if (!empty($success)): ?>
        <script>
            setTimeout(() => {
                window.location.href = '../index.php';
            }, 2000);
        </script>
    <?php endif; ?>
    <script src="../assets/js/inputPasswordBaru.js"></script>
   
</body>

</html>