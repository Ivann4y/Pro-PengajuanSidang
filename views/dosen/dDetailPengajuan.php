<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <title>Detail Pengajuan</title>
  <style>
    .btn-circle {
      border-radius: 12px;
      padding: 6px 24px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-danger.btn-circle {
      background-color: #dc3545;
      color: white;
      border: 2px solid #dc3545;
    }

    .btn-danger.btn-circle:hover {
      background-color: transparent;
      color: #dc3545;
      border: 2px solid #dc3545;
    }

    .btn-success.btn-circle {
      background-color: #198754;
      color: white;
      border: 2px solid #198754;
    }

    .btn-success.btn-circle:hover {
      background-color: transparent;
      color: #198754;
      border: 2px solid #198754;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 24px;
    }
    .btn-circle {
      border-radius: 12px;
      padding: 6px 24px;
      font-weight: 500;
    }
    .file-pill {
      background: #f4f6fa;
      border: 2px solid #2f3a8f;
      border-radius: 30px;
      padding: 6px 12px;
      margin-right: 10px;
      display: inline-flex;
      align-items: center;
    }
    .file-pill i {
      margin-right: 6px;
    }
  </style>
</head>
<body class="p-4">
  <h3 class="mb-4">Detail Pengajuan</h3>

  <div class="card mb-3">
    <h5 class="fw-semibold">Informasi Pengajuan</h5>
    <div class="row mt-2">
      <div class="col-md-6">
        <p class="mb-1">Nama Mahasiswa</p>
        <p class="fw-bold">M. Haaris Nur Salim</p>
        <p class="mb-1">Nomor Induk Mahasiswa</p>
        <p class="fw-bold">0920240033</p>
      </div>
      <div class="col-md-6">
        <p class="mb-1">Mata Kuliah</p>
        <p class="fw-bold">Pemrograman 2</p>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <h5 class="fw-semibold">Dokumen Sidang</h5>
    <div class="mt-2">
      <a class="file-pill text-decoration-none text-dark" href="#" download>
        <i class="bi bi-file-earmark-pdf"></i> berkas_laporan_kel-1.pdf
      </a>
      <a class="file-pill text-decoration-none text-dark" href="#" download>
        <i class="bi bi-file-earmark-zip"></i> dokumen_pendukung_kel-1.zip
      </a>
    </div>
  </div>

  <div class="d-flex justify-content-between">
    <a href="dpengajuan.php" class="btn btn-primary btn-circle">Kembali</a>
    <div>
     <button class="btn btn-danger btn-circle me-2" onclick="showModal('Ditolak')">Tolak</button>
    <button class="btn btn-success btn-circle" onclick="showModal('Disetujui')">Setujui</button>
    </div>
  </div>

  <!-- Modal Notifikasi -->
  <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" width="80" class="mx-auto mb-3" alt="Check Icon">
        <h5 class="modal-title fw-bold" id="notifModalLabel"></h5>
        <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>

  <script>
    function showModal(status) {
      const modalLabel = document.getElementById('notifModalLabel');
      modalLabel.innerText = `Sidang berhasil ${status.toLowerCase()}`;
      const modal = new bootstrap.Modal(document.getElementById('notifModal'));
      modal.show();
    }
  </script>
</body>
</html>
