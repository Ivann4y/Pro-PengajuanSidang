<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <title>Mahasiswa - Pengajuan</title>
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

        thead th:nth-child(5) {
        width: 5%;
        text-align: center;
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
        text-align: center;
        }
  </style>
</head>
<body>
<<<<<<< HEAD
    <div class="container-fluid">
        <div class="sideNav"></div>
        <div class="container-fluid bodyContainer">
            <div class="row">
              <h2 class="bodyHeading">
                <b>Nayaka Ivana Putra (Mahasiswa)</b>
              </h2>
            </div><br><br>
            <div class="row">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                        Sidang TA
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchMSidang();">Sidang Semester</a></li>
                    </ul>
                </div>
            </div><br><br>
            <div class="row">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Mata Kuliah</th>
                            <th scope="col">Dosen Pembimbing</th>
                            <th scope="col" class="text-center">Aksi</th>
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
=======
  <div class="container-fluid">
    <div class="sideNav"></div>
    <div class="container-fluid bodyContainer">
      <div class="row">
        <div class="col-12">
          <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
        </div>
      </div><br>

      <div class="row">
        <div class="col-12 col-md-6">
          <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
              Sidang TA
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" id="ddMSidangMenu" onclick="switchMSidang();">Sidang Semester</a></li>
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
                  <th scope="col">No</th>
                  <th scope="col">Judul</th>
                  <th scope="col">Mata Kuliah</th>
                  <th scope="col">Dosen Pembimbing</th>
                  <th scope="col" class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody id="mSidangTA"></tbody>
              <tbody id="mSidangSem" style="display: none;"></tbody>
            </table>
          </div>
        </div>
      </div>

      <button class="btn btn-primary tambah-sidang-btn" onclick="tambahData()">+ Tambah Sidang</button>
>>>>>>> 1d9cc30956814b28750fa349ca1e82b5677c5b4d
    </div>

  <script>
    const dataTA = [
      {
        judul: "Sistem Pengajuan Sidang",
        matkul: "Tugas Akhir",
        dosen: "Rida Indah Fariani"
      }
    ];

    const dataSemester = [
      {
        judul: "Implementasi Sistem Sidang",
        matkul: "Pemrograman 2",
        dosen: "Timotius Victory"
      },
      {
        judul: "Deployment Sistem Sidang",
        matkul: "Sistem Operasi",
        dosen: "Suhendra"
      }
    ];

    function renderTabel(id, data) {
      const tbody = document.getElementById(id);
      tbody.innerHTML = "";
      data.forEach((item, index) => {
        tbody.innerHTML += `
          <tr class="isiTabel jadiBiru">
            <td>${index + 1}</td>
            <td>${item.judul}</td>
            <td>${item.matkul}</td>
            <td>${item.dosen}</td>
            <td class="text-center">
              <button class="btn btn-link p-0" type="button" title="Edit" onclick="editData(${index}, '${id}', '${item.judul}', '${item.matkul}')">
                <i class="bi bi-pencil-square"></i>
              </button>
            </td>
          </tr>
        `;
      });
    }

<<<<<<< HEAD
    function editData(index, jenis) {
    window.location.href = `mEditPengajuan.php?index=${index}&jenis=${jenis}`;
=======
    function editData(index, jenis, judul, matkul) {
      window.location.href = `mEditPengajuan.php?index=${index}&jenis=${jenis}&judul=${judul}&matkul=${matkul}`;
    }

    function tambahData() {
      window.location.href = `mEditPengajuan.php`;
>>>>>>> 1d9cc30956814b28750fa349ca1e82b5677c5b4d
    }


    function switchMSidang() {
      const ta = document.getElementById("mSidangTA");
      const sem = document.getElementById("mSidangSem");
      const btn = document.getElementById("ddMSidang");

      if (ta.style.display !== "none") {
        ta.style.display = "none";
        sem.style.display = "";
        btn.innerText = "Sidang Semester";
      } else {
        ta.style.display = "";
        sem.style.display = "none";
        btn.innerText = "Sidang TA";
      }
    }

    window.onload = () => {
      renderTabel("mSidangTA", dataTA);
      renderTabel("mSidangSem", dataSemester);
    };
  </script>
</body>
</html>