<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DetailSidang-Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
      body {
        margin : 0;
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
            color: #3970e6;
            font-weight: 600;
        }
        
       .nav-item:hover {
            background-color: #ffffffcc;
            color: #4538db;
        }
        
        .bodyContainer {
            margin-left: 15vw; /* Adjust based on sideNav width */
            padding: 20px;
        }
    
        .status-badge {
        background-color: #4cfaab;
        color: black;
        border-radius: 20px;
        padding: 10px 20px;
        display: inline-block;
        font-size: 14px;
        margin-bottom: 20px;
        box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
      }

      .info-card {
        position: relative;
        background:rgb(224, 224, 224);
        border-radius: 20px;
        box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
        padding: 25px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 20px;
        overflow: hidden;
        transition: background-color 0.4s ease;
      }

      .info-card::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 100%;
        background-color: #3970e6;
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
        transition: width 0.4s ease;
        z-index: 0;
      }

      .info-card:hover::after {
        width: 100%;
        border-radius: 20px;
      }

      .info-card .section {
        flex: 0 0 48%;
        margin-bottom: 15px;
        z-index: 1;
        color: #333;
        transition: color 0.4s ease;
      }

      .info-card:hover .section {
        color: white;
      }

      .info-card .section i {
        margin-right: 8px;
        color: #4538db;
        transition: color 0.4s ease;
      }

      .info-card:hover .section i {
        color: white;
      }


      .h2 {
        font-size: 30px;
        font-weight: 600;
        
        
      }

      .btn-ubah {
        background-color: #ff5f5f;
        color: white;
        border: none;
        border-radius: 25px;
        margin-bottom: 10px;
        padding: 15px 35px;
        cursor: pointer;
        font-size: 16px;
        box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
        position:;

      }

      .btn-ubah:hover {
        background-color:rgb(255, 255, 255);
        border: 3px solid #ff5f5f;
        color : #ff5f5f;
        transition: background-color 0.4s ease, border 0.4s ease, color 0.4s ease;
        
        
      }

      .btn-kembali {
        background-color: #3970e6;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 10px 25px;
        cursor: pointer;
        font-size: 16px;
        box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
        
      }

  </style>
</head>
<body>
  <div class="sideNav">
    <h4><img src="logo-astratech.png" alt="ASTRAtech"></h4>
    <div class="nav-item active">Detail Sidang</div>
    <div class="nav-item">Evaluasi</div>
    <div class="nav-item">Nilai Akhir</div>
  </div>
  
  <div class="bodyContainer">
    <h2>Detail Sidang -Sistem Pengajuan Sidang</h2>
    <div class="status-badge">Status Pengajuan : Disetujui</div>

  <div class="info-card">
    <div class="section">
        <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br>Basis Data 1</p>
        <p><i class="fa-solid fa-user"></i><strong>Dosen Pembimbing</strong><br>Dr. Rida Indah Fariani, S.Kom, M.Kom</p>
        <p><i class="fa-solid fa-users"></i><strong>Dosen Penguji</strong><br>Timotius Victory, S.Kom, M.Kom<br>Ning Ratwasturi, S.Kom, M.Kom</p>
     </div>
     <div class="section">
        <p><i class="fa-solid fa-door-open"></i><strong>Ruangan</strong><br>CB101 - RPL 1B</p>
        <p><i class="fa-solid fa-calendar-days"></i><strong>Tanggal</strong><br>Selasa, 22 April 2025</p>
        <p><i class="fa-solid fa-clock"></i><strong>Jam</strong><br>09.00 - 10.00</p>
    </div>
  </div>
  
    <h5>Aksi</h5>
    
      <button class="btn-ubah">Ubah Jadwal Sidang</button>
      <br>
      <button class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
    
</body>
</html>