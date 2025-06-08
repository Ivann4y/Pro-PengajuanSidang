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
    <title>Dosen - Nilai Akhir</title>
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

  .btn-kirim {
    width: 100% !important;
    max-width: 150px;
    margin: auto;
  }

  .btn-kembali {
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


    .modal-content {
  border-radius: 30px !important;
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
            <a href="#">
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
        <h2 class="text-heading">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
      </div>
    </div>
    <br>
        <div class="row align-items-center mb-4">
          <div class="col-lg-6 col-md-12 mb-3">
            <div class="card" id="cardNilai">
              <div class="card-body card-soft px-4 py-3 text-center">
                <h3 class="card-title mb-3" style="padding:10px ;">Nilai Mahasiswa:</h3>
                <div>
                <input
                  onclick="bukaKonfirmasiModal()"
                  type="text"
                  class="form-control form-control-lg text-center mx-auto"
                  id="nilaiMahasiswa"
                  placeholder=""
                  maxlength="1"
                />
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-12 mb-3">
            <img
              src="../../assets/img/img5.png"
              alt="Mahasiswa"
              class="img-fluid rounded"
              
            />
          </div>
        </div>
        <div class="row mt-5">
          <div class="col-12">
            <div class="card" id="carddetailPenilaian">
        <div class="card-body">
            <h3 class="card-title">Detail Penilaian :</h3>
            <div class="row justify-content-center align-items-center">
              <div class="col-lg-3 col-md-3 mb-3 d-flex align-items-center justify-content-center">
                <label for="nilaiLaporan" id="labelpenilaian">Nilai laporan</label>
                <label for=":" class="colon1">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="nilaiLaporan"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
              </div>
              <div class="col-lg-3 col-md-3 mb-3 d-flex align-items-center">
                  <label for="MateriPresentasi" id="labelpenilaian">Materi Presentasi</label>
                  <label for=":" class="colon2">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="MateriPresentasi"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
              </div>
              <div class="col-lg-3 col-md-3 mb-3 d-flex align-items-center">
                  <label for="Penyampaian" id="labelpenilaian">Penyampaian</label>
                  <label for=":" class="colon3">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="Penyampaian"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"/>
              </div>
              <div class="col-lg-3 col-md-3 mb-3 d-flex align-items-center">
                  <label for="NilaiProyek" id="labelpenilaian">Nilai Proyek</label>
                  <label for=":" class="colon4">:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
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
              <h3 class="card-title">Catatan:</h3>
              <textarea
                class="form-control form-control-lg"
                id="catatan"
                placeholder=""
                rows="4"
              ></textarea>
            </div>
          </div>
        </div>
      </div>
       <div class="row mt-5 justify-content-between">
        <div class="col-auto">
           <button class="btn btn-kembali">
    <span class="icon-circle">
      <i class="bi bi-arrow-left"></i>
    </span>
    Kembali
  </button>
        </div>
        <div class="col-auto">
          <button class="btn btn-kirim" onclick="bukaKonfirmasiModalKirim()" style="width: 120px;">
            Kirim
          </button>
        </div>
        </div>
    
    </main>
    
  </div>
  <div class="modal fade" id="konfirmasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-body">
        <p class="fs-5 fw-semibold">Apakah nilai akhir sama dengan nilai sementara?</p>
        <div class="d-flex flex-row justify-content-center row mb-5 kakimodal">
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
 <div class="modal fade" id="konfirmasiModalKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-body">
        <p class="fs-5 fw-semibold">Apakah yakin ingin mengirim nilai akhir?</p>
        <div class="d-flex justify-content-center row mb5 kakimodal">
          <div class="col-md-6">
          <button type="button" class="btnKonfirmasi" data-bs-dismiss="modal">Tidak</button>
          </div>
          <div class="col-md-6">
          <button type="button" class="btnKonfirmasi" onclick="kirimNilaiAkhir()">Iya</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>

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

   document.getElementById('detailpenilaian').addEventListener('input', function() {
  this.value = this.value.replace(/[^0-9]/g, '');
  if (this.value > 100) {
    this.value = '';
  }
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
        title: 'Error',
        text: 'Semua nilai harus diisi sebelum mengirim.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      }).then(() => {
        modal.hide();
      });
    } else{
    modal.hide();
    Swal.fire({
      title: 'Berhasil',
      text: 'Nilai akhir telah dikirim.',
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
