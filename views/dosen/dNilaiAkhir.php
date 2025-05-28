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
    <link rel="stylesheet" href="style.css" />
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
        font-size: 9.5rem;
        font-weight: bold;
        text-align: center;
        border-radius: 30px;
        width: 90%;
        margin-left: 23px;
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

.logo {
  width: 120px;
  margin-left: auto;
  margin-right: auto;
}

.sidebar-link {
  color: white;
  font-weight: 600;
  padding: 12px 20px;
  border-radius: 0 0 0 0;
  transition: all 0.3s ease;
  position: relative;
  margin-bottom: 40px;
}

.sidebar-link.active {
  background-color: #ffffff;
  color: #4E6BFF;
  width: 227px;
  border-top-left-radius: 100px;
  border-bottom-left-radius: 100px;
  margin-left: -10px;
  padding-left: 28px;
}

    </style>
  </head>
  <body>

      <div class="d-flex flex-column sideNav p-4">


  <nav class="nav flex-column text-center">
    <a class="nav-link sidebar-link" href="#">Evaluasi</a>
    <a class="nav-link sidebar-link" href="#">Dokumen</a>
    <a class="nav-link sidebar-link active" href="#">Nilai Akhir</a>
  </nav>
</div>

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
                <input
                  type="text"
                  class="form-control form-control-lg text-center text"
                  id="nilaiMahasiswa"
                  placeholder=""
                  maxlength="1"
                />
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
            <div class="col-auto d-flex align-items-center ">
                <label for="nilaiLaporan">Nilai laporan:</label>
                <input
                  type="number"
                  class="form-control form-control-lg text-center"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3">
                  <label for="MateriPresentasi" id="labelpenilaian">Materi Presentasi:</label>
                <input
                  type="number"
                  class="form-control form-control-lg text-center"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3">
                  <label for="Penyampaian" id="labelpenilaian">Penyampaian:</label>
                <input
                  type="number"
                  class="form-control form-control-lg text-center"
                  id="detailpenilaian"
                  placeholder=""
                  maxlength="3">
                  <label for="NilaiProyek" id="labelpenilaian">Nilai Proyek:</label>
                <input
                  type="number"
                  class="form-control form-control-lg text-center"
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
           <button class="btn btn-kembali" style="margin-left: 50px;">
    <span class="icon-circle">
      <i class="bi bi-arrow-left"></i>
    </span>
    Kembali
  </button>
        </div>
        <div class="col-auto">
          <button class="btn btn-kirim" style="margin-right: 100px; width: 120px;">
            Kirim
          </button>
        </div>
        </div>
    </div>
    
  </div>
  </body>
</html>

