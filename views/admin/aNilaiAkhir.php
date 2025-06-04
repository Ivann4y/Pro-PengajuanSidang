<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Nilai Akhir</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../css/style.css" />
  <link rel="stylesheet" href="../../extra/style.css" />
  
  <style>
    body,
    .card,
    .form-control,
    h1, h2, h3, h4, h5, h6 {
      font-family: "Poppins", sans-serif !important;
      color: #464869;
    }
    
    /* Card Styles */
    #cardNilai {
      background-color: #f2f2f2;
      border-radius: 50px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 500px;
      height: 350px;
      margin-left: 50px;
    }
    
    #carddetailPenilaian {
      width: 1000px;
      margin-left: 60px;
      background-color: #f2f2f2;
      border-radius: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    #cardcatatan {
      background-color: #f2f2f2;
      border-radius: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 1000px;
      margin-left: 50px;
    }
    
    /* Input Styles */
    #nilaiMahasiswa {
      font-size: 9.5rem !important;
      font-weight: bold;
      text-align: center;
      border-radius: 30px;
      width: 90%;
      margin-left: 23px;
      height: 40px;
      background-color: #f2f2f2;
      border-color: #f2f2f2;
    }
    
    input.form-control:not(:placeholder-shown) {
      background: #f2f2f2 !important;
      border-color: #f2f2f2 !important;
    }
    
    #detailpenilaian {
      width: 75px;
      font-size: 1rem;
      margin-right: auto;
      margin-top: 20px;
      text-size-adjust: 100000px;
      background-color: #f2f2f2;
      border-color: #f2f2f2; 
    }
    
    #catatan {
      width: 100%;
      height: 150px;
      border-radius: 30px;
      font-size: 1rem;
      margin-top: 20px;
      background-color: #f2f2f2;
      border-color: #f2f2f2;
    }
    
    /* Label Styles */
    label {
      margin-top: 20px;
      margin-right: 15px;
      font-weight: bold;
    }
    
    #labelpenilaian {
      margin-left: 30px;
    }
    
    /* Button Styles */
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
    
    
    .btnKonfirmasi {
      background-color: #464869;
      color: white;
      border-radius: 15px;
      padding: 10px 20px;
      font-size: 0.9rem;
      height: 40px;
      width: 100px;
    }
    
    /* Icon Styles */
    .icon-circle {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: white;
      color: #4B68FB;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      font-size: 16px;
    }
    
    /* Modal Styles */
    .modal-content {
      border-radius: 30px !important;
    }
    
    /* Image Styles */
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
          <b></b><b></b>
          <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <b></b><b></b>
          <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
      </ul>
    </div>


          <div class="NavSide__toggle">
        <i class="bi bi-list open"></i>
        <i class="bi bi-x-lg close"></i>
      </div>

      
    <main class="NavSide__main-content"> <!-- panjang lebar tampilan nilai akhir menyesuaikan -->

    <div class="container-fluid bodyContainer">
      <div class="row mb-5">
        <h2 style="margin-left: 50px;">
          <b>Detail Evaluasi - Sistem Evaluasi Sidang</b>
        </h2>
      </div>
      

      <!-- Main Content Row -->
      <div class="row mt-5 align-items-center justify-content-between">
        <!-- Nilai Card -->
        <div class="col-md-6">
          <div class="card" id="cardNilai">
            <div class="card-body">
              <h3 class="card-title" style="padding:10px;">Nilai Mahasiswa:</h3>
              <div>
                <input
                  type="text"
                  class="form-control form-control-lg text-center text"
                  id="nilaiMahasiswa"
                  placeholder="A"
                  maxlength="1"
                  disabled
                  
                />
              </div>
            </div>
          </div>
        </div>
        

        <!-- Gambar  -->
        <div class="col-md-6">
          <img
            src="../../assets/img/img5.png"
            alt="Mahasiswa"
            class="img-fluid student-image"
            style="mix-blend-mode: multiply;"
          />
        </div>
      </div>

      
      <!-- Detail Penilaian Card -->
      <div class="row mt-5 align-items-center justify-content-between">
        <div class="card" id="carddetailPenilaian">
          <div class="card-body">
            <h3 class="card-title">Detail Penilaian :</h3>
            <div class="col-auto d-flex align-items-center">
              <label for="nilaiLaporan">Nilai laporan:</label>
              <input
                type="type"
                class="form-control form-control-lg text-center"
                name="nilaiLaporan"
                id="detailpenilaian"
                placeholder="90"
                maxlength="3"
                disabled>
                
                
              <label for="MateriPresentasi" id="labelpenilaian">Materi Presentasi:</label>
              <input
                type="type"
                class="form-control form-control-lg text-center"
                name="MateriPresentasi"
                id="detailpenilaian"
                placeholder="85"
                maxlength="3"
                disabled>
                

              <label for="Penyampaian" id="labelpenilaian">Penyampaian:</label>
              <input
                type="type"
                class="form-control form-control-lg text-center"
                name="Penyampaian"
                id="detailpenilaian"
                placeholder="95"
                maxlength="3"
                disabled>
                

              <label for="NilaiProyek" id="labelpenilaian">Nilai Proyek:</label>
              <input
                type="type"
                class="form-control form-control-lg text-center"
                name="NilaiProyek"
                id="detailpenilaian"
                placeholder="93"
                maxlength="3"
                disabled>
                
            </div>
          </div>
        </div>
      </div>
      

      <!-- Catatan Card -->
      <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-md-12">
          <div class="card" id="cardcatatan">
            <div class="card-body">
              <h3 class="card-title">Catatan:</h3>
              <textarea
                class="form-control form-control-lg"
                id="catatan"
                placeholder="Pertahankan terus semangat belajarnya kurangin main banyakin minum air putih derr biar ga sariawan " 
                disabled>
              </textarea>
            </div>
          </div>
        </div>
      </div>
      

      <!-- Fungsi Tombol Kembali -->
      <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-auto">
          <button class="btn btn-kembali" style="margin-left: 50px;" onclick="pindahKeHalamanDaftarSidang()">
            <span class="icon-circle">
              <i class="bi bi-arrow-left"></i>
            </span>
            Kembali
          </button>
        </div>
      </div>
    </div>
  </div>
  

  <!-- konfimasi Modal Nilai -->
  <div class="modal fade" id="konfirmasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-3">
        <div class="modal-body">
          <p class="fs-5 fw-semibold">Apakah nilai akhir sama dengan nilai sementara?</p>
          <div class="d-flex justify-content-center row mb5">
            <div class="col-md-6">
              <button type="button" class="btnKonfirmasi" onclick="TutupKonfirmasiModal()">Tidak</button>
            </div>
            <div class="col-md-6">
              <button type="button" class="btnKonfirmasi" onclick="isiNilaiAkhir()">Iya</button>
            </div>
          </div>
        </div>
      </div>
    </div>
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
        if(!this.classList.contains("NavSide__sidebar-item--active")) {
          for (let j = 0; j < listItems.length; j++) {
            listItems[j].classList.remove("NavSide__sidebar-item--active");
          }
          this.classList.add("NavSide__sidebar-item--active");
        }
      };
    }
    
    function bukaKonfirmasiModal() {
      const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
      modal.show();
    }
    
    function TutupKonfirmasiModal() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
      modal.hide();
      setTimeout(() => {
        const input = document.getElementById("nilaiMahasiswa");
        input.focus();
      }, 300);
    }
    
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