<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';


if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

require $path_to_root . "koneksi/koneksiAndrew.php";

$user = $_SESSION['user_data'];
$nim = $user['nomor_dosen'];
$nama = $user['nama_dosen'];

if ($user['prodi'] === 'TRPL') {
    $prodi = 'Teknologi Rekayasa Perangkat Lunak (TRPL)';
} elseif ($user['prodi'] === 'MI') {
    $prodi = 'Manajemen Informatika (MI)';
} elseif ($user['prodi'] === 'TRL') {
    $prodi = 'Teknologi Rekayasa Logistik (TRL)';
} elseif ($user['prodi'] === 'MO') {
    $prodi = 'Mesin Otomotif (MO)';
} else {
    $prodi = 'Program Studi Tidak Diketahui';
}

$email = $user['email'];
$no_telepon = $user['no_telepon'];

if ($user['jenis_kelamin'] === 'L') {
    $jk = 'Laki-laki';
} elseif ($user['jenis_kelamin'] === 'P') {
    $jk = 'Perempuan';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa - Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/profil.css">
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>

            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </div>

        <main class="NavSide__main-content" id="mSidang">
            <div class="container">
                <div class="row">
                    <h1>Profile</h1>
                </div>
                <div class="row">
                    <div class="col-md-6 profil-img">
                        <img src="../../assets/img/img3-nobg.png" alt="">
                    </div>
                    <div class="col-md-6 data-all">
                        <h2>Data Dosen</h2>
                        <div class="row allData">
                            <div class="col-12">
                                <p>NIP</p>
                                <p class="value"><?= $nim ?></p>
                            </div>
                        </div>
                        <div class="row allData">
                            <div class="col-12">
                                <p>Nama</p>
                                <p class="value"><?= $nama ?></p>
                            </div>
                        </div>
                        <div class="row allData">
                            <div class="col-12">
                                <p>Program Studi</p>
                                <p class="value"><?= $prodi ?></p>
                            </div>
                        </div>
                        <div class="row allData">
                            <div class="col-12">
                                <p>Email</p>
                                <p class="value"><?= $email ?></p>
                            </div>
                        </div>
                        <div class="row allData">
                            <div class="col-12">
                                <p>No. Telepon</p>
                                <p class="value"><?= $no_telepon ?></p>
                            </div>
                        </div>
                        <div class="row allData">
                            <div class="col-12">
                                <p>Jenis Kelamin</p>
                                <p class="value"><?= $jk ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Logic 
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        if (menuToggle) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        // Sidebar Active Item Logic 
        // let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        // for (let i = 0; i < listItems.length; i++) {
        //     listItems[i].onclick = function(event) {
        //         if (!this.classList.contains("NavSide__sidebar-item--active")) {
        //             for (let j = 0; j < listItems.length; j++) {
        //                 listItems[j].classList.remove("NavSide__sidebar-item--active");
        //             }
        //             this.classList.add("NavSide__sidebar-item--active");
        //         }
        //     };
        // }
    </script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>