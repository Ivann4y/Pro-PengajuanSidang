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

    .btn-kirim {
      background-color: #4CD964;
      color: white;
      border-radius: 50px;
    }

    .btn-kirim:hover {
      border-color: #4CD964;
      background-color: #ffffff;
      color: #4CD964;
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

        @media (max-width: 700px) {
        .NavSide__sidebar {
            /* Sidebar disembunyikan ke kiri di mobile */
            transform: translateX(-100%); 
            width: 250px; /* Lebar sidebar saat muncul di mobile */
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2); 
        }
        /* Kelas ini akan ditambahkan oleh JavaScript untuk menampilkan sidebar */
        .NavSide__sidebar.NavSide__sidebar--active-mobile {
            transform: translateX(0);
        }
        
        .NavSide__main-content {
            margin-left: 0; 
            width: 100%;
            padding: 1rem;
        }
        .text-heading { font-size: large; }
        .upload-area-v2 { padding: 1.5rem; }
        .upload-area-v2 #initial-state svg, 
        .upload-area-v2 #selected-state svg { width: 60px; height: 60px; }
        .btn-custom-primary { padding: 0.6rem 1.2rem; font-size: 0.9rem; }

        /* Tampilkan tombol toggle di mobile */
        .NavSide__toggle {
            display: flex; 
        }
        /* Opsional: Geser tombol toggle saat sidebar terbuka */
        .NavSide__toggle.NavSide__toggle--active {
            left: calc(250px + 10px); /* 250px = lebar sidebar mobile */
        }
    }

        #NavSide {
        display: flex;
        min-height: 100vh;
        position: relative;
        background-color: #f8f9fa; 
    }
    .NavSide__sidebar-brand {
        padding: 10% 5% 10% 5%;
        text-align: center;
    }
    .NavSide__sidebar-brand img {
        width: 80%;
        max-width: 150px;
        height: auto;
        display: inline-block;
    }
    .NavSide__sidebar {
        position: fixed;
        top: 0px;
        left: 0px;
        bottom: 0px;
        width: 240px;
        background: #3b52f9;
        overflow-x: hidden;
        overflow-y: auto;
        z-index: 1000; /* Default z-index, toggle akan lebih tinggi */
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        padding-top: 2rem;
    }
    .NavSide__sidebar h4 { 
        text-align: center;
        font-weight: bold;
        margin-bottom: 2rem;
        color: white;
    }
    .NavSide__sidebar-nav {
        width: 100%;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
        flex-grow: 1;
    }
    
    .NavSide__sidebar-item {
        position: relative;
        display: block;
        width: calc(100% - 1rem); 
        margin-left: 1rem; 
        margin-right: 0;
        margin-bottom: 0.5rem;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .NavSide__sidebar-link {
        display: flex;
        align-items: center;
        width: 100%;
        text-decoration: none;
        color: white;
        padding: 12px 24px; 
        box-sizing: border-box;
        font-weight: normal;
    }
    .NavSide__sidebar-title {
        white-space: normal;
        text-align: left;
        line-height: 1.5;
        flex-grow: 1;
    }
    .NavSide__sidebar-item.NavSide__sidebar-item--active {
        background: #ffffff;
    }
    .NavSide__sidebar-item.NavSide__sidebar-item--active .NavSide__sidebar-link {
        color: #3b52f9;
        font-weight: 600;
    }





    </style>
  </head>
  <body>
    <!-- tanpa toggle fokus desktop -->
    <aside class="NavSide__sidebar" id="mainSidebar"> 
        <div class="NavSide__sidebar-brand">
          <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />

        </div>        
        <ul class="NavSide__sidebar-nav">
            <li class="NavSide__sidebar-item">
                <a href="" class="NavSide__sidebar-link">
                    <span class="NavSide__sidebar-title">Detail Pengajuan</span>
                </a>
            </li>
            <li class="NavSide__sidebar-item ">
                <a href="mPerbaikan.php" class="NavSide__sidebar-link">
                    <span class="NavSide__sidebar-title">Perbaikan</span>
                </a>
            </li>
            <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                <a href="mNilaiakhir.php" class="NavSide__sidebar-link">
                    <span class="NavSide__sidebar-title">Nilai Akhir</span>
                </a>
            </li>
        </ul>
    </aside>

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
 <main class="NavSide__main-content">
        
      

      <div class="container-fluid bodyContainer">
          <div class="row mb-5">
                 <h2 style="margin-left: 50px ;">
                <b>Detail Evaluasi - Sistem Evaluasi Sidang</b>
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
              style="width: 500px; height: 350px; margin-left: 20px; "
    
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
                <!--apabila terdapat catatan maka akan ada text catatan -->
                    <!-- <div class="form-control form-control-lg" style="min-height: 100px;">
            </div> -->

      </div>
       <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-auto">
           <button class="btn btn-kembali" style="margin-left: 50px;">
    <span class="icon-circle">
      <i class="bi bi-arrow-left"></i>
    </span>
    Kembali
  </button>

    </main>

<script>
  
  function isiNilaiAkhir() {
    document.getElementById("nilaiMahasiswa").value = "A";
    document.getElementsByName("nilaiLaporan")[0].value = "90";
    document.getElementsByName("MateriPresentasi")[0].value = "85";
    document.getElementsByName("Penyampaian")[0].value = "88";
    document.getElementsByName("NilaiProyek")[0].value = "92";
    const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
    modal.hide(); 
  }

</script>
  </body>
</html>
