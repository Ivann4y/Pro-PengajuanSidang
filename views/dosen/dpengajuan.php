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
    <title>Dosen - Pengajuan</title>
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

        
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="sideNav"></div>
        <div class="container-fluid bodyContainer">
            <div class="row">
                <h2 class="bodyHeading">
                    Pengajuan Sidang
                </h2>
            </div><br><br>
            <div class="row">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddDPengajuan">
                        Sidang TA
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="ddDSidangMenu" onclick="switchDPengajuan();">Sidang Semester</a></li>
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
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240033')">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240053')">
                            <td>2</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru" onclick="goToDetail('0920240055')">
                            <td>3</td>
                            <td>0920240055</td>
                            <td>Nur Widya Astuti</td>
                            <td>Pemrograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                    </tbody>
                    <tbody id="dPengajuanSem" style="display: none;">
                       <tr class="isiTabel jadiBiru">
                            <td>1</td>
                            <td>0920240033</td>
                            <td>M. Harris Nur S.</td>
                            <td>Pemograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>2</td>
                            <td>0920240053</td>
                            <td>Nayaka Ivanna</td>
                            <td>Pemograman 2</td>
                            <td>Timotius Victory</td>
                        </tr>
                        <tr class="isiTabel jadiBiru">
                            <td>3</td>
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

function switchDPengajuan() {
    const taTable = document.getElementById('dPengajuanTA');
    const semTable = document.getElementById('dPengajuanSem');
    const dropdownButton = document.getElementById('ddDPengajuan');
    const dropdownMenuItem = document.getElementById('ddDSidangMenu');

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