<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <title>Edit Pengajuan Sidang</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
    }
    .form-section {
      background-color: #f5f5f5;
      padding: 30px;
      border-radius: 20px;
    }
    label {
      font-weight: 500;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="sideNav"></div>
    <div class="container-fluid bodyContainer">
      <div class="row">
        <h2 class="bodyHeading"><b>Nayaka Ivana Putra (Mahasiswa)</b></h2>
      </div><br>

      <div class="row">
        <div class="col-md-8">
          <form class="form-section" action="#" method="post">
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Sidang</label>
              <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan Judul Sidang">
            </div>
            <div class="mb-3">
              <label for="matkul" class="form-label">Mata Kuliah</label>
              <select class="form-select" id="matkul" name="matkul">
                <option selected disabled>Pilih Mata Kuliah</option>
                <option value="Tugas Akhir">Tugas Akhir</option>
                <option value="Pemrograman 2">Pemrograman 2</option>
                <option value="Sistem Operasi">Sistem Operasi</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="kelas" class="form-label">Kelas</label>
              <select class="form-select" id="kelas" name="kelas">
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
  <style>
    label[for] {
      cursor: pointer;
    }

    input[type="file"] {
      display: none;
    }
  </style>

            <a href="javascript:history.back()" class="btn btn-primary ms-2">Kirim</a>
            <button type="submit" class="btn btn-secondary">Simpan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
