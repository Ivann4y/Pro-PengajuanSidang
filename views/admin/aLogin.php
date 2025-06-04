<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 100vw;
        }

        .fullscreen {
            height: 100vh;
            width: 100vw;
            display: flex;
        }

        .bgBiru {
            background-color: rgb(67, 54, 240);
            width: 60%;
            height: 100vh;
        }

        .log {
            width: 40%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        img {
            object-fit: cover;
        }

        input {
            border-radius: 50%;
            border: 1px solid #D9D9D9;
            background-color: #F5F5F5;
            color: #626262;
        }

        .carousel-item {
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }


        #carouselExampleAutoplaying .carousel-control-prev,
        #carouselExampleAutoplaying .carousel-control-next {
            opacity: 0;
            pointer-events: none;
            transition: 300ms;
        }

        #carouselExampleAutoplaying:hover .carousel-control-prev,
        #carouselExampleAutoplaying:hover .carousel-control-next {
            opacity: 1;
            pointer-events: auto;
        }

        form {
            width: 25vw;
        }
    </style>
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

        <div class="log d-flex justify-content-center align-items-center pb-5">
            <form action="../../auth.php" method="POST">
                <div class="text-center pt-5 mb-4">
                    <h2><strong>Masuk Akun Admin</strong></h2>
                </div>
                <div class="mb-3">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            if ($_GET['error'] === '1') {
                                echo "NIP atau Password salah!";
                            } elseif ($_GET['error'] === 'role') {
                                echo "Role tidak ditemukan!";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="role" value="admin">
                    <input type="text" class="form-control form-control-lg border border-dark" id="username" name="username" placeholder="NIP" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control form-control-lg border border-dark" id="password" name="password" placeholder="Password" required>
                    <a href="../lupaPassword.php" class="float-end"> Lupa kata sandi?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>