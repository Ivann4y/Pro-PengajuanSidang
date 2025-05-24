<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background-color: #f9f9f9;
    }

    .sideNav {
      position: fixed;
      top: 0;
      left: 0;
      width: 15vw;
      height: 100vh;
      background-color: #4538db;
      padding: 2% 1%;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      z-index: 1000;
    }

    .sideNav h4 img {
      width: 100%;
      max-width: 120px;
      margin-bottom: 30px;
    }

    .nav-item {
      margin: 15px 0;
      font-weight: 500;
      cursor: pointer;
      width: 100%;
      padding: 10px 15px;
      text-align: center;
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    .nav-item.active {
      background-color: white;
      color: #4538db;
      font-weight: 600;
    }

    .nav-item:hover {
      background-color: #ffffffcc;
      color: #4538db;
    }

    .bodyContainer {
      margin-left: 15vw;
      padding: 4vh 3vw;
    }

    .bodyHeading {
      font-weight: 600;
      margin-bottom: 20px;
    }

    .welcomeText {
      color: #555;
      font-size: 2rem;
    }

    .dashboardTitle {
      color: #4538db;
      font-size: 1.5rem;
      font-weight: 500;
    }

    .statusCard {
      padding: 20px;
      border-radius: 15px;
      height: 100%;
      transition: 0.3s ease;
      cursor: pointer;
    }

    .statusCard:hover {
      transform: scale(1.05);
    }

    .card-penjadwalan {
      background-color: #4538db;
      color: white;
    }

    .card-pengajuan {
      background-color: #e2e2e2;
      color: #444;
    }

    .statusTitle {
      font-weight: 600;
      margin-bottom: 10px;
    }

    .statusNumber {
      font-size: 2.5rem;
      font-weight: bold;
    }

    .notifBox {
      background-color: #dcdcdc;
      padding: 20px;
      border-radius: 15px;
      position: relative;
    }

    .notifBox h5 {
      font-weight: 600;
      margin-bottom: 15px;
    }

    .notifItem {
      background-color: white;
      padding: 10px 15px;
      margin-bottom: 10px;
      border-radius: 10px;
      position: relative;
    }

    .notifItem .notif-check {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #007bff;
      font-size: 1.2rem;
      cursor: pointer;
      transition: color 0.3s;
    }

    .notifItem .notif-check:hover {
      color: #0056b3;
    }

    .notif-all-check {
      position: absolute;
      top: 15px;
      right: 20px;
      color: #007bff;
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s;
    }

    .notif-all-check:hover {
      color: #0056b3;
    }

    .img-slot {
      width: 100%;
      height: 100%;
      background-color: #e0e0e0;
      border-radius: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 14px;
      color: gray;
      min-height: 250px;
    }

    .profile-icon {
      position: absolute;
      top: 30px;
      right: 30px;
      font-size: 1.8rem;
      color: #444;
      cursor: pointer;
      transition: color 0.3s;
    }

    .profile-icon:hover {
      color: #007bff;
    }

    @media (max-width: 768px) {
      .sideNav {
        position: relative;
        width: 100vw;
        height: auto;
        flex-direction: row;
        justify-content: space-around;
      }
      .bodyContainer {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="sideNav">
    <h4><img src="logo-astratech.png" alt="ASTRAtech Logo"></h4>
    <div class="nav-item active">Beranda</div>
    <div class="nav-item" id="penjadwalanNav">Penjadwalan</div>
    <div class="nav-item">Daftar Sidang</div>
    <div class="nav-item">Keluar</div>
  </div>

  <div class="bodyContainer position-relative">
    <div class="profile-icon">
      <i class="fas fa-user-circle"></i>
    </div>
     <div class="dashboardTitle">Dashboard Admin</div>
    <h2 class="welcomeText">Selamat Datang, Admin!</h2>
   
    <div class="row my-4">
      <div class="col-md-3 mb-3">
        <div class="statusCard card-penjadwalan" id="cardPenjadwalan" onclick="location.href='#penjadwalanNav'">
          <div class="statusTitle">Penjadwalan</div>
          <div class="d-flex align-items-center">
            <div class="statusNumber me-3">4</div>
            <div>Menunggu Dijadwalkan</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="statusCard card-pengajuan">
          <div class="statusTitle">Pengajuan</div>
          <div class="d-flex align-items-center">
            <div class="statusNumber me-3">2</div>
            <div>Menunggu Persetujuan</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <div class="notifBox">
          <h5>Notifikasi</h5>
          <div class="notif-all-check" onclick="markAllRead()">✔</div>
          <div class="notifItem">
            Sidang PRG telah disetujui oleh dosen pembimbing
            <span class="notif-check" onclick="markOneRead(this)">✔</span>
          </div>
          <div class="notifItem">
            Sidang BasDat telah selesai dinilai
            <span class="notif-check" onclick="markOneRead(this)">✔</span>
          </div>
          <div class="notifItem">
            Sidang TA Nayaka Ivana Putra telah selesai dinilai
            <span class="notif-check" onclick="markOneRead(this)">✔</span>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-3">
        <div class="img-slot">
          Slot Gambar/Ilustrasi Admin
        </div>
      </div>
    </div>
  </div>

  <script>
    function markAllRead() {
      document.querySelectorAll('.notifItem').forEach(item => item.remove());
    }

    function markOneRead(el) {
      el.closest('.notifItem').remove();
    }
  </script>
</body>
</html>
