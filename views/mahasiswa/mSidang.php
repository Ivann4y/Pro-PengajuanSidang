<?php

require "../../koneksi.php";

$query = "SELECT s.id_sidang, s.judul, s.jenis_sidang, m.nama_matkul, MIN(d.nama_dosen) AS dosen FROM Sidang s, MataKuliah m, Dosen d, Detail_Sidang ds WHERE ds.id_sidang = s.id_sidang AND ds.id_matkul = m.id_matkul AND ds.nomor_dosen = d.nomor_dosen GROUP BY s.id_sidang, s.judul, s.jenis_sidang, m.nama_matkul ORDER BY s.id_sidang";
$result = sqlsrv_query($conn, $query);

if ($result === false) {
    echo "Terjadi kesalahan saat mengeksekusi query:<br>";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
            echo "Code: " . $error['code'] . "<br>";
            echo "Message: " . $error['message'] . "<br>";
        }
    }
    exit();
}

// Initialize arrays to store data
$dataTA = array();
$dataSemester = array();

// Process the results
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    if ($row['jenis_sidang'] == 0) {
        $dataTA[] = $row;
    } else {
        $dataSemester[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa - Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/msidang.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
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
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>
        
        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </div>

        <main class="NavSide__main-content" id="mSidang">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="dashboard-header">
                    <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
                    <div class="header-icons d-none d-md-flex">
                        <a href="aNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                        <div class="profile-icon">
                            <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                        </div>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 d-flex align-items-center"> 
                        <label for="ddMsidang" class="fw-semibold mb-0">Filter: </label>
                        <div class="dropdown ms-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                Semua
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="switchMSidang('all')">Semua</a></li>
                                <li><a class="dropdown-item" href="#" onclick="switchMSidang('ta')">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="#" onclick="switchMSidang('semester')">Sidang Semester</a></li>
                            </ul>
                        </div>
                    </div>
                </div><br><br>
                <div class="row table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen Pembimbing</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mSidangAll">
                            <?php foreach ($dataTA as $row): ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $row['id_sidang'] ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td>
                                        <button class="detail-btn" onclick="location.href='mdetailSidangTA.php?id=<?= $row['id_sidang'] ?>';">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        <tbody>
                            <?php foreach ($dataSemester as $row): ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $row['id_sidang'] ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td>
                                        <button class="detail-btn" onclick="location.href='mdetailSidangTA.php?id=<?= $row['id_sidang'] ?>';">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tbody id="mSidangTA" style="display: none;">
                            <?php foreach ($dataTA as $row): ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $row['id_sidang'] ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td>
                                        <button class="detail-btn" onclick="location.href='mdetailSidangTA.php?id=<?= $row['id_sidang'] ?>';">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tbody id="mSidangSem" style="display: none;">
                            <?php foreach ($dataSemester as $row): ?>
                                <tr class="isiTabel jadiBiru">
                                    <td><?= $row['id_sidang'] ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                                    <td><?= htmlspecialchars($row['dosen']) ?></td>
                                    <td>
                                        <button class="detail-btn" onclick="location.href='mdetailSidangTA.php?id=<?= $row['id_sidang'] ?>';">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="pagination-controls"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal keluar-->
    <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color: rgb(67, 54, 240);">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Logic 
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        if (menuToggle) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        let currentData = [];
        let currentPage = 1;
        const rowsPerPage = 10;
        const dataTA = <?php echo json_encode($dataTA); ?>;
        const dataSemester = <?php echo json_encode($dataSemester); ?>;

        function displayPage(page) {
            currentPage = page;
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageData = currentData.slice(start, end);
            
            const allTable = document.getElementById('mSidangAll');
            const taTable = document.getElementById('mSidangTA');
            const semTable = document.getElementById('mSidangSem');
            
            allTable.innerHTML = '';
            taTable.innerHTML = '';
            semTable.innerHTML = '';
            
            const isTA = currentData === dataTA;
            const isSemester = currentData === dataSemester;
            
            pageData.forEach((row) => {
                const tr = document.createElement('tr');
                tr.className = 'isiTabel jadiBiru';
                tr.innerHTML = `
                    <td>${row.id_sidang}</td>
                    <td>${row.judul}</td>
                    <td>${row.nama_matkul}</td>
                    <td>${row.dosen}</td>
                    <td>
                        <button class="detail-btn" onclick="location.href='mdetailSidangTA.php?id=${row.id_sidang}';">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                `;
                
                if (isTA) {
                    taTable.appendChild(tr);
                } else if (isSemester) {
                    semTable.appendChild(tr);
                } else {
                    allTable.appendChild(tr);
                }
            });
            
            if (isTA) {
                allTable.style.display = 'none';
                taTable.style.display = '';
                semTable.style.display = 'none';
            } else if (isSemester) {
                allTable.style.display = 'none';
                taTable.style.display = 'none';
                semTable.style.display = '';
            } else {
                allTable.style.display = '';
                taTable.style.display = 'none';
                semTable.style.display = 'none';
            }
            
            updatePaginationButtons();
        }

        function setupPagination() {
            const paginationControls = document.getElementById('pagination-controls');
            paginationControls.innerHTML = '';
            const pageCount = Math.ceil(currentData.length / rowsPerPage);
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displayPage(currentPage - 1);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    displayPage(i);
                });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < pageCount) displayPage(currentPage + 1);
            });
            paginationControls.appendChild(nextButton);

            updatePaginationButtons();
        }

        function updatePaginationButtons() {
            const pageCount = Math.ceil(currentData.length / rowsPerPage);
            const pageItems = document.querySelectorAll('#pagination-controls .page-item');
            if (pageItems.length === 0) return;

            pageItems.forEach((item, index) => {
                item.classList.remove('active', 'disabled');
                if (index === 0) {
                    if (currentPage === 1) item.classList.add('disabled');
                } else if (index === pageItems.length - 1) {
                    if (currentPage === pageCount) item.classList.add('disabled');
                } else {
                    if (index === currentPage) {
                        item.classList.add('active');
                    }
                }
            });
        }

        window.switchMSidang = function(type) {
            const btn = document.getElementById("ddMSidang");
            
            if (type === 'ta') {
                currentData = dataTA;
                btn.innerText = "Sidang TA";
            } else if (type === 'semester') {
                currentData = dataSemester;
                btn.innerText = "Sidang Semester";
            } else {
                currentData = [...dataTA, ...dataSemester];
                btn.innerText = "Semua";
            }

            currentPage = 1;
            setupPagination();
            displayPage(1);
        };

        currentData = [...dataTA, ...dataSemester];
        setupPagination();
        displayPage(1);
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>