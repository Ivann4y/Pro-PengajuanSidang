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
    <link rel="stylesheet" href="assets/css/landingPage.css">
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