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
    <link rel="stylesheet" href="../../assets/css/dNilaiAkhir.css" />
    <script src="../../assets/js/dNilaiAkhir.js"></script>
    <title>Dosen - Nilai Akhir</title>
    <style>
      
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
        <div class="row align-items-stretch">
          <div class="col-lg-49 mb-4 d-flex">
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
          
          <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
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
         
          <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
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
  <div class="col-lg-49 mb-4 d-flex">
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
  
 
    
        
          <div class="col-12 mb-4 d-flex">
            <div class="card flex-fill" id="carddetailPenilaian">
        <!-- GUNAKAN KODE BARU INI -->
<!-- STRUKTUR HTML YANG BENAR -->
<div class="card-body" id="card-penilaian-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title text-black mb-0">Detail Penilaian :</h3>
        <a onclick="bukaKonfirmasiModal()" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tekan ini jika ingin menggunakan nilai sementara">
            <i class="bi bi-pencil-fill fs-5" style="color: var(--text-dark);"></i>
        </a>
    </div>

    <!-- Wadah untuk tampilan desktop (horizontal) -->
    <div class="penilaian-container">
        <div class="penilaian-item">
            <label for="nilaiLaporan">Nilai laporan :</label>
            <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan" maxlength="3">
        </div>
        <div class="penilaian-item">
            <label for="MateriPresentasi">Materi Presentasi :</label>
            <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi" maxlength="3">
        </div>
        <div class="penilaian-item">
            <label for="Penyampaian">Penyampaian :</label>
            <input type="text" class="form-control text-center input-nilai" name="Penyampaian" maxlength="3">
        </div>
        <div class="penilaian-item">
            <label for="NilaiProyek">Nilai Proyek :</label>
            <input type="text" class="form-control text-center input-nilai" name="NilaiProyek" maxlength="3">
        </div>
    </div> <!-- << PENUTUP DIV PINDAH KE SINI -->

    <!-- Wadah BARU untuk tampilan tablet/mobile (vertikal) -->
    <div class="penilaian-grid-vertical">
        <label for="nilaiLaporan_v">Nilai laporan</label> <span>:</span>
        <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan_v" maxlength="3">
        
        <label for="MateriPresentasi_v">Materi Presentasi</label> <span>:</span>
        <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi_v" maxlength="3">
        
        <label for="Penyampaian_v">Penyampaian</label> <span>:</span>
        <input type="text" class="form-control text-center input-nilai" name="Penyampaian_v" maxlength="3">
        
        <label for="NilaiProyek_v">Nilai Proyek</label> <span>:</span>
        <input type="text" class="form-control text-center input-nilai" name="NilaiProyek_v" maxlength="3">
    </div>
</div>
        </div>
      </div>
      
      
        <div class="col-12 mb-4 d-flex">
          <div class="card flex-fill" id="cardcatatan">
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
  
</script>
  </body>
</html>
