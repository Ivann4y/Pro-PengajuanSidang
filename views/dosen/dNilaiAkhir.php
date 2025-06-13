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
