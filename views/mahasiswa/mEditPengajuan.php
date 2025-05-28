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
          <div class="col-md-6 mb-4">
            <div class="p-4 bg-light text-center border rounded">
              <h6><b>Dokumen Laporan Sidang</b></h6>
              <label for="laporanSidang" class="d-block my-3">
                <i class="bi bi-upload" style="font-size: 2rem;"></i><br>
                Unggah berkas revisi dengan format pdf, docx, pptx, dan zip
              </label>
              <input type="file" id="laporanSidang" name="laporanSidang" class="form-control" hidden />
            </div>
          </div>

          <div class="col-md-6 mb-4">
            <div class="p-4 bg-light text-center border rounded">
              <h6><b>Dokumen Pendukung Sidang</b></h6>
              <label for="dokPendukung" class="d-block my-3">
                <i class="bi bi-upload" style="font-size: 2rem;"></i><br>
                Unggah berkas revisi dengan format pdf, docx, pptx, dan zip
              </label>
              <input type="file" id="dokPendukung" name="dokPendukung" class="form-control" hidden />
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
          <button type="button" class="btn btn-secondary" onclick="history.back()">Simpan</button>
          <a href="javascript:history.back()" class="btn btn-primary">Kirim</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
