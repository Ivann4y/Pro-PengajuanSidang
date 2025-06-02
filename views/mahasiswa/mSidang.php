<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Mahasiswa - Sidang</title>
    <style>
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
        width: 30%;
        }

        thead th:nth-child(3) {
        width: 20%;
        }

        thead th:nth-child(4) {
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

        .isiTabel td:nth-child(4) {
        border-radius: 0 20px 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div id="NavSide">
      <div id="main-sidebar" class="NavSide__sidebar">
        <div class="NavSide__sidebar-brand">
          <img src="WhiteAstra.png" alt="Astra Logo" />
        </div>
        <ul class="NavSide__sidebar-nav">
          <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
            <b></b>
            <b></b>
            <a href="#">
              <span class="NavSide__sidebar-title fw-semibold">Beranda</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
            <b></b>
            <b></b>
            <a href="#">
              <span class="NavSide__sidebar-title fw-semibold">Pengajuan</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
            <b></b>
            <b></b>
            <a href="#">
              <span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
            <b></b>
            <b></b>
            <a href="#">
              <span class="NavSide__sidebar-title fw-semibold">Keluar</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="NavSide__toggle">
        <i class="bi bi-list open"></i>
        <i class="bi bi-x-lg close"></i>
      </div>

      <main class="NavSide__main-content">
        <div class="container-fluid">
            <div class="row">
                <h2 class="text-heading">
                    Nayaka Ivana Putra (Mahasiswa)
                </h2>
            </div><br>
            <div class="row">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                        Sidang TA
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchMSidang();">Sidang Semester</a></li>
                    </ul>
                </div>
            </div><br>
            <div class="row table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Mata Kuliah</th>
                            <th scope="col">Dosen Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody id="mSidangTA">
                        <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>Sistem Pengajuan Sidang</td>
                            <td>Tugas Akhir</td>
                            <td>Rida Indah Fariani</td>
                        </tr>
                    </tbody>
                    <tbody id="mSidangSem" style="display: none;">
                        <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>Implementasi Sistem Sidang</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>2</td>
                            <td>Deployment Sistem Sidang</td>
                            <td>Sistem Operasi</td>
                            <td>Suhendra</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
      </main>
    </div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar"); // More direct access

      // Toggle sidebar visibility
      menuToggle.onclick = function () {
        menuToggle.classList.toggle("NavSide__toggle--active");
        sidebar.classList.toggle("NavSide__sidebar--active-mobile"); // Specific class for mobile active state
      };

      // Active menu item highlighting
      let listItems = document.querySelectorAll(".NavSide__sidebar-item");
      for (let i = 0; i < listItems.length; i++) {
        listItems[i].onclick = function () {
          // Remove active class from all items
          for (let j = 0; j < listItems.length; j++) {
            listItems[j].classList.remove("NavSide__sidebar-item--active");
          }
          // Add active class to the clicked item
          this.classList.add("NavSide__sidebar-item--active");
        };
      }
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>