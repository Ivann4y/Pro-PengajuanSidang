<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <title>Dosen - Daftar Sidang</title>
    <style>
        
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

      .sideNav h4 {
      width: 100%;
      max-width: 120px;
      margin-bottom: 30px;
    }

    .sideNav img {
        width: 100%;
        max-width: 150px;
        margin-bottom: 100px;
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

        table {
        border-spacing: 0 10px;
        border-collapse: separate;
        width: 100%;
        }

        thead {
        border-bottom: 2px solid rgb(0, 0, 0) !important;
        }

        thead th {
        padding: 12px 15px;
        text-align: left;
        }

        thead th:nth-child(1) {
        text-align: center;
        width: 5%;
        }

        thead th:nth-child(2) {
        width: 20%;
        }

        thead th:nth-child(3) {
        width: 20%;
        }

        thead th:nth-child(4) {
        width: 20%;
        }

        thead th:nth-child(5) {
        width: 20%;
        }

        .isiTabel td {
        padding: 12px 15px;
        font-family: "Poppins";
        font-weight: 400;
        vertical-align: middle;
        }

        .isiTabel td:nth-child(1) {
        border-radius: 20px 0 0 20px;
        text-align: center;
        }

        .isiTabel td:nth-child(5) {
        border-radius: 0 20px 20px 0;
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
        <img src="../../assets/img/Logo_Astratech_White-8.png" alt="ASTRAtech Logo">
        <div href="dBeranda.php" class="nav-item " id="berandaNav" onclick="location.href='dBeranda.php'">Beranda</div>
        <div href="dPengajuan.php" class="nav-item" id="pengajuanNav" onclick="location.href='dPengajuan.php'">Pengajuan</div>
        <div href="dDaftarSidang.php"class="nav-item active" id="DaftarSidangNav" onclick="location.href='dDaftarSidang'">Daftar Sidang</div>
        <div class="nav-item">Keluar</div>
  </div>

    <div class="container-fluid">
        <div class="container-fluid bodyContainer">
            <div class="row">
                <h2 class="bodyHeading">
                    Daftar Sidang
                </h2>
            </div><br><br>
            <div class="row">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                        Sidang TA
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchDdaftarSidang();">Sidang Semester</a></li>
                    </ul>
                </div>
            </div><br><br>
            <div class="row">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Mata Kuliah</th>
                            <th scope="col">Dosen Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody id="dPengajuanTA">
                        <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>2</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>3</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Tugas Akhir</td>
                            <td>Timotius Victory</td>
                        </tr>
                    </tbody>
                    <tbody id="dPengajuanSem" style="display: none;">
                        <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M.Harris Nur S.</td>
                            <td>Pemograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>2</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Pemograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let isTA = true;

        function switchDdaftarSidang() {
            const taTable = document.getElementById('dPengajuanTA');
            const semTable = document.getElementById('dPengajuanSem');
            const dropdownButton = document.getElementById('ddMSidang');
            const dropdownMenuItem = document.getElementById('ddMSidangMenu');

            if (isTA) {
                taTable.style.display = 'none';
                semTable.style.display = 'table-row-group';
                dropdownButton.textContent = 'Sidang Semester';
                dropdownMenuItem.textContent = 'Sidang TA';
            } else {
                taTable.style.display = 'table-row-group';
                semTable.style.display = 'none';
                dropdownButton.textContent = 'Sidang TA';
                dropdownMenuItem.textContent = 'Sidang Semester';
            }

            isTA = !isTA;
        }
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>