<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Nilai Akhir</title>
  
  <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <link rel="stylesheet" href="../../css/button-styles.css" />
    <link rel="stylesheet" href="../../extra/style.css" />
  
  <style>
    body,
    .card,
    .form-control,
    h1, h2, h3, h4, h5, h6 {
      font-family: "Poppins", sans-serif !important;
      color: #464869;
    }

    #cardNilai {
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        height:100%;
        margin-left: 0; 

    }

    #carddetailPenilaian {
      width: 1000px;
      margin-left: 60px;
      background-color: rgb(235, 238, 245);
      border-radius: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

      #carddataMahasiswa{
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        
      }

    #cardcatatan {
      background-color: rgb(235, 238, 245);
      border-radius: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 1000px;
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
      background-color:rgb(235, 238, 245);
      border-color: #f2f2f2;
    }

    input.form-control:not(:placeholder-shown) {
      background: #f2f2f2 !important;
      border-color: #f2f2f2 !important;
    }

    #detailpenilaian {
      width: 75px ;
      font-size: 1rem;
      margin-right: auto;
      margin-top: 20px;
      background-color: rgb(235, 238, 245);
      border-color: #f2f2f2;
    }

    
    #catatan {
      width: 100%;
      height: 150px;
      border-radius: 30px;
      font-size: 1rem;
      margin-top: 20px;
      background-color: rgb(235, 238, 245);
      border-color: #f2f2f2;
    }

    label {
      margin-top: 20px;
      margin-right: 15px;
      font-weight: bold;
    }

    #labelpenilaian {
      margin-left: 30px;
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

    .modal-content {
      border-radius: 30px !important;
    }

    .student-image {
      width: 500px;
      height: 350px;
      margin-left: 20px;
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
          <a href="aDetailSidangTA.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
      </ul>
    </div>

    <div class="NavSide__toggle">
      <i class="bi bi-list open"></i>
      <i class="bi bi-x-lg close"></i>
    </div>

    <main class="NavSide__main-content">
      <div class="container-fluid bodyContainer">
        <div class="row mb-5">
           <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
        </div>

<div class="row mt-5 align-items-stretch">
  <div class="col-md-6 d-flex">
    <div class="card flex-fill h-100" id="cardNilai">
      <div class="card-body card-soft px-4 py-2 d-flex flex-column">
        <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Nilai Mahasiswa</h3>
<div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center" style="margin-left:50px;">
  <input
    type="text"
    class="form-control form-control-lg text-center mt-n2"
    id="nilaiMahasiswa"
    placeholder="A"
    maxlength="1"
    readonly/>
</div>


      </div>
    </div>
  </div>
  <div class="col-md-6 d-flex">
    <div class="card flex-fill h-100" id="carddataMahasiswa">
      <div class="card-body card-soft px-4 py-2 d-flex flex-column">
        <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Data Mahasiswa</h3>
        <div class="d-flex flex-wrap gap-1 px-4 py-3 flex-grow-1">
          <!-- Section 1 -->
          <div class="section" style="flex: 1 1 200px; margin-left:30px; margin-top:25px; color: #333;">
            <!-- NIM -->
            <div class="info-group mb-3">
              <div class="label-row d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-id-card"></i>
                <span class="fw-bold">NIM</span>
              </div>
              <div class="value-row text-secondary">0920240033</div>
            </div>
            <!-- Nama -->
            <div class="info-group mb-3">
              <div class="label-row d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-user"></i>
                <span class="fw-bold">Nama</span>
              </div>
              <div class="value-row text-secondary">Nayaka Ivanna</div>
            </div>
          </div>
          <!-- Section 2 -->
          <div class="section2" style="flex: 1 1 200px; margin-top:25px; color: #333;">
            <!-- Mata Kuliah -->
            <div class="info-group mb-3">
              <div class="label-row d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-book"></i>
                <span class="fw-bold">Mata Kuliah</span>
              </div>
              <div class="value-row text-secondary">Tugas Akhir</div>
            </div>
            <!-- Dosen Pembimbing -->
            <div class="info-group mb-3">
              <div class="label-row d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-user-tie"></i>
                <span class="fw-bold">Dosen Pembimbing</span>
              </div>
              <div class="value-row text-secondary">Timotius Victory</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
        <div class="row mt-5 align-items-center justify-content-between">
          <div class="card" id="carddetailPenilaian">
            <div class="card-body">
              <h3 class="card-title">Detail Penilaian :</h3>
              <div class="col-auto d-flex align-items-center">
                <label for="nilaiLaporan">Nilai laporan:</label>
                <input type="type" class="form-control form-control-lg text-center" name="nilaiLaporan" id="detailpenilaian" placeholder="90" maxlength="4" readonly>
                <label for="MateriPresentasi" id="labelpenilaian">Materi Presentasi:</label>
                <input type="type" class="form-control form-control-lg text-center" name="MateriPresentasi" id="detailpenilaian" placeholder="85" maxlength="4" readonly>
                <label for="Penyampaian" id="labelpenilaian">Penyampaian:</label>
                <input type="type" class="form-control form-control-lg text-center" name="Penyampaian" id="detailpenilaian" placeholder="95" maxlength="3" readonly>
                <label for="NilaiProyek" id="labelpenilaian">Nilai Proyek:</label>
                <input type="type" class="form-control form-control-lg text-center" name="NilaiProyek" id="detailpenilaian" placeholder="87" maxlength="3" readonly>
              </div>
            </div>
          </div>
        </div>

        
        <div class="row mt-5 align-items-center justify-content-between">
          <div class="col-md-12">
            <div class="card" id="cardcatatan">
              <div class="card-body">
                <h3 class="card-title">Catatan:</h3>
                <textarea class="form-control form-control-lg" id="catatan" placeholder="Pertahankan terus semangat belajarnya, kurangin main, banyakin minum air putih derr biar ga sariawan" readonly></textarea>
              </div>
            </div>
          </div>
        </div>

       
        <div class="row mt-5 align-items-center justify-content-between">
          <div class="col-auto">
            <button class="btn-kembali" onclick="location.href='mSidang.php'">
              <span class="icon-circle">
                <i class="fa-solid fa-arrow-left"></i>
              </span>
              Kembali
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
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
        if (!this.classList.contains("NavSide__sidebar-item--active")) {
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
