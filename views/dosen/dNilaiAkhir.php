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

    <title>Dosen - Nilai Akhir</title>
    <style>
      #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
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

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
            filter: brightness(0) invert(1);
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
            margin-bottom: 15px;
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
            color: white;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active .NavSide__sidebar-title {
  color: #4B68FB !important;
}

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
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
            padding: 20px 20px 20px calc(20px + 1cm);
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
        }

        .NavSide__main-content h2 {
            margin-bottom: 1.2cm;
            font-weight: 700;
        }

        .NavSide__main-content h3 {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 0.2cm;
        }
      .NavSide__main-content {
  flex-grow: 1;
  padding: 20px;
  margin-left: 300px;
  overflow-y: auto;
  transition: margin-left 0.5s ease-in-out;
  background-color: #f9fafb;
  padding-top: 3vh;
}


.info-group {
  font-size: 1rem;
}
.value-row {
  font-size: 1rem;
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

      .btn-kembali {
        background-color: #4B68FB;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 0 25px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 45px;
      }

      .btn-kembali:hover {
        background-color: white;
        transform: translateY(-2px);
        color:#4B68FB;
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
      }

      .btn-kembali:hover .icon-circle i {
        color: white;
      }

      #cardNilai {
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-left: 0;
      }
      #carddataMahasiswa{
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
      }

      #nilaiMahasiswa {
        font-size: 9.5rem !important;
        font-weight: bold;
        text-align: center;
        border-radius: 30px;
        width: 100%;
        margin-left: 23px;
        height: 40px;
        background-color: rgb(235, 238, 245) !important;
        border-color: rgb(235, 238, 245) !important;
      }

      

      #cardPenilaian {
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-left: 50px;
      }

      label
      {
        margin-top: 20px;
        margin-right: 15px;
        font-weight: 550;
      }
      .label-row i {
  font-size: 1.5rem;    /* Perbesar icon */
}

.label-row .fw-bold {
  font-size: 1.2rem;    /* Perbesar tulisan label */
}

      #detailpenilaian {
        width: 75px; ;
        font-size: 1rem; 
        margin-top: 20px;
      }

      #carddetailPenilaian {
        width: 100%;
        margin-left: 0;
        background-color: rgb(235, 238, 245);
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      #cardcatatan {
        background-color: rgb(235, 238, 245);
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
      #iyamodal{
        margin-top: 20px;
        background-color: #4FD382;
      }
      #tidakmodal{
        margin-top: 20px;
        background-color: #FD7D7D;
      }

      .modal-content {
        border-radius: 30px !important;
      }

      .btn-kirim {
        background-color: #4B68FB;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 0 25px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 45px;
      }

      .btn-kirim:hover {
        background-color: #3a56e8;
        transform: translateY(-2px);
      }
   
      .col-lg-49 {
  flex: 0 0 49%;
  max-width: 49%;
}


#catatan::placeholder {
  color: #888 !important;      
  opacity: 60% !important;       
}
      input.form-control:focus,
