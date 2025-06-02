<?php
  $judul = $_GET['judul'] ?? '';
  $matkul = $_GET['matkul'] ?? '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <title>Edit Pengajuan Sidang</title>
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

    .form-control, .form-select {
      font-family: "Poppins", sans-serif;
      font-size: 16px;
      padding: 12px 15px;
      border-radius: 12px;

    .  
}

  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="sideNav"></div>
    <div class="container-fluid bodyContainer">
      <div class="row">
        <div class="col-12">
        <h2 class="text-heading"><b>Nayaka Ivana Putra (Mahasiswa)</b></h2>
        <h5 class="fw-bold mt-4 mb-3">Tambah Sidang Semester</h5>
        <hr>
      </div>

      <form action="#" method="post">
        <div class="mb-3">
          <label for="judul" class="form-label">Judul Sidang</label>
          <input type="text" class="forM form-control" id="judul" name="judul" value="<?php echo $judul?>" placeholder="Masukkan Judul Sidang" />
        </div>
        <div class="mb-3">
          <label for="matkul" class="form-label">Mata Kuliah</label>
          <select class="forM form-select" id="matkul" name="matkul">
            <option selected disabled>Pilih Mata Kuliah</option>
            <option value="Tugas Akhir"<?php if ($matkul == 'Tugas Akhir') {
                echo ' selected';
              }
              ?> >Tugas Akhir</option>
            <option value="Pemrograman 2" <?php if ($matkul == 'Pemrograman 2') {
                echo ' selected';
              }
              ?>
              >Pemrograman 2</option>
            <option value="Sistem Operasi"<?php if ($matkul == 'Sistem Operasi') {
                echo ' selected';
              }
              ?>
              >Sistem Operasi</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="kelas" class="form-label">Kelas</label>
          <select class="forM form-select" id="kelas" name="kelas">
            <option selected disabled>Pilih Kelas</option>
            <option value="A">RPL 1A</option>
            <option value="B">RPL 1B</option>
          </select>
        </div>

 <div class="row">
  <!-- Laporan Sidang -->
  <div class="col-md-6 mb-4">
    <div class="p-4 rounded bg-light border text-start">
      <h6 class="fw-bold text-dark">Dokumen Laporan Sidang</h6>
      <form id="laporanSidangForm" action="#" method="POST" enctype="multipart/form-data">
        <label for="laporanSidang" class="upload-box w-100 mt-3 text-center">
          <div class="upload-content">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
              <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z"/>
              <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            <p class="mt-2 text-muted small">Upload file revisi dengan format pdf, docx, pptx, dan zip</p>
          </div>
        </label>
        <input type="file" id="laporanSidang" name="laporanSidang" accept=".pdf,.docx,.pptx,.zip" hidden />
      </form>
    </div>
  </div>

  <!-- Dokumen Pendukung -->
  <div class="col-md-6 mb-4">
    <div class="p-4 rounded bg-light border text-start">
      <h6 class="fw-bold text-dark">Dokumen Pendukung Sidang</h6>
      <form id="dokPendukungForm" action="#" method="POST" enctype="multipart/form-data">
        <label for="dokPendukung" class="upload-box w-100 mt-3 text-center">
          <div class="upload-content">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
              <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z"/>
              <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            <p class="mt-2 text-muted small">Upload file revisi dengan format pdf, docx, pptx, dan zip</p>
          </div>
        </label>
        <input type="file" id="dokPendukung" name="dokPendukung" accept=".pdf,.docx,.pptx,.zip" hidden />
      </form>
    </div>
  </div>
</div>

<style>
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
</style>




        <div class="d-flex justify-content-end gap-2 mt-3">
          <button type="button" class="btn btn-secondary" onclick="history.back()">Simpan</button>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Kirim</button>
       <div class="modal fade" id="staticBackdrop"data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Perhatian</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah anda sudah yakin ingin mengajukan sidang?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
        <button type="button" class="btn btn-primary">Lanjutkan</button>
      </div>
    </div>
  </div>
</div>              
      </form>
    </div>
  </div>
</body>
</html>
