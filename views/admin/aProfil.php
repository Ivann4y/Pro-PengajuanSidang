<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta author="JaisyuNurM-AliansiSidang_Kelompok5"/>
    <title>Admin - Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #ffffff;
        }

        .data-admin {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
        }

        .data-admin h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: 600;
        }

        .aData {
            margin-bottom: 1rem;
        }

        .aData p:first-child {
            font-weight: 600;
            color: #666;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .aData .value {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            font-size: 0.95rem;
            color: #494949;
            font-weight: 600;
        }

        .profil-img {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .profil-img img {
            width: 80%;
            height: auto;
            border-radius: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .data-admin {
                padding: 1.5rem;
                margin-top: 1rem;
            }
            
            .profil-img {
                margin-bottom: 2rem;
            }
            
            .profil-img img {
                width: 60%;
            }
        }
    </style>
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
                    <a href="aBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aPenjadwalan.php"><span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
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
               <a href="aNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
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
                    <div class="col-md-6 data-admin">
                        <h2>Data admin</h2>
                        <div class="row aData">
                            <div class="col-12">
                                <p>Nama</p>
                                <p class="value">John Doe</p>
                            </div>
                        </div>
                        <div class="row aData">
                            <div class="col-12">
                                <p>Email</p>
                                <p class="value">123456789@polytechnic.astra.ac.id</p>
                            </div>
                        </div>
                        <div class="row aData">
                            <div class="col-12">
                                <p>No. Telepon</p>
                                <p class="value">08123456789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

           <!-- Modal keluar-->
  <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                 <div class="modal-heading-color">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center">
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
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function(event) {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }

        
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>