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

  <title>Admin - Nilai Akhir</title>
  <style>
    /* ... CSS seperti semula, tanpa perubahan untuk carddetailPenilaian dan cardcatatan (pastikan width dan margin-left di-set ke 100% / 0) ... */
    #NavSide {
      display: flex;
      min-height: 100vh;
      position: relative;
    }
    /* (semua CSS NavSide dan styling lain dipertahankan) */

    body,
    .card,
    .form-control,
    h1, h2, h3, h4, h5, h6 {
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
    #carddataMahasiswa {
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
      height: 50%;
      background-color: rgb(235, 238, 245) !important;
      border-color: rgb(235, 238, 245) !important;
      cursor: default;
    }

    label {
      margin-right: 15px;
      font-weight: 550;

    }

    #detailpenilaian {
      width: 75px;
      font-size: 1rem;
      border-color: transparent; /* Ini mungkin belum cukup kuat */
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

    .form-control {
      background-color: rgb(235, 238, 245);

    }

    #catatan {
      width: 100%;
      height: 150px;
      border-radius: 30px;
      font-size: 1rem;
      margin-top: 20px;
      pointer-events: none; /* Membuat elemen tidak bisa di-klik atau di-fokus */
      resize: none;         /* Menghilangkan handle untuk resize */
      border: none;
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

    /* Responsif dan media queries seperti semula, kecuali bagian modal atau kirim */
    @media (max-width: 750px) {
      /* ... */
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
    }
    @media (max-width: 1000px) {
      /* ... */
      .NavSide__main-content #carddetailPenilaian,
      .NavSide__main-content #cardcatatan {
        width: 100% !important;
        margin-left: 0 !important;
      }
      .NavSide__main-content #cardNilai {
        width: 100% !important;
        margin-left: 0 !important;
        margin-bottom: 40px;
      }
      .NavSide__main-content #nilaiMahasiswa {
        font-size: 5rem !important;
      }
      .NavSide__main-content #detailpenilaian {
        width: 15% !important;
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
      color:black !important;
      border: 1px solid black;
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
        <li class="NavSide__sidebar-item">
          <a href="aDetailSidang.php">
            <span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span>
          </a>
        </li>
        <li class="NavSide__sidebar-item">
          <a href="aEvaluasi.php">
            <span class="NavSide__sidebar-title fw-semibold">Evaluasi</span>
          </a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <a href="aNilaiAkhir.php">
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

    <!-- Top bar desktop -->
      
            <div class="dashboard-header p-3">
                <h2 class="text-heading text-black walcomeText" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="aNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
              </div>


        <!-- Baris Nilai & Data Mahasiswa -->
        <div class="row align-items-stretch mb-4 p-2">
          <div class="col-lg-6 mb-3 d-flex">
            <div class="card flex-fill" id="cardNilai">
              <div class="card-body card-soft px-3 py-3 text-center">
                <h3 class="card-title mb-3 text-black" style="padding:10px;">Nilai Mahasiswa</h3>
                <div>
                  <input
                    type="text"
                    class="form-control form-control-lg text-center mx-auto"
                    id="nilaiMahasiswa"
                    placeholder="A"
                    maxlength="1"
                    readonly
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-3 d-flex">
            <div class="card flex-fill" id="carddataMahasiswa">
              <div class="card-body card-soft px-4 py-3">
                <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Data Mahasiswa</h3>
                <div class="d-flex flex-wrap gap-1 px-4 py-3">
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
                      <div class="value-row text-secondary">Nayakan Ivanna</div>
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

        <!-- Baris Detail Penilaian tanpa modal -->
        <div class="row mt-3 p-3">
            <div class="card flex-fill h-100" id="carddetailPenilaian">
              <div class="card-body px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <h3 class="card-title text-black mb-0">Detail Penilaian :</h3>
                </div>
                <div class="row justify-content-center align-items-center">
                  <div class="col d-flex align-items-center">
                    <label for="nilaiLaporan" class="text-black me-2 mb-2">Nilai laporan</label>
                    <label class="colon1 me-2 mb-2">:</label>
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="nilaiLaporan"
                      id="detailpenilaian"
                      placeholder=" 87"
                      maxlength="3"
                      readonly/>
                  </div>
                  <div class="col d-flex align-items-center">
                    <label for="MateriPresentasi" class="text-black me-2 mb-2">Materi Presentasi</label>
                    <label class="colon2 me-2 mb-2">:</label>
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="MateriPresentasi"
                      id="detailpenilaian"
                      placeholder="87"
                      maxlength="3"
                      readonly/>
                  </div>
                  <div class="col d-flex align-items-center">
                    <label for="Penyampaian" class="text-black me-2 mb-2">Penyampaian</label>
                    <label class="colon3 me-2 mb-2">:</label>
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="Penyampaian"
                      id="detailpenilaian"
                      placeholder="90"
                      maxlength="3"
                      readonly/>
                  </div>
                  <div class="col d-flex align-items-center">
                    <label for="NilaiProyek" class="text-black me-2 mb-2">Nilai Proyek</label>
                    <label class="colon4 me-2 mb-2">:</label>
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="NilaiProyek"
                      id="detailpenilaian"
                      placeholder="95"
                      maxlength="3"
                      readonly/>
                  </div>
                </div>
              </div>
            </div>
        </div>


        <!-- Baris Catatan -->
        <div class="row mt-3 p-3">
            <div class="card flex-fill h-100" id="cardcatatan">
              <div class="card-body px-4 py-3 d-flex flex-column">
                <h3 class="card-title text-black mb-4">Catatan:</h3>
                <textarea
                  class="form-control flex-grow-1"
                  id="catatan"
                  placeholder="semangatt terus pertahankan semangat belajarnya yaa"
                  rows="4"
                  readonly
                ></textarea>
              </div>
            </div>
        </div>

        <!-- Baris Tombol Kembali saja -->
        <div class="row mt-5 justify-content-between">
          <div class="col-auto">
            <button class="btn btn-kembali" onclick="pindahKeHalamanDaftarSidang()">
              <span class="icon-circle">
                <i class="fa-solid fa-arrow-left"></i>
              </span>
              Kembali
            </button>
          </div>
          <!-- Tombol Kirim dihapus -->
        </div>

      </div> <!-- akhir container-fluid -->
    </main>
  </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
      // Inisialisasi tooltip Bootstrap (jika ada)
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
      });


      // Fungsi untuk membuat elemen menjadi statis (tidak bisa diklik/fokus)
      const makeElementStatic = (element) => {
        if (element) {
          // 1. Pindahkan teks dari atribut 'placeholder' ke 'value' agar terlihat
          const placeholderText = element.getAttribute('placeholder');
          if (placeholderText) {
            element.value = placeholderText.trim(); // .trim() untuk hapus spasi berlebih
            element.removeAttribute('placeholder');
          }

          // 2. Buat elemen tidak bisa diinteraksi
          element.style.pointerEvents = 'none'; // Menonaktifkan semua event mouse (klik, hover)
          element.style.userSelect = 'none';   // Mencegah teks di dalamnya bisa di-select
          element.tabIndex = -1;               // Menghapus dari urutan navigasi keyboard (Tab)
        }
      };

      // Terapkan fungsi ke semua input nilai di dalam 'carddetailPenilaian'
      const scoreInputs = document.querySelectorAll('#carddetailPenilaian input.form-control');
      scoreInputs.forEach(makeElementStatic);

      // Terapkan fungsi ke textarea 'catatan'
      const notesTextarea = document.getElementById('catatan');
      makeElementStatic(notesTextarea);

      // --- AKHIR SOLUSI ---
    });

    // Kode untuk toggle sidebar (tetap dipertahankan)
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    menuToggle.onclick = function() {
      menuToggle.classList.toggle("NavSide__toggle--active");
      sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    // Kode untuk active item di sidebar (tetap dipertahankan)
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

    // Fungsi untuk tombol kembali (tetap dipertahankan)
    function pindahKeHalamanDaftarSidang() {
      window.location.href = "aDaftarSidang.php"; // Ganti dengan halaman tujuan yang benar
    }
</script>
</body>
</html>
