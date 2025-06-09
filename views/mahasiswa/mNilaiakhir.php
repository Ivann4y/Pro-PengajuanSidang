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

       @media (max-width: 750px) {
              .row.mt-5.justify-content-between {
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
    display: flex !important;
    flex-wrap: nowrap !important;
  }

  .row.mt-5.justify-content-between .col-auto {
    flex: auto;
    text-align: center;
    margin-bottom: 0 !important;
    padding: 10px;
  }

  #carddetailPenilaian,
  #cardcatatan {
    width: 100% !important;
    margin-left: 0 !important;
  }
  #cardNilai {
    width: 100% !important;
    margin-left: 0 !important;
  }
  
  #nilaiMahasiswa {
    font-size: 5rem !important;


  }

  #detailpenilaian {
    width: 15% !important;
  }

  img.img-fluid {
    display: none !important;
  }

  .row.mt-5 .col-auto {
    width: 100%;
    text-align: left;
    margin-bottom: 10px;
  }
  .btn-kirim {
    margin-right: 0 !important;
  }
  .modal-dialog {
    margin: 1rem auto;
    max-width: 95% !important;
  }

  .modal-content {
    border-radius: 20px !important;
    padding: 15px;
  }

  .modal-body {
    text-align: center;
    padding: 20px;
  }
  .kakimodal {
    
     flex-direction: row !important;
    justify-content: center !important;
    align-items: center !important;
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 150px !important;
  }
  


  .kakimodal .col-md-6,
  .mb5 .col-md-6 {
    margin-left: 0 !important;
    width: auto !important;
    padding: 0 !important;
  }

  .kakimodal .btnKonfirmasi,
  .mb5 .btnKonfirmasi {
    min-width: 90px;
    flex:1 1 auto;
  }
  .penilaian-row {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }

    .label-penilaian {
      display: flex;
      text-align: center;
    }

    .colon1 {

      flex: 1;
      display: flex;
      margin-left: 81px;
    }
    .colon2{
      flex: 1;
      display: flex;
      margin-left: 42px;
    }
    .colon3{
      flex: 1;
      display: flex;
      margin-left: 69px;
    }
    .colon4{
      flex: 1;
      display: flex;
      margin-left: 88px;
    }

    .input-penilaian {
      width: 100%;
      flex: 3;
    }
}
@media (max-width: 1000px) {
    .NavSide__main-content .row.mt-5.justify-content-between {
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
    display: flex !important;
    flex-wrap: nowrap !important;
  }

  .NavSide__main-content .row.mt-5.justify-content-between .col-auto {
    flex: auto;
    text-align: center;
    margin-bottom: 0 !important;
    padding: 10px;
  }

/* --- ATURAN RESPONSIVE ANDA (TETAP DIPERTAHANKAN) --- */
.NavSide__main-content .btn-kembali {
    width: 100% !important;
    max-width: 150px;
    margin: auto;
}

 .NavSide__main-content  #carddetailPenilaian,
  .NavSide__main-content #cardcatatan {
    width: 100% !important;
    margin-left: 0 !important;
  }
  .NavSide__main-content #cardNilai {
    width: 100% !important;
    margin-left: 0 !important;
  }
  .NavSide__main-content #nilaiMahasiswa {
    font-size: 5rem !important;

    
  }

  .NavSide__main-content #detailpenilaian {
    width: 15% !important;
  }

  .NavSide__main-content img.img-fluid {
    display: none !important;
  }

  .NavSide__main-content .row.mt-5 .col-auto {
    width: 100%;
    text-align: left;
    margin-bottom: 10px;
  }
  .NavSide__main-content .btn-kirim {
    margin-right: 0 !important;
  }
  .modal-dialog {
    margin: 1rem auto;
    max-width: 95% !important;
  }

  .modal-content {
    border-radius: 20px !important;
    padding: 15px;
  }

  .modal-body {
    text-align: center;
    padding: 20px;
  }
  .kakimodal {
    
     flex-direction: row !important;
    justify-content: center !important;
    align-items: center !important;
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 150px !important;
  }

  .kakimodal .col-md-6,
  .mb5 .col-md-6 {
    margin-left: 0 !important;
    width: auto !important;
    padding: 0 !important;
  }

  .kakimodal .btnKonfirmasi,
  .mb5 .btnKonfirmasi {
    min-width: 90px;
    flex:1 1 auto;
  }
  .NavSide__main-content .penilaian-row {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }

    .NavSide__main-content .label-penilaian {
      display: flex;
      text-align: center;
    }

    .NavSide__main-content .colon1 {

      flex: 1;
      display: flex;
      margin-left: 81px;
    }
    .NavSide__main-content .colon2{
      flex: 1;
      display: flex;
      margin-left: 42px;
    }
    .NavSide__main-content .colon3{
      flex: 1;
      display: flex;
      margin-left: 69px;
    }
    .NavSide__main-content .colon4{
      flex: 1;
      display: flex;
      margin-left: 88px;
    }

    .NavSide__main-content .input-penilaian {
      width: 100%;
      flex: 3;
    }
}
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
        width: 100%;
        margin-left: 0;}
      #nilaiMahasiswa {
        font-size: 9.5rem !important;
        font-weight: bold;
        text-align: center;
        border-radius: 30px;
        width: 90%;
        margin-left: 23px;
        height: 40px;
      }
      input.form-control:not(:placeholder-shown) {
      background: #f2f2f2 !important;
      border-color: #f2f2f2 !important;
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
            margin-right: 15px;
            font-weight: bold;
        }
        #detailpenilaian {
           width: 75px; ;
           font-size: 1rem; 
          
           margin-top: 20px;
           
        }
        #carddetailPenilaian {
            width: 100%;
            margin-left: 0;
            background-color: #f2f2f2;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
      #cardcatatan {
        background-color: #f2f2f2;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-left: 0;
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
            border: none;
            border-radius: 20px;
            padding: 10px 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            display: inline-flex; 
            align-items: center; 
            margin-top: 1.2cm;
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
            font-size: 1rem; 
            transition: color 0.3s ease;
        }

        .btn-kembali:hover .icon-circle i {
            color: white;
        }


        
    </style>
  </head>
  <body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand img ">
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
                        <div>
                            <input type="text" class="form-control form-control-lg text-center mx-auto"
                                id="nilaiMahasiswa" placeholder="" value="--" readonly />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-3">
                <img src="../../assets/img/img5.png" alt="Mahasiswa" class="img-fluid rounded" />
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
        </div>

               <button class="btn-kembali" onclick="location.href='mSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>
</main>
    
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

        // Sidebar Active Item Logic
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