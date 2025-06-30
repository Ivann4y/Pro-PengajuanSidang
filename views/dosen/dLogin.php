<?php
require "../../koneksi/koneksiAndrew.php";

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header('Location: ../../index.php');
    exit();
}

$role = "dosen";
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
            $error = $_GET['error'] ?? '';
            ?>
            <div class="col-md-7 d-flex justify-content-center align-items-center mt-5">

                <form action="../../auth.php" method="POST" novalidate onsubmit="salinSandiAsli()">
                    <div class="text-center pt-5 mb-4">
                        <h2><strong>Masuk Akun</strong></h2>
                        <h2><strong>Dosen</strong></h2>
                    </div>
                    <input type="hidden" name="role" value="<?= $role ?>">

                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control form-control-lg <?= ($error === 'empty' || $error === '1') ? 'border border-danger' : 'border border-dark' ?>"
                            id="username"
                            name="username"
                            placeholder="NIP"
                            value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
                        <?php if ($error === 'empty'): ?>
                            <small class="text-danger">NIP dan Kata Sandi harus diisi!</small>
                        <?php elseif ($error === '1'): ?>
                            <small class="text-danger">NIP atau Kata Sandi salah!</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <div class="password-wrap">
                            <input type="text" name="password"
                                class="form-control form-control-lg <?= ($error === 'empty' || $error === '1') ? 'border border-danger' : 'border border-dark' ?>"
                                id="passwordTampil" placeholder="Kata Sandi">
                            <!-- <input type="hidden" name="password" id="passwordAsli"> -->
                            <i class="bi bi-eye-fill" id="togglePassword"></i>
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
            window.location.href = '../../views/lupaPassword.php?role=<?= $role ?>';
        }

        function kembaliKePilihRole() {
            window.location.href = '../../index.php';
        }

        // hide pw
        // let sandiAsli = '';

        // document.getElementById('passwordTampil').addEventListener('input', function(e) {
        //     const tampilan = e.target.value;
        //     if (tampilan.length < sandiAsli.length) {
        //         sandiAsli = sandiAsli.slice(0, tampilan.length);
        //     } else {
        //         const hurufBaru = tampilan.charAt(tampilan.length - 1);
        //         sandiAsli += hurufBaru;
        //     }
        //     e.target.value = '•'.repeat(sandiAsli.length);
        // });

        // function salinSandiAsli() {
        //     document.getElementById('passwordAsli').value = sandiAsli;
        // }

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordTampil');

        togglePassword.addEventListener('click', function(e) {
            // Toggle tipe input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle ikon mata
            this.classList.toggle('bi-eye-slash-fill');
        });
    </script>
</body>

</html>