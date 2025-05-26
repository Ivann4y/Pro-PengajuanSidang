<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa - Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/style.css">
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
            position: relative;
        }

        .bgBiru {
            background-color: rgb(67, 54, 240);
            height: 100vh;
            width: 60vw;
        }

        .imgPertama {
            width: 50%;
            height: 50%;
            object-fit: cover;
            border-radius: 50%;
        }

        .imgKedua {
            width: 5%;
            height: 5%;
            object-fit: cover;
            border-radius: 70%;
        }

        .imgKetiga {
            width: 5%;
            height: 5%;
            object-fit: cover;
            border-radius: 100%;
        }
    </style>
</head>

<body>
    <div class="fullscreen">
        <div class="bgBiru">
            <div>

            </div>
            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../../assets/img/img1.png" class="imgPertama d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="../../assets/img/img2.png" class="imgKedua d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="../../assets/img/img3.png" class="imgKetiga d-block w-100" alt="...">
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>