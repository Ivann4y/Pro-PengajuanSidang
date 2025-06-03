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

        /* NavSide styles from mBeranda.php - START */
        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
        }

        .NavSide__sidebar {
            position: fixed;
            top: 0px;
            left: 0px;
            bottom: 0px;
            width: 280px;
            border-radius: 1px;
            box-sizing: border-box;
            border-left: 5px solid rgb(67, 54, 240);
            background: rgb(67, 54, 240);
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.5s ease-in-out, width 0.5s ease-in-out;
        }

        .NavSide__sidebar-nav {
            width: 100%;
            padding-left: 0;
            padding-top: 0;
            list-style: none;
            flex-grow: 1;
        }

        .NavSide__sidebar-item {
            position: relative;
            display: block;
            width: 100%;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            margin-bottom: 10px;
        }

        .NavSide__sidebar-item a {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-decoration: none;
            color: rgb(252, 252, 252);
            padding: 5% 2%;
            height: 60px;
            box-sizing: border-box;
        }

        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: rgb(67, 54, 240);
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
            right: 0;
        }

        .NavSide__sidebar-item b:nth-child(1)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-bottom-right-radius: 20px;
            background: rgb(67, 54, 240);
            display: block;
        }

        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            bottom: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
            right: 0;
        }

        .NavSide__sidebar-item b:nth-child(2)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-top-right-radius: 20px;
            background: rgb(67, 54, 240);
            display: block;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            background-color: #F9FAFB;
            padding-top: 3vh;
        }

        .NavSide__toggle {
            position: fixed;
            top: 15px;
            left: 15px; 
            width: 40px;
            height: 40px;
            z-index: 1100;
            transition: left 0.5s ease-in-out;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none; 
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            display: none;
        }

        .NavSide__toggle i.bi.open {
            color: rgb(67, 54, 240);
        }

        .NavSide__toggle i.bi.close {
            color: rgb(67, 54, 240);
        }

        .NavSide__toggle.NavSide__toggle--active i.bi.open {
            display: none;
        }

        .NavSide__toggle.NavSide__toggle--active i.bi.close {
            display: block;
        }
        
        .NavSide__topbar {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px; 
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 999; 
            align-items: center; 
            padding: 0 15px; 
            justify-content: space-between; 
        }

        .NavSide__topbar .header-icons {
            display: flex;
            align-items: center;
        }

        .NavSide__topbar .header-icons .bi-bell-fill {
            font-size: 1.5rem;
            color: #555;
            margin-right: 1.5rem;
            cursor: pointer;
        }

        .NavSide__topbar .profile-icon {
            width: 40px;
            height: 40px;
            background-color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        .text-heading { 
            font-size: 1.75rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem; 
        }

        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            .NavSide__sidebar-brand {
                padding: 20px 10px 30px 10px;
            }

            .NavSide__sidebar-brand img {
                width: 90%;
            }

            .NavSide__sidebar-nav {
                padding-top: 20%;
            }

            .NavSide__sidebar-item a {
                padding: 12% 10%;
                height: auto;
            }

            .NavSide__main-content {
                margin-left: 0;
                padding: 15px;
                padding-top: 75px; 
            }

            .NavSide__toggle { 
                display: flex;
                position: relative; 
                top: auto;
                left: auto; 
                background-color: transparent;
                box-shadow: none;
            }
            
            .NavSide__toggle i.bi.open {
                display: block; 
            }
            

            .NavSide__topbar {
                display: flex; 
            }
        }
        /* NavSide styles from mBeranda.php - END */

        .data-mahasiswa {
            background-color: #ffffff;
            padding: 3vw;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: start;
            color: #333;
        }
        .data-mahasiswa p {
            font-weight: 600;
            font-size: larger;
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
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="logout.html"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
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
                    <div class="col-md-6">
                        <img src="../../assets/img/img3-nobg.png" alt="">
                    </div>
                    <div class="col-md-6 data-mahasiswa">
                        <h2 class="text-heading">Data Mahasiswa</h2>
                        <p class="text-muted">NIM: 123456789</p>
                        <p class="text-muted">Nama: John Doe</p>
                        <p class="text-muted">Email: 123456789@polytechnic.astra.ac.id</p>
                        <p class="text-muted">No. Telepon: 08123456789</p>
                        <p class="text-muted">Jenis Kelamin: Laki-laki</p>
                    </div>
                </div>
            </div>
        </main>
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