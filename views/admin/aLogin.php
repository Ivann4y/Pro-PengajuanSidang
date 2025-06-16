<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$role = "admin";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="../../css/button-styles.css"> -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 100vw;
            overflow: hidden;
        }

        .fullscreen {
            height: 100vh;
            width: 100vw;
            display: flex;
        }

        .bgBiru {
            background-color: #4B68FB;
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

        .btn-kembali {
            background-color: #4B68FB;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 0 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
        }

        .btn-kembali:hover {
            position: relative;
            background-color: white;
            color: #4B68FB;
        }

        .btn-kembali .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-kembali:hover .icon-circle {
            background-color: #4B68FB;
        }

        .btn-kembali .icon-circle i {
            color: #4B68FB;
        }

        .btn-kembali:hover .icon-circle i {
            color: white;
        }

        .btn {
            border: none;
            border-radius: 20px;
            padding: 0 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            text-decoration: none;
            font-family: "Poppins", sans-serif;
            color: white;
            /* Default text color for all buttons */
        }

        .back-button-container {
            padding-left: 2vw;
            margin-top: 20vh;
        }

        .btnMasuk {
            background-color: #4B68FB;
            color: white;
        }

        .btnMasuk:hover {
            background-color: #3a53c3;
            color: white;
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

            <!-- <div class="position-absolute"
                style="top: 0; left: 0; width: 60vw; height: 100vh; background-color: rgba(0, 0, 100, 0.2); z-index: 1;">
            </div>
             -->
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

                <form action="../../auth.php" method="POST" novalidate>
                    <div class="text-center pt-5 mb-4">
                        <h2><strong>Masuk Akun</strong></h2>
                        <h2><strong>Admin</strong></h2>
                    </div>
                    <input type="hidden" name="role" value="<?= $role ?>">

                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control form-control-lg <?= ($error === 'empty' || $error === '1') ? 'border border-danger' : 'border border-dark' ?>"
                            id="username"
                            name="username"
                            placeholder="Nama Pengguna"
                            value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
                        <?php if ($error === 'empty'): ?>
                            <small class="text-danger">Nama Pengguna dan Kata Sandi harus diisi!</small>
                        <?php elseif ($error === '1'): ?>
                            <small class="text-danger">Nama Pengguna atau Kata Sandi salah!</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <input
                            type="password"
                            class="form-control form-control-lg <?= ($error === 'empty' || $error === '1') ? 'border border-danger' : 'border border-dark' ?>"
                            id="password"
                            name="password"
                            placeholder="Kata Sandi">
                        <a href="#" class="float-end" onclick="toLupaPassword()"> Lupa kata sandi?</a>
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
    </script>
</body>

</html>