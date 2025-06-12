//NAUFAL AF

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <link rel="stylesheet" href="../../extra/style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Edit Pengajuan Sidang</title>
</head>

<style>
  body {
    font-family: "Poppins", sans-serif;
  }

  label {
    font-weight: 500;
    margin-bottom: 5px;
  }

  input[type="file"] {
    display: none;
  }

  .form-control,
  .form-select {
    font-family: "Poppins", sans-serif;
    font-size: 16px;
    padding: 12px 15px;
    border-radius: 12px;

  }

  .upload-box {
    background-color: #e9ecef;
    border-radius: 16px;
    padding: 40px 20px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .upload-box:hover {
    background-color: #dee2e6;
  }

  .upload-box.file-selected {
    background-color: #d1e7dd;
    /* Hijau muda */
    border: 2px solid #0f5132;
    color: #0f5132;
  }

  .upload-content {
    min-height: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .btn-kembali {
    background-color: #4B68FB;
    color: white;
    border: none;
    border-radius: 20px;
    padding: 8px 16px;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
    display: inline-flex;
    align-items: center;
    max-width: 200px;
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
    width: 25px;
    height: 25px;
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

  .bintangMerah {
    color: red;
    font-weight: bold;
  }
</style>



<body>
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
      </div>
      <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item ">
          <b></b><b></b>
          <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <b></b><b></b>
          <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="logout.html"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
        </li>
      </ul>
    </div>

    <div class="NavSide__topbar">
      <div class="NavSide__toggle">
        <i class="bi bi-list open"></i>
        <i class="bi bi-x-lg close"></i>
      </div>
      <div class="header-icons">
        <a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
          <i class="bi bi-bell-fill"></i>
        </a>
        <div class="profile-icon">
          <a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
            <i class="bi bi-person-fill fs-5"></i>
          </a>
        </div>
      </div>
    </div>

    <main class="NavSide__main-content" id="mPengajuan">
      <div class="container-fluid">
        <div class="dashboard-header">
          <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
          <div class="header-icons d-none d-md-flex">
            <a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
            <div class="profile-icon">
              <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">

            <h5 class="fw-bold mt-4 mb-3">Tambah Sidang</h5>
            <hr>
          </div>

          <?php
          $judul = $_GET['judul'] ?? '';
          $matkul = $_GET['matkul'] ?? '';
          ?>
          <form action="#" method="post">
            <div class="mb-3">
              <label for="judul" class="form-label">
                <span class="text-danger">* </span>Judul Sidang
              </label>
              <input type="text" class="forM form-control" id="judul" name="judul" value="<?php echo $judul ?>" placeholder="Masukkan Judul Sidang" />
            </div>
            <div class="mb-3">
              <label for="matkul" class="form-label">
                <span class="text-danger">* </span>Mata Kuliah
              </label>
              <select class="forM form-select" id="matkul" name="matkul">
                <option selected disabled>Pilih Mata Kuliah</option>
                <option value="Tugas Akhir" <?php if ($matkul == 'Tugas Akhir') {
                                              echo ' selected';
                                            }
                                            ?>>Tugas Akhir</option>
                <option value="Pemrograman 2" <?php if ($matkul == 'Pemrograman 2') {
                                                echo ' selected';
                                              }
                                              ?>>Pemrograman 2</option>
                <option value="Sistem Operasi" <?php if ($matkul == 'Sistem Operasi') {
                                                  echo ' selected';
                                                }
                                                ?>>Sistem Operasi</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="kelas" class="form-label">
                <span class=text-danger>* </span> Kelas
              </label>
              <select class="forM form-select" id="kelas" name="kelas">
                <option selected disabled>Pilih Kelas</option>
                <option value="A">RPL 1A</option>
                <option value="B">RPL 1B</option>
              </select>
            </div>

            <!-- Upload Dokumen Laporan Sidang -->
            <div class="row">
              <div class="col-md-6 mb-4">
                <div class="p-4 rounded bg-light border text-start">
                  <h6 class="fw-bold text-dark">
                    <span class="text-danger">* </span>Dokumen Laporan Sidang
                  </h6>
                  <div id="laporanSidangForm" action="#" method="POST" enctype="multipart/form-data">
                    <label class="upload-box w-100 mt-3 text-center">
                      <input type="file" id="laporanSidang" name="laporanSidang" accept=".pdf,.docx,.pptx,.zip" hidden />
                      <div class="upload-content">
                        <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z" />
                          <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z" />
                        </svg>
                        <p class="mt-2 text-muted small" id="laporanLabelText">Upload file revisi dengan format pdf, docx, pptx, dan zip</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Upload Dokumen Pendukung Sidang -->
              <div class="col-md-6 mb-4">
                <div class="p-4 rounded bg-light border text-start">
                  <h6 class="fw-bold text-dark">
                    <span class="text-danger">* </span>Dokumen Pendukung Sidang
                  </h6>
                  <div id="dokPendukungForm" action="#" method="POST" enctype="multipart/form-data">
                    <label class="upload-box w-100 mt-3 text-center">
                      <input type="file" id="dokPendukung" name="pendukungSidang" accept=".pdf,.docx,.pptx,.zip" hidden />
                      <div class="upload-content">
                        <svg id="UploadIcon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z" />
                          <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z" />
                        </svg>
                        <p class="mt-2 text-muted small">Upload file revisi dengan format pdf, docx, pptx, dan zip</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div class="bintangMerah">
                <p>* : wajib diisi</p>
              </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                  <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                  </div>
                  <div class="modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin ingin mengajukan sidang?</p>
                    <div class="d-flex justify-content-between px-5">
                      <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                      <button type="submit" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="submitBtn">Lanjutkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>




            <!-- Button Simpan & Kirim, Verifikasi Modal -->
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <button class="btn btn-kembali" onclick="location.href='mPengajuan.php'">
                  <span class="icon-circle">
                    <i class="fa-solid fa-arrow-left"></i>
                  </span>
                  Kembali
                </button>
              </div>

              <!-- Kanan: Tombol Simpan & Kirim -->
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" id="btnSimpan">Simpan</button>
                <button type="button" class="btn-setuju" id="btnOpenModalKonfirmasi">Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar Toggle Logic 
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    if (menuToggle) {
      menuToggle.onclick = function() {
        menuToggle.classList.toggle("NavSide__toggle--active");
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
      };
    }

    // Sidebar Active Item Logic
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    for (let i = 0; i < listItems.length; i++) {
      listItems[i].onclick = function(event) {
        if (!this.classList.contains("NavSide__sidebar-item--active")) {
          for (let j = 0; j < listItems.length; j++) {
            listItems[j].classList.remove("NavSide__sidebar-item--active");
          }
          this.classList.add("NavSide__sidebar-item--active");
        }
      };
    }

    const btnOpenModalKonfirmasi = document.getElementById('btnOpenModalKonfirmasi');
    const modalKonfirmasiEl = document.getElementById('modalKonfirmasi');
    // const modalPeringatanEl = document.getElementById('modalPeringatan');

    const modalKonfirmasi = new bootstrap.Modal(modalKonfirmasiEl);
    // const modalPeringatan = new bootstrap.Modal(modalPeringatanEl);

    btnOpenModalKonfirmasi.addEventListener('click', function() {
      const laporan = document.getElementById('laporanSidang').files.length;
      const pendukung = document.getElementById('dokPendukung').files.length;

      if (laporan === 0 || pendukung === 0) {
        Swal.fire({
          title: 'Dokumen Tidak Boleh Kosong!',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        }).then(() => {
          modal.hide();
        });
      } else {
        modalKonfirmasi.show();
      }
    });

    const btnSimpan = document.getElementById('btnSimpan');

    btnSimpan.addEventListener('click', function() {
      const laporan = document.getElementById('laporanSidang').files.length;
      const pendukung = document.getElementById('dokPendukung').files.length;

      if (laporan === 0 || pendukung === 0) {
        Swal.fire({
          title: 'Dokumen Tidak Boleh Kosong!',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        }).then(() => {
          modal.hide();
        });
      } else {
        history.back();
      }
    });

    const laporanInput = document.getElementById('laporanSidang');
    const uploadIcon = document.getElementById('uploadIcon');
    const laporanBox = laporanInput.closest('.upload-box');

    laporanInput.addEventListener('change', function() {
      if (laporanInput.files.length > 0) {
        uploadIcon.style.display = 'none';
      }
    });

    const pendukungInput = document.getElementById('dokPendukung');
    const pendukungBox = pendukungInput.closest('.upload-box');
    const UploadIcon = document.getElementById('UploadIcon');

    pendukungInput.addEventListener('change', function() {
      if (pendukungInput.files.length > 0) {
        UploadIcon.style.display = 'none';
      }
    });

    function updateUploadBox(input, box) {
      if (input.files.length > 0) {
        const fileName = input.files[0].name;
        box.classList.add('file-selected');
        box.querySelector('.upload-content p').textContent = `File terupload: ${fileName}`;
      } else {
        box.classList.remove('file-selected');
        box.querySelector('.upload-content p').textContent = 'Upload file revisi dengan format pdf, docx, pptx, dan zip';
      }
    }



    laporanInput.addEventListener('change', () => updateUploadBox(laporanInput, laporanBox));
    pendukungInput.addEventListener('change', () => updateUploadBox(pendukungInput, pendukungBox));

    const submitBtn = document.getElementById('submitBtn');

    submitBtn.addEventListener('click', function() {
      Swal.fire({
        title: 'Pengajuan Berhasil Dikirim!',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      }).then((result) => {
        if (result.isConfirmed) {
          history.back();
        }
      });
    });
  </script>
</body>

</html>