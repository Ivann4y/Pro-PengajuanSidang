<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../extra/style.css" />
    <title>Mahasiswa - Nilai Akhir</title>
    <style>
      body,
      .card,
      .form-control,
      h1,
      h2,
      h3,
      h4,
      h5,
      h6 {
        font-family: "Poppins", sans-serif !important;
        color: #464869;
      }
      #cardNilai {
        background-color: #f2f2f2;
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 500px; 
        height: 350px; 
        margin-left: 50px;

      }
      #nilaiMahasiswa {
        font-size: 9.5rem !important;
        font-weight: bold;
        text-align: center;
        border-radius: 30px;
        width: 90%;
        margin-left: 23px;
        height: 40px;
      }
   
      #cardPenilaian {
        background-color: #f2f2f2;
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-left: 50px;
      }
      label
        {
            margin-top: 20px;
            margin-right: 15px; ;
            font-weight: bold;
        }
        #detailpenilaian {
           width: 75px; ;
           font-size: 1rem; 
           margin-right: auto;
           margin-top: 20px;
           text-size-adjust: 100000px;
        }
        #carddetailPenilaian {
            width: 1000px;
            margin-left: 60px;
            background-color: #f2f2f2;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        #labelpenilaian {
                margin-left: 30px;
                
            }
      #cardcatatan {
        background-color: #f2f2f2;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 1000px;
        margin-left: 50px;
      }
      #catatan {
        width: 100%;
        height: 150px;
        border-radius: 30px;
        font-size: 1rem;
        margin-top: 20px;
        
      }
          .btn-kembali {
      background-color: #4B68FB;
      color: white;
      border-radius: 50px;
    }

    .btn-kembali:hover {
      border-color: #4B68FB;
      background-color: #ffffff;
      color: #4B68FB;
    }

    .icon-circle {
  display: inline-flex;
  align-items: center;
  justify-content:center;
  background-color: white;
  color: #4B68FB;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  font-size: 16px;
}

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
            border-left: 5px solid #4B68FB;
            background: #4B68FB;
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
            margin-bottom: 10px; /* Kept from user's last version */
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
            color: #4B68FB;
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
            background: #4B68FB;
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
            background: #4B68FB;
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
            padding-top: 3vh; /* Default for larger screens */
              overflow-x: hidden;
  max-width: 100%;
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
            display: none; /* Hidden by default for larger screens */
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            display: none;
        }

        .NavSide__toggle i.bi.open {
            color: #4B68FB;
        }
        .NavSide__toggle i.bi.close {
            color: #4B68FB;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.open { display: none; }
        .NavSide__toggle.NavSide__toggle--active i.bi.close { display: block; }

        /* NEW: Top Bar for smaller screens */
        .NavSide__topbar {
            display: none; /* Hidden by default for larger screens */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px; /* Adjust height as needed */
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 999; /* Below sidebar, above main content */
            align-items: center; /* Vertically align items */
            padding: 0 15px; /* Add horizontal padding */
            justify-content: space-between; /* Space between toggle and icons */
        }

        .NavSide__topbar .header-icons {
            display: flex;
            align-items: center;
            /* margin-left: auto; This is handled by justify-content: space-between */
        }

        /* --- STYLES FOR ICONS IN TOPBAR TO MATCH DESKTOP --- */
        .NavSide__topbar .header-icons .bi-bell-fill {
            font-size: 1.5rem; /* Matches desktop size */
            color: #555; /* Matches desktop color */
            margin-right: 1.5rem; /* Space between bell and profile */
            cursor: pointer;
        }
        .NavSide__topbar .profile-icon {
            width: 40px; /* Matches desktop size */
            height: 40px; /* Matches desktop size */
            background-color: #333; /* Matches desktop color */
            border-radius: 50%; /* Matches desktop shape */
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        /* --- END ICON STYLES --- */


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
                padding-top: 75px; /* Adjust this to create space for the fixed top bar */
            }
            .NavSide__toggle {
                display: flex; /* Show toggle button on small screens */
                /* When inside NavSide__topbar, remove fixed positioning */
                position: relative;
                top: auto; /* Reset */
                background-color: transparent; /* Maintain transparency */
                box-shadow: none; /* Remove shadow if topbar has one */
                left: 0; /* Adjusted for better alignment */
            }
            .NavSide__toggle i.bi.open {
                display: block;
            }
            .NavSide__toggle.NavSide__toggle--active {
                /* This rule targets the toggle's position when the sidebar is open on mobile */
                /* We need to re-evaluate if this specific 'left' adjustment is still needed/correct */
                /* given the toggle is now part of the topbar's flex layout. */
                /* For now, leaving it as is, but it might not have the intended effect or might conflict */
                /* if the toggle is a flex item. */
                left: calc(50% + 10px);
                background-color: aliceblue; /* This might still be needed if you want a background change when active */
            }

            /* Show the top bar on small screens */
            .NavSide__topbar {
                display: flex;
            }

            /* Hide dashboard-header icons on small screens if they are now in the topbar */
            .dashboard-header .header-icons {
                display: none;
            }

            /* Adjust main content heading on small screens */
            .dashboard-header {
                margin-top: 0; /* Remove top margin if the welcome text is directly after the top bar */
            }
        }
    </style>
  </head>
  <body>

      <body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item ">
                    <b></b><b></b>
                    <a href="MdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPerbaikan.php"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
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
        <main class="NavSide__main-content">
       
      

      <div class="container-fluid ">
          <div class="row mb-5">
                 <h2 style="margin-left: 50px ;">
                <b>Detail Evaluasi - Sistem Pengajuan Sidang</b>
            </h2>
            </div> 
        <div class="row mt-5 align-items-center justify-content-between">
          <div class="col-md-6">
            <div class="card" id="cardNilai">
              <div class="card-body">
                <h3 class="card-title" style="padding:10px ;">Nilai Mahasiswa:</h3>
                <div id ="nilaiMahasiswa">
                    --
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <img
              src="../../assets/img/img5.png"
              alt="Mahasiswa"
              class="img-fluid "
              style="max-width: 100%; height: auto;"
            />
          </div>
        </div>
        <div class="row mt-5 align-items-center justify-content-between">
            <div class="card" id="carddetailPenilaian">
        <div class="card-body">
            <h3 class="card-title">Detail Penilaian :</h3>
        <div class="col-auto d-flex align-items-center gap-4 flex-wrap">
        <span><strong>Nilai Laporan:</strong> --</span>
        <span><strong>Materi Presentasi:</strong> --</span>
        <span><strong>Penyampaian:</strong> --</span>
        <span><strong>Nilai Proyek:</strong> --</span>
        </div>
        </div>
        </div>
      </div>
      <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-md-12">
          <div class="card" id="cardcatatan">
            <div class="card-body">
              <h3 class="card-title">Catatan:</h3>
                --
            </div>
          </div>
        </div>
        
        <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-auto">
           <button class="btn btn-kembali" style="margin-left: 50px;"  onclick="pindahKeHalamanDaftarSidang()">
    <span class="icon-circle">
      <i class="bi bi-arrow-left"></i>
    </span>
    Kembali
  </button>
        </div>
      </main>
    </div>

     
                <!--apabila terdapat catatan maka akan ada text catatan -->
                    <!-- <div class="form-control form-control-lg" style="min-height: 100px;">
            </div> -->

        <!-- tanpa toggle fokus desktop -->
   <!-- <div id="main-sidebar" class="NavSide__sidebar">
  <div class="NavSide__sidebar-brand">
    <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
  </div>
  <ul class="NavSide__sidebar-nav">
    <li class="NavSide__sidebar-item">
      <b></b><b></b>
      <a href="detail-pengajuan.php">
        <span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span>
      </a>
    </li>
    <li class="NavSide__sidebar-item">
      <b></b><b></b>
      <a href="mPerbaikan.php">
        <span class="NavSide__sidebar-title fw-semibold">Perbaikan</span>
      </a>
    </li>
    <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
      <b></b><b></b>
      <a href="nilai-akhir.php">
        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
      </a>
    </li>
  </ul>
 </div> -->
 
    <!-- Bagian Script atau menjalankan fungsi -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>

    function pindahKeHalamanDaftarSidang() {
    window.location.href = "mSidang.php";
    }
        // Sidebar Toggle Logic
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function () {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // Sidebar Active Item Logic (no change needed here as it's already functional)
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function () {
                if(!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }
    </script>
  </body>
</html>