input.form-control:active {
  background-color: #fff !important;
  border-color: #ced4da !important;
  box-shadow: none !important;
  color:rgb(0, 0, 0) !important;
}

      @media (max-width: 750px) {
        .page-nama{
          margin-left:10px;
        }
        h2{
          margin-left: 10px;
          margin-top: 50px;
        }
       .col-lg-49{
          display: block;
          flex: 0 0 95.5% ;
          max-width: 95.5% ;
        }
        
             .NavSide__main-content {
  flex-grow: 1;
  padding: 20px;
  margin-left: 0;
  overflow-y: auto;
  transition: margin-left 0.5s ease-in-out;
  background-color: #f9fafb;
  padding-top: 3vh;
}
        .row.mt-5.justify-content-between {
          flex-direction: row !important;
          justify-content: center !important;
          align-items: center !important;
          display: flex !important;
          gap: 20px !important;
        }

        .row.mt-5.justify-content-between .col-auto {
          flex: auto;
          text-align: center;
          margin-bottom: 0 !important;
          padding: 10px;
        }

        .btn-kirim {
          width: 100% !important;
          max-width: 150px;
          margin: auto;
        }

        #carddetailPenilaian,
        #cardcatatan {
          width: 100% !important;
          margin-left: 0 !important;
        }

        #cardNilai {
          width: 100% !important;
          margin-left: 0 !important;
          margin-bottom: 40px;
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
          text-align: center;
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
    max-width: 48%;
    flex: 1 1 48%;
  }
  .modal-body .btnKonfirmasi {
  width: 10% !important;
}
  .kakimodal {
    gap: 10px !important;
    justify-content: center !important;
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
         .section{
          margin-left:0px !important;
           margin-top: 0px !important;
        }
        .section2{
          margin-top: 5px !important;
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
        .section{
          margin-left:0px !important;
           margin-top: 0px !important;
        }
        .section2{
          margin-top: 5px !important;
        }
       
        .col-lg-49{
          display: block;
          flex: 0 0 94.5% ;
          max-width: 94.5% ;
        }
        .NavSide__main-content .row.mt-5.justify-content-between .col-auto {
          flex: auto;
          text-align: center;
          margin-bottom: 0 !important;
          padding: 10px;
        }

        .NavSide__main-content .btn-kirim {
          width: 100% !important;
          max-width: 150px;
          margin: auto;
        }

        .NavSide__main-content .btn-kembali {
          width: 100% !important;
          max-width: 150px;
          margin: auto;
        }

        .NavSide__main-content #carddetailPenilaian,
        .NavSide__main-content #cardcatatan {
          width: 100% !important;
          margin-left: 0 !important;
        }

        .NavSide__main-content #cardNilai {
          width: 100% !important;
          margin-left: 0 !important;
          margin-bottom: 0px;
          margin-top: 30px;
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
          text-align: center;
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
    max-width: 48%;
    flex: 1 1 48%;
  }
  .modal-body .btnKonfirmasi {
  width: 10% !important;
}
  .kakimodal {
    gap: 10px !important;
    justify-content: center !important;
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
        .bi-pencil-fill{
          margin-right:17px !important;
        }
   
      }
      .page-nama {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: -35px;
            margin-bottom: 20px;
        }
        .tooltip .tooltip-inner {
  background-color: rgb(235, 238, 245) !important;
  color:black !important;      /* Ganti warna teks sesuai kebutuhan */
  border: 1px solid black;      /* Opsional: tambahkan border agar lebih jelas */
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.tooltip.bs-tooltip-top .tooltip-arrow::before,
.tooltip.bs-tooltip-bottom .tooltip-arrow::before,
.tooltip.bs-tooltip-start .tooltip-arrow::before,
.tooltip.bs-tooltip-end .tooltip-arrow::before {
  border-top-color: rgb(235, 238, 245) !important;
  border-bottom-color: rgb(235, 238, 245) !important;
  border-left-color: rgb(235, 238, 245) !important;
  border-right-color: rgb(235, 238, 245) !important;
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
          <li class="NavSide__sidebar-item ">
            <b></b>
            <b></b>
             <a href="dEvaluasiSidang.php">
              <span class="NavSide__sidebar-title fw-semibold">Evaluasi</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
            <b></b>
            <b></b>
            <a href="dDokumenRevisi.php">
              <span class="NavSide__sidebar-title fw-semibold">Dokumen</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
            <b></b>
            <b></b>
            <a href="dNilaiAkhir.php">
              <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
            </a>
          </li>
         </ul>
      </div>

      <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
      </div>

      <main class="NavSide__main-content">
      <div class="container-fluid">
           <div class="row mb-3">
      <div class="col-12">
        <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
        <!--<p class="page-nama">M. Harris Nur S</p>-->
      </div>
    </div>
    <br>
        <div class="row align-items-stretch mb-4">
          <div class="col-lg-49 mb-3 d-flex">
  <div class="card flex-fill" id="carddataMahasiswa">
    <div class="card-body card-soft px-4 py-3">
      <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Data Mahasiswa</h3>
      <div class="d-flex flex-wrap gap-1 px-4 py-3">
        
        <div class="section" style="flex: 1 1 200px; margin-left:30px;  color: #333;">
         
          <div class="info-group mb-3">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-id-card"></i>
              <span class="fw-bold">NIM</span>
            </div>
            <div class="value-row text-secondary fw-bold">0920240033</div>
          </div>
          
          <div class="info-group mb-3" style="margin-top:45px;">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-user"></i>
              <span class="fw-bold">Nama</span>
            </div>
            <div class="value-row text-secondary fw-bold">M. Harris Nur S.</div>
          </div>
        </div>
        
        <div class="section2" style="flex: 1 1 200px; color: #333;">
         
          <div class="info-group mb-3">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-book"></i>
              <span class="fw-bold">Mata Kuliah</span>
            </div>
            <div class="value-row text-secondary fw-bold">Tugas Akhir</div>
          </div>
         
          <div class="info-group mb-3" style="margin-top:45px;">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-user-tie"></i>
              <span class="fw-bold">Dosen Pembimbing</span>
            </div>
            <div class="value-row text-secondary fw-bold">Timotius Victory</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <div class="col-lg-49 mb-3 d-flex">
    <div class="card flex-fill" id="cardNilai">
      <div class="card-body card-soft px-4 py-3 text-center">
        <h3 class="card-title mb-3 text-black" style="padding:10px ;">Nilai Mahasiswa:</h3>
        <div>
          <input
            type="text"
            class="form-control form-control-lg text-center mx-auto"
            id="nilaiMahasiswa"
            placeholder="--"
            maxlength="1"
            style="cursor:pointer;"
            readonly
          />
        </div>
      </div>
    </div>
  </div>
  
 
    
        <div class="row mt-5">
          <div class="">
            <div class="card" id="carddetailPenilaian">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="card-title text-black mb-0">Detail Penilaian :</h3>
            <a onclick="bukaKonfirmasiModal()" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tekan ini jika ingin menggunakan nilai sementara" data-bs-boundary="window" data-bs-fallback-placements="[]">
  <i class="bi bi-pencil-fill" style="margin-right: 25px;"></i>
</a>
            </div>
            <div class="row justify-content-center align-items-center">
              <div class="col d-flex align-items-center">
                <label for="nilaiLaporan" id="labelpenilaian" class="text-black">Nilai laporan</label>
                <label for=":" class="colon1">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center input-nilai"
                  name="nilaiLaporan"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
              </div>
              <div class="col d-flex align-items-center">
                  <label for="MateriPresentasi" id="labelpenilaian" class="text-black">Materi Presentasi</label>
                  <label for=":" class="colon2">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center input-nilai"
                  name="MateriPresentasi"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
              </div>
              <div class="col d-flex align-items-center ">
                  <label for="Penyampaian" id="labelpenilaian" class="text-black">Penyampaian</label>
                  <label for=":" class="colon3">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center input-nilai"
                  name="Penyampaian"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
              </div>
              <div class="col d-flex align-items-center ">
                  <label for="NilaiProyek" id="labelpenilaian" class="text-black">Nilai Proyek</label>
                  <label for=":" class="colon4">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center input-nilai"
                  name="NilaiProyek"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
                </div>
                
              </div>
        </div>
        </div>
      </div>
      </div>
      <div class="row mt-5">
        <div class="col-12">
          <div class="card" id="cardcatatan">
            <div class="card-body">
              <h3 class="card-title text-black">Catatan:</h3>
              <textarea
                class="form-control form-control-lg"
                id="catatan"
                placeholder="Masukan catatan di sini...(Opsional)"
                rows="4"
              ></textarea>
            </div>
          </div>
        </div>
      </div>
       <div class="row mt-5 justify-content-between">
        <div class="col-auto">
           <button class="btn btn-kembali" onclick="pindahKeHalamanDaftarSidang()">
    <span class="icon-circle">
      <i class="fa-solid fa-arrow-left"></i>
    </span>
    Kembali
  </button>
        </div>
        <div class="col-auto">
          <button class="btn btn-setujui" onclick="bukaKonfirmasiModalKirim()">
            Kirim
          </button>
        </div>
        </div>
    
    </main>
    
  </div>
  
  <div class="modal fade" id="konfirmasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
      <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                  </div>
      <div class="modal-body">
       <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah nilai akhir sama dengan nilai sementara?</p>
        <div class="d-flex justify-content-between px-5">
          <button type="button" class="btnKonfirmasi btn-tolak" id="tidakmodal"onclick="TutupKonfirmasiModal()">Tidak</button>
          <button type="button" class="btnKonfirmasi btn-setujui" id="iyamodal" onclick="isiNilaiAkhir()">Iya</button>
        </div>
      </div>
    </div>
  </div>
</div>
 <div class="modal fade" id="konfirmasiModalKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
      <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                  </div>
      <div class="modal-body">
        <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah yakin ingin mengirim nilai akhir?</p>
        <div class="d-flex justify-content-between px-5">
          <button type="button" class="btnKonfirmasi  btn-tolak" id="tidakmodal" data-bs-dismiss="modal">Tidak</button>
          <button type="button" class="btnKonfirmasi  btn-setujui" id="iyamodal" onclick="kirimNilaiAkhir()">Iya</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
  // Tampilkan modal konfirmasi saat halaman dibuka
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
  modal.show();
});

  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
 function showTooltipPensil() {
  var tooltipTrigger = document.querySelector('[data-bs-toggle="tooltip"]');
  var tooltipInstance = bootstrap.Tooltip.getInstance(tooltipTrigger) || new bootstrap.Tooltip(tooltipTrigger);
  tooltipInstance.show();
  setTimeout(function () {
    tooltipInstance.hide();
  }, 5000);
}


    let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // Sidebar Active Item Logic
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }

   document.querySelectorAll('.input-nilai').forEach(function(input){
  input.addEventListener('input', function() {
  this.value = this.value.replace(/[^0-9]/g, '');
  if (this.value.length > 1 && this.value.startsWith('0')) {
      this.value = this.value.replace(/^0+/, '');
    }
  if (this.value > 100 ) {
    this.value = '';
  }
    });
   });
  document.getElementById('nilaiMahasiswa').addEventListener('input', function() {
  this.value = this.value.replace(/[^A-Ea-e]/g, '').toUpperCase();
  if (!this.value || this.value.length > 1) {
    this.value = '';
  }
});
  function pindahKeHalamanDaftarSidang() {
    window.location.href = "dDaftarSidang.php";
  }


  function bukaKonfirmasiModalKirim() {
    const modal = new bootstrap.Modal(document.getElementById('konfirmasiModalKirim'));
    modal.show();
  }
  function kirimNilaiAkhir() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModalKirim'));
    const nilaiMahasiswa = document.getElementById("nilaiMahasiswa").value;
    const nilaiLaporan = document.getElementsByName("nilaiLaporan")[0].value;
    const materiPresentasi = document.getElementsByName("MateriPresentasi")[0].value;
    const penyampaian = document.getElementsByName("Penyampaian")[0].value;
    const nilaiProyek = document.getElementsByName("NilaiProyek")[0].value;
    if (nilaiMahasiswa === "" || nilaiLaporan === "" || materiPresentasi === "" || penyampaian === "" || nilaiProyek === "") {
      Swal.fire({
        title: 'Semua nilai harus diisi sebelum mengirim!',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      }).then(() => {
        modal.hide();
      });
    } else{
    modal.hide();
    Swal.fire({
      title: 'Nilai akhir telah dikirim.',
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
  
}
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
    showTooltipPensil();
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
  showTooltipPensil();
}

  function hitungRataRataDanSetNilai() {
  // Ambil semua nilai input detail penilaian
  const nilaiLaporan = parseFloat(document.getElementsByName("nilaiLaporan")[0].value) || 0;
  const materiPresentasi = parseFloat(document.getElementsByName("MateriPresentasi")[0].value) || 0;
  const penyampaian = parseFloat(document.getElementsByName("Penyampaian")[0].value) || 0;
  const nilaiProyek = parseFloat(document.getElementsByName("NilaiProyek")[0].value) || 0;

  // Hitung rata-rata
  const arr = [nilaiLaporan, materiPresentasi, penyampaian, nilaiProyek];
  // Jika ada yang kosong, jangan proses
  if (arr.some(v => v === 0)) return;

  const rataRata = (nilaiLaporan + materiPresentasi + penyampaian + nilaiProyek) / 4;

  // Set nilai mahasiswa otomatis jika rata-rata 85-100
  if (rataRata >= 85 && rataRata <= 100) {
    document.getElementById("nilaiMahasiswa").value = "A";
  } else if (rataRata >= 70 && rataRata < 85) {
    document.getElementById("nilaiMahasiswa").value = "B";
  } else if (rataRata >= 60 && rataRata < 70) {
    document.getElementById("nilaiMahasiswa").value = "C";
  } else if (rataRata >= 40 && rataRata < 60) { 
    document.getElementById("nilaiMahasiswa").value = "D";
  } else if (rataRata < 40) {
    document.getElementById("nilaiMahasiswa").value = "E";
  }
  }
  document.getElementsByName("nilaiLaporan")[0].addEventListener('input', hitungRataRataDanSetNilai);
document.getElementsByName("MateriPresentasi")[0].addEventListener('input', hitungRataRataDanSetNilai);
document.getElementsByName("Penyampaian")[0].addEventListener('input', hitungRataRataDanSetNilai);
document.getElementsByName("NilaiProyek")[0].addEventListener('input', hitungRataRataDanSetNilai);

</script>
  </body>
</html>
