<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $_SESSION['role'] = $role;

    switch ($role) {
        case 'dosen':
            header('Location: views/dosen/dLogin.php');
            break;
        case 'admin':
            header('Location: views/admin/aLogin.php');
            break;
        case 'mahasiswa':
            header('Location: views/mahasiswa/mLogin.php');
            break;
        default:
            header('Location: index.php');
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }

        button {
            border: none;
            border-radius: 50px;
            background-color: #D9D9D9;
            color: #626262;
            transition: 300ms;
        }

        button:hover {
            background-color: #4B68FB;
            color: white;
        }

        .fullscreen {
            height: 100vh;
            width: 100vw;
            position: relative;
        }

        .bgBiru {
            background-color: #4B68FB;
            height: 50vh;
        }

        .bgBiru .teks {
            margin-bottom: 10%;
        }

        .bgWhite {
            width: 100vw;
        }

        .letak-LogBox {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
        }

        @media (max-width: 600px) {
            body {
                overflow-y: auto;
            }

            .bgBiru {
                position: sticky;
                top: 0;
                z-index: 1000;
                background-color: rgb(67, 54, 240);
                height: auto;
                padding: 2vh 0;
            }

            .teks {
                text-align: center;
                padding-top: 2vh;
                color: white;
                margin: 0 0 2vh 0;
                padding-left: 20%;
            }

            .img-topRight,
            .img-buttomLeft,
            .img-buttomRight{
                display: none;
            }

            .img-topLeft{
                position: absolute;
                top: 90%;
                right: 12%;
            }

            .letak-LogBox {
                position: relative;
                left: 13%;
                top: 20%;
                transform: none;
                width: 75%;
                z-index: 1001;
            }

            .letak-LogBox .container {
                padding: 1.5em;
            }
        }
    </style>
</head>

<body>
    <!-- Fullscreen -->
    <div class="fullscreen">
        <!-- Header Section -->
        <div class="bgBiru d-flex justify-content-between align-items-center">
            <img src="assets/img/img6-noBg.png" alt="" class="img-topLeft ms-5" width="400vh" height="372vh">
            <div class="teks text-white text-center" style="z-index: 999;">
                <h2><strong>Sistem Pengajuan Sidang</strong></h2>
                <h2><strong>Politeknik Astra</strong></h2>
            </div>
            <img src="assets/img/img4-noBg.png" alt="" class="img-topRight me-5" width="372vh" height="372vh">
        </div>

        <!-- Login Box -->
        <div class="letak-LogBox">
            <div class="container text-dark">
                <div class="row justify-content-center">
                    <div class="col-md-5 text-center bg-white p-4 rounded rounded-5 shadow">
                        <div class="my-2 p-2" style="color: rgb(67, 54, 240);">
                            <h1>Masuk Sebagai</h1>
                        </div>
                        <form method="POST">
                            <div class="role-button d-grid gap-3 mb-4 p-2">
                                <div>
                                    <button name="role" value="dosen" class="w-75 p-2 fw-bold fs-5">Dosen</button>
                                </div>
                                <div>
                                    <button name="role" value="admin" class="w-75 p-2 fw-bold fs-5">Admin</button>
                                </div>
                                <div>
                                    <button name="role" value="mahasiswa" class="w-75 p-2 fw-bold fs-5">Mahasiswa</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="bgWhite d-flex justify-content-between align-items-end">
            <img src="assets/img/img1.png" alt="" class="img-buttomLeft ms-5" width="480vh" height="372vh">
            <img src="assets/img/img3-noBg.png" alt="" class="img-buttomRight me-5" width="420vh" height="372vh">
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>


</html>