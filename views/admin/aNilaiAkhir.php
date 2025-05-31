<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Sidang</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: #3D5AFE;
      color: white;
      height: 100vh;
      display: flex;
      flex-direction: column;
      padding-top: 30px;
      box-sizing: border-box;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 50px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar ul li {
      padding: 15px 20px;
      cursor: pointer;
      transition: 0.3s;
    }

    .sidebar ul li.active {
      background-color: white;
      color: #3D5AFE;
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      border-top-right-radius: 50px;
      border-bottom-right-radius: 50px;
    }

    .sidebar ul li:hover:not(.active) {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .content {
      flex: 1;
      padding: 40px;
    }

    .title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .nilai-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #f0f0f0;
      padding: 40px;
      border-radius: 20px;
      margin-bottom: 30px;
    }

    .nilai-huruf {
      font-size: 80px;
      font-weight: bold;
      background-color: white;
      padding: 50px;
      border-radius: 20px;
      width: 200px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .gambar {
      width: 200px;
      height: 200px;
      background-color: #ddd;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
    }

    .detail-penilaian,
    .catatan {
      background-color: white;
      border-radius: 20px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .kembali-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #3D5AFE;
      color: white;
      border: none;
      border-radius: 20px;
      text-decoration: none;
    }

  </style>
</head>
<body>

  <div class="sidebar">
    <h2>ASTRAtech</h2>
    <ul>
      <li>Detail Sidang</li>
      <li>Evaluasi</li>
      <li class="active">Nilai Akhir</li>
    </ul>
  </div>

  <div class="content">
    <div class="title">Detail Sidang - Pemrograman 2</div>

    <div class="nilai-box">
        <div></div>
      <div class="nilai-huruf">--</div>
      <div class="gambar">[Gambar orang]</div>
    </div>

    <div class="detail-penilaian">
      <div></div>
    </div>

    <div class="catatan">
        <div></div>
    </div>

    <a href="#" class="kembali-btn">← Kembali</a>
  </div>

</body>
</html>
