<?php   
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
     <link rel="stylesheet" href="../../assets/css/style.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
   

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background-color: rgb(255, 255, 255);
        }
        
        .bodyContainer {
            margin-left: 15vw;
            padding: 4vh 3vw;
        }
        
        .bodyHeading {
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .welcomeText {
            color: #555;
            font-size: 2rem;
        }
        
        .dashboardTitle {
            color: #4538db;
            font-size: 1.5rem;
            font-weight: 500;
        }
        
        .statusCard {
            padding: 20px;
            border-radius: 15px;
            height: 100%;
            transition: 0.3s ease;
            cursor: pointer;
        }
        
        .statusCardItem {
            position: relative;
            word-wrap: break-word;
            /* Pecah kata jika terlalu panjang */
            word-break: break-word;
            /* Tambahan untuk jaga-jaga */
            white-space: normal;
            /* Pastikan teks bisa turun ke bawah */
            overflow-wrap: break-word;
            /* Untuk browser modern */
            max-width: 100%;
            /* Biar tidak melebihi lebar parent */
            font-size: 1rem;
        }
        
        .statusCard:hover {
            transform: scale(1.05);
        }
        
        .card-penjadwalan {
            background-color: #4538db;
            color: white;
        }
        
        .card-pengajuan {
            background-color: #e2e2e2;
            color: #444;
        }
        
        .statusTitle {
            font-weight: 600;
            margin-bottom: 10px;
            word-break: break-word;
        }
        
        .statusNumber {
            font-size: 2.5rem;
            font-weight: bold;
        }
        
        .notifBox {
            background-color: #dcdcdc;
            padding: 20px;
            border-radius: 15px;
            position: relative;
        }
        
        .notifBox h5 {
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .notifItem {
            background-color: white;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            position: relative;
        }
        
        .notifItem .notif-check {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .notifItem .notif-check:hover {
            color: #0056b3;
        }
        
        .notif-all-check {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #007bff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .notif-all-check:hover {
            color: #0056b3;
        }
        
        /* mobile screen image */
        @media (max-width: 768px) {
            .img-slot {
                display: none;
            }
            .img-slot img {
                display: none;
            }
        }
        
        .img-slot {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 250px;
            position: relative;
        }
        
        .img-slot img {
            width: 80%;
            height: 90%;
            transform: translateY(-20%);
        }
        
        .profile-icon {
            position: absolute;
            top: 30px;
            right: 30px;
            font-size: 1.8rem;
            color: #444;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .notif-icon {
            position: absolute;
            top: 30px;
            right: 80px;
            font-size: 1.8rem;
            color: #444;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .profile-icon:hover {
            color: #007bff;
        }
        
        .notif-icon:hover {
            color: #007bff;
        }
    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b>
                    <b></b>
                    <a href="aBeranda.php">
                        <span class="NavSide__sidebar-title fw-semibold">Beranda</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b>
                    <b></b>
                    <a href="aPenjadwalan.php">
                        <span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b>
                    <b></b>
                    <a href="aDaftarSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span>
            </a>
          </li>
    
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

         <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon">
                    <a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <main class="NavSide__main-content">
            <div class="profile-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <!-- <div class="notif-icon">
                <i class="fa-solid fa-bell"></i>
            </div> -->
            <div class="dashboardTitle">Dashboard Admin</div>
            <h2 class="welcomeText">Selamat Datang, Admin!</h2>

            <div class="row my-4">
                <div class="col-md-3 mb-3">
                    <div class="statusCard card-penjadwalan" id="cardPenjadwalan" onclick="location.href='aPenjadwalan.php'">
                        <div class="statusTitle">Penjadwalan</div>
                        <div class="d-flex align-items-center">
                            <div class="statusNumber me-3">4</div>
                            <div class="statusCardItem">Menunggu Dijadwalkan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="statusCard card-pengajuan" id="cardPengajuan" onclick="location.href='aPenjadwalan.php'">
                        <div class="statusTitle">Pengajuan</div>
                        <div class="d-flex align-items-center">
                            <div class="statusNumber me-3">2</div>
                            <div class="statusCardItem">Menunggu Persetujuan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- modal keluar -->
            <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header mx-auto">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Perhatian!</h1>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
                </div>
            </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="notifBox">
                        <h5>Notifikasi</h5>
                        <div class="notif-all-check" onclick="markAllRead()">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <div class="notifItem">
                            Sidang PRG telah disetujui oleh dosen pembimbing
                            <span class="notif-check" onclick="markOneRead(this)"><i class="fa-solid fa-check"></i
                ></span>
                        </div>
                        <div class="notifItem">
                            Sidang BasDat telah selesai dinilai
                            <span class="notif-check" onclick="markOneRead(this)"><i class="fa-solid fa-check"></i
                ></span>
                        </div>
                        <div class="notifItem">
                            Sidang TA Nayaka Ivana Putra telah selesai dinilai
                            <span class="notif-check" onclick="markOneRead(this)"><i class="fa-solid fa-check"></i
                ></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3" style="top: -50px; z-index: 10">
                    <div class="img-slot">
                        <img src="../../assets/img/img4.png" />
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markAllRead() {
            document
                .querySelectorAll(".notifItem")
                .forEach((item) => item.remove());
        }

        function markOneRead(el) {
            el.closest(".notifItem").remove();
        }

        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar"); // More direct access

        // Toggle sidebar visibility
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile"); // Specific class for mobile active state
        };

        // Active menu item highlighting
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                // Remove active class from all items
                for (let j = 0; j < listItems.length; j++) {
                    listItems[j].classList.remove("NavSide__sidebar-item--active");
                }
                // Add active class to the clicked item
                this.classList.add("NavSide__sidebar-item--active");
            };
        }
    </script>
</body>

</html>