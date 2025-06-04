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
             <a onclick="location.href='dEvaluasiSidang.php'">
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

      <div class="NavSide__toggle">
        <i class="bi bi-list open"></i>
        <i class="bi bi-x-lg close"></i>
      </div>

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
                <div>
                <input
                  onclick="bukaKonfirmasiModal()"
                  type="text"
                  class="form-control form-control-lg text-center text"
                  id="nilaiMahasiswa"
                  placeholder=""
                  maxlength="1"
                />
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <img
              src="../../assets/img/img5.png"
              alt="Mahasiswa"
              class="img-fluid "
              style="width: 500px; height: 350px; margin-left: 20px; mix-blend-mode: multiply;"
            />
          </div>
        </div>
        <div class="row mt-5 align-items-center justify-content-between">
            <div class="card" id="carddetailPenilaian">
        <div class="card-body">
            <h3 class="card-title">Detail Penilaian :</h3>
            <div class="col-auto d-flex align-items-center ">
                <label for="nilaiLaporan">Nilai laporan:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="nilaiLaporan"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3"
                  >
                  <label for="MateriPresentasi" id="labelpenilaian">Materi Presentasi:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="MateriPresentasi"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3">
                  <label for="Penyampaian" id="labelpenilaian">Penyampaian:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="Penyampaian"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3">
                  <label for="NilaiProyek" id="labelpenilaian">Nilai Proyek:</label>
                <input
                  type="type"
                  class="form-control form-control-lg text-center"
                  name="NilaiProyek"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3">
                
              </div>
        </div>
        </div>
      </div>
      <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-md-12">
          <div class="card" id="cardcatatan">
            <div class="card-body">
              <h3 class="card-title">Catatan:</h3>
              <textarea
                class="form-control form-control-lg"
                id="catatan"
                placeholder=""
              ></textarea>
            </div>
          </div>
        </div>

      </div>
       <div class="row mt-5 align-items-center justify-content-between">
        <div class="col-auto">
           <button class="btn btn-kembali" style="margin-left: 50px;" onclick="pindahKeHalamanDaftarSidang()">
    <span class="icon-circle">
      <i class="bi bi-arrow-left"></i>
    </span>
    Kembali
  </button>
        </div>
        <div class="col-auto">
          <button class="btn btn-kirim" onclick="bukaKonfirmasiModalKirim()" style="margin-right: 100px; width: 120px;">
            Kirim
          </button>
        </div>
        </div>
    </div>
    </main>
    
  </div>
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
<div class="modal fade" id="konfirmasiModalKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-body">
        <p class="fs-5 fw-semibold">Apakah yakin ingin mengirim nilai akhir?</p>
        <div class="d-flex justify-content-center row mb5">
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
