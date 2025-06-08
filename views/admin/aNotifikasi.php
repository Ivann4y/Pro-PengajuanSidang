<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../extra/style.css">
  <title>Admin - Notifikasi</title>
  <style>
    table {
      width: 100%;
      border-spacing: 0 10px;
      border-collapse: separate;
    }

    th {
      border-bottom: black 2px solid;
      border-radius: 0px 0px 0px 0px;
    }

    th,
    td {
      padding: 12px 25px;
      font-family: "Poppins";
      font-weight: 400;
      vertical-align: middle;
    }

    tr.isiTabel {
      background-color: #F1F1F1;
      border-color: #F1F1F1;
      border-radius: 10px;
    }

    #bacabutton {
      background-color: #4FD381;
      /* Green */
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    #tidakbacabutton {
      background-color: #D9D9D9;
      /* Red */
      border-color: #FD7D7D;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    #tidakbacabutton:hover,
    #bacabutton:hover {
      opacity: 0.8;
    }

    #tidakbacabutton:hover {
      background-color: #FD7D7D;
      /* Red */
      border-color: #FD7D7D;
      color: white;
    }

    .modal-header {
      justify-content: center;
      border-bottom: none !important;
    }

    .modal-header .modal-title {
      width: 100%;
      text-align: center;
    }

    .buttonKonfirmasi {
      background-color: #4FD381;
      /* Green */
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    th:nth-child(1) {
      width: 20%;
      text-align: center;
    }

    th:nth-child(5) {
      width: 10%;
      text-align: center;
    }

    td:nth-child(1) {
      width: 20%;
      text-align: center;
      border-radius: 20px 0px 0px 20px;
    }

    th:nth-child(2),
    td:nth-child(2) {
      width: 30%;
      text-align: center;
    }

    th:nth-child(3),
    td:nth-child(3) {
      width: 20%;
      text-align: center;
    }

    th:nth-child(4),
    td:nth-child(4) {
      width: 20%;
      text-align: center;
    }

    td:nth-child(5) {
      width: 10%;
      text-align: center;
      border-radius: 0px 20px 20px 0px;
    }
  </style>
</head>

<body>
   <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aPenjadwalan.php"><span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="logout.html" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>
        
        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <div class="profile-icon">
                    <a href="aProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

      <main class="NavSide__main-content">
        <div class="container-fluid">
          <div class="row">
            <h2 class="text-heading">
              Admin
            </h2>
          </div><br>
          <div class="row align-items-center mb-3">
            <div class="col">
              <b>Notifikasi</b>
            </div>
            
            <div class="col-auto ms-auto">
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMBelumDibaca">
                  Belum Dibaca
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" id="ddMSudahDibaca" onclick="switchMNotifikasi();">Sudah Dibaca</a></li>
                </ul>
              </div>
            </div>

          </div><br>
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th scope="col">Dari</th>
                  <th scope="col">Pesan</th>
                  <th scope="col">Waktu</th>
                  <th scope="col">Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="BelumDibaca">
                <tr class="isiTabel jadiBiru ">
                  <td>Rida Indah Fariani</td>
                  <td>Pengajuan telah disetujui oleh dosen pembimbing</td>
                  <td>20 April 2025, 10:00</td>
                  <td>Belum Dibaca</td>
                  <td><span onclick="bacaModal(this)">✔️</span></td>
                </tr>
              </tbody>
              <tbody id="SudahDibaca" style="display: none;">
                <tr class="isiTabel ">
                  <td>Timotius Victory</td>
                  <td>Pengajuan telah ditolak oleh dosen pembimbing</td>
                  <td>20 Januari 2025, 10:00</td>
                  <td>Sudah Dibaca</td>
                  <td></td>
                </tr>
                <tr class="isiTabel ">
                  <td>Timotius Victory</td>
                  <td>Pengajuan telah disetujui oleh dosen pembimbing</td>
                  <td>21 Januari 2025, 10:00</td>
                  <td>Sudah Dibaca</td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
    </div>
    </main>

         <!-- Modal keluar-->
    <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header mx-auto">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Perhatian!</h1>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="konfirmasiModalnotifikai" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius: 20px;">
          <div class="modal-body text-center">
            <h5 class="fw-bold mb-3" style="color: #3A3A3A;">Perhatian</h5>
            <p class="mb-4" style="color: #3A3A3A;">Apakah anda sudah yakin ingin mengubah status Terbaca?</p>
            <div class="d-flex justify-content-center row-mb-3">
              <div class="col-md-8">
                <button type="button" class="btn btn-outline-danger px-4 fw-semibold" data-bs-dismiss="modal">Batalkan</button>
              </div>
              <div class="col-md-8">
                <button type="button" class="btn btn-success px-4 fw-semibold" onclick="lanjutkanAksi()">Lanjutkan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script type="text/javascript">
    let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

    // Active menu item highlighting
     let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }
    let rowToUpdate = null;

    function bacaModal(spanElem) {
      // Simpan referensi baris (tr) yang akan diubah
      rowToUpdate = spanElem.closest('tr');
      const modal = new bootstrap.Modal(document.getElementById("konfirmasiModalnotifikai"));
      modal.show();
    }

    function lanjutkanAksi() {
      // Cek apakah rowToUpdate sudah di-set

      document.getElementById("bacabutton")
      if (rowToUpdate) {
        // Ubah status pada kolom ke-4 dan hapus tombol span
        rowToUpdate.cells[3].innerText = "Sudah Dibaca";
        rowToUpdate.cells[4].innerHTML = ""; // Hapus span
        // Hapus class jadiBiru
        rowToUpdate.classList.remove("jadiBiru");
        // Pindahkan row ke tbody SudahDibaca
        document.getElementById("SudahDibaca").appendChild(rowToUpdate);
        rowToUpdate = null;
      }
      // Tutup modal
      bootstrap.Modal.getInstance(document.getElementById("konfirmasiModalnotifikai")).hide();
    };

    document.getElementById("tidakbacabutton").onclick = function() {
      // Tutup modal tanpa mengubah apapun
      bootstrap.Modal.getInstance(document.getElementById("konfirmasiModalnotifikai")).hide();
    };

    function switchMNotifikasi() {
      const mnotif = document.getElementById("BelumDibaca");
      const mnotif2 = document.getElementById("SudahDibaca");
      const mnotifButton = document.getElementById("ddMBelumDibaca");
      const mnotifmenu = document.getElementById("ddMSudahDibaca");
      if (mnotif && mnotif2) {
        if (
          mnotif.style.display === "none" ||
          getComputedStyle(mnotif).display === "none"
        ) {
          mnotif.style.display = "table-row-group";
          mnotif2.style.display = "none";
          mnotifButton.innerText = "Belum Dibaca";
          mnotifmenu.innerText = "Sudah Dibaca";
        } else {
          mnotif.style.display = "none";
          mnotif2.style.display = "table-row-group";
          mnotifButton.innerText = "Sudah Dibaca";
          mnotifmenu.innerText = "Belum Dibaca";
        }
      }
    }
  </script>
</body>

</html>