<?php
// Letakkan ini di baris paling atas file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path ke root directory. Untuk file di dalam /views/admin/, path ini sudah benar.
$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. PERUBAHAN: Cek jika role pengguna BUKAN 'admin'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    // Arahkan ke halaman login utama di root
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

require "../../koneksi/koneksiAndrew.php";

// echo "Koneksi Berhasil!"; // Comment this out for normal use
$selectedTipe = $_GET['tipe'] ?? 'semua';
$selectedStatus = $_GET['status'] ?? 'semua';

$query = "SELECT 
    s.id_sidang AS id,
    m.nim,
    m.nama_mhs AS nama,
    m.prodi,
    s.judul AS judulSidang,
    mk.nama_matkul AS mataKuliah,
    CASE 
        WHEN s.jenis_sidang = 0x00 THEN 'TA'
        WHEN s.jenis_sidang = 0x01 THEN 'Semester'
    END AS tipeSidang,
    CASE 
        WHEN s.status_ajuan = 0x01 THEN 1
        ELSE 0
    END AS statusPersetujuan,
    d_pembimbing.nama_dosen AS pembimbing,
    COALESCE(d_pengampu.dosen_list, '') AS dosenPengampuList
    FROM Sidang s
    INNER JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
    INNER JOIN Mahasiswa m ON km.nim = m.nim

    LEFT JOIN Bimbingan b ON s.id_kelompok = b.id_kelompok AND s.jenis_sidang = 0x00
    LEFT JOIN Dosen d_pembimbing ON b.nomor_dosen = d_pembimbing.nomor_dosen

    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang AND s.jenis_sidang = 0x01
    LEFT JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul

    LEFT JOIN (
        SELECT 
            pk.id_matkul, 
            STRING_AGG(d.nama_dosen, ', ') AS dosen_list
        FROM Pengampu_Kelas pk
        INNER JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
        GROUP BY pk.id_matkul
    ) d_pengampu ON ds.id_matkul = d_pengampu.id_matkul";

$result = sqlsrv_query($conn, $query);
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

$data = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    // Convert dosen list to array
    $row['dosenPengampu'] = !empty($row['dosenPengampuList']) ? 
        explode(', ', $row['dosenPengampuList']) : [];
    
    // Convert status to boolean
    $row['statusPersetujuan'] = (bool)$row['statusPersetujuan'];
    
    $data[] = $row;
}

// New flexible filtering logic
$filteredData = array_filter($data, function($entry) use ($selectedTipe, $selectedStatus) {
    // Check Tipe: if 'semua', it's always a match. Otherwise, check the type.
    $tipeMatch = ($selectedTipe == 'semua') ? true : (isset($entry['tipeSidang']) && $entry['tipeSidang'] == $selectedTipe);

    // Check Status: if 'semua', it's always a match. Otherwise, check the boolean status.
    $statusMatch = true;
    if ($selectedStatus == 'disetujui') {
        $statusMatch = (isset($entry['statusPersetujuan']) && $entry['statusPersetujuan'] === true);
    } elseif ($selectedStatus == 'belum') {
        $statusMatch = (isset($entry['statusPersetujuan']) && $entry['statusPersetujuan'] === false);
    }

    return $tipeMatch && $statusMatch;
});


// 2. --- VARIABLES FOR BUTTON TEXT ---
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'TA') {
    $tipeButtonText = 'Sidang TA';
} elseif ($selectedTipe == 'Semester') {
    $tipeButtonText = 'Sidang Semester';
}

$statusButtonText = 'Semua Status';
if ($selectedStatus == 'disetujui') {
    $statusButtonText = 'Disetujui';
} elseif ($selectedStatus == 'belum') {
    $statusButtonText = 'Belum Disetujui';
}

// Determine dynamic header text based on Tipe filter
$dynamicHeaderText = 'Judul/Mata Kuliah';
if ($selectedTipe == 'TA') {
    $dynamicHeaderText = 'Judul Sidang';
} elseif ($selectedTipe == 'Semester') {
    $dynamicHeaderText = 'Mata Kuliah';
}

$dynamicDosenHeaderText = 'Pembimbing/Dosen';
if ($selectedTipe == 'TA') {
    $dynamicDosenHeaderText = 'Pembimbing';
} elseif ($selectedTipe == 'Semester') {
    $dynamicDosenHeaderText = 'Dosen Pengampu';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Penjadwalan Sidang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/css/aPenjadwalan.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
        <div class="NavSide__sidebar-brand">
            <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
        </div>
        <ul class="NavSide__sidebar-nav">
            <li class="NavSide__sidebar-item">
                <b></b><b></b><a href="aBeranda.php"><span class="fw-semibold">Beranda</span></a>
            </li>
            <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                <b></b><b></b><a href="#"><span class="fw-semibold">Penjadwalan</span></a>
            </li>
            <li class="NavSide__sidebar-item">
                <b></b><b></b><a href="aDaftarSidang.php"><span class="fw-semibold">Daftar Sidang</span></a>
            </li>
            <li class="NavSide__sidebar-item">
                <b></b><b></b>
                <a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="fw-semibold">Keluar</span></a>
            </li>
        </ul>
    </div>

    <div class="NavSide__topbar">
        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>
        <div id="mobile-icons-container"></div>
    </div>

    <main class="NavSide__main-content">
        <div class="main-header">
            <div class="header-left-panel">
                <h1 class="main-title">Penjadwalan Sidang</h1>
                <div class="filter-container">
                    <span class="filter-label fw-semibold">Filter:</span>
                    <div class="dropdown me-2">
                       <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($tipeButtonText) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=semua&status=<?= htmlspecialchars($selectedStatus) ?>">Semua Tipe</a></li>
                            <li><a class="dropdown-item" href="?tipe=TA&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang TA</a></li>
                            <li><a class="dropdown-item" href="?tipe=Semester&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang Semester</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangStatusButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($statusButtonText) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=semua">Semua Status</a></li>
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=belum">Belum Disetujui</a></li>
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=disetujui">Disetujui</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="header-right-panel">
                <div id="desktop-icons-container">
                    <div class="header-icons">
                        <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                        <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="bi bi-person-fill"></i></a></div>
                    </div>
                </div>
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari Nama Mahasiswa..." aria-label="Cari">
                </div>
            </div>
        </div>

        <div class="table-responsive">
          <table class="table-admin-custom">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">NIM</th>
                <th scope="col">Nama</th>
                <th scope="col"><?= htmlspecialchars($dynamicHeaderText) ?></th>
                <th scope="col"><?= htmlspecialchars($dynamicDosenHeaderText) ?></th>
                <th scope="col" style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody id="adminSidangContent">
              <?php if (empty($filteredData)): ?>
                <tr class="no-results-row"><td colspan="6">Tidak ada data untuk ditampilkan.</td></tr>
              <?php else: ?>
                <?php foreach ($filteredData as $entry): ?>
                <?php
                    $row_props_js = "";
                    $action_button = "";
                    $dosen_pengampu_json = '[]';
                    $judul_or_matkul = 'N/A';
                    $pembimbing_or_pengampu = 'N/A';

                    // Determine content based on tipe sidang
                    if ($entry['tipeSidang'] == 'TA') {
                        $judul_or_matkul = htmlspecialchars($entry['judulSidang'] ?? 'N/A');
                        $pembimbing_or_pengampu = htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                    } elseif ($entry['tipeSidang'] == 'Semester') {
                        $judul_or_matkul = htmlspecialchars($entry['mataKuliah'] ?? $entry['judulSidang'] ?? 'N/A');
                        if (isset($entry['dosenPengampu']) && is_array($entry['dosenPengampu'])) {
                           $dosen_pengampu_json = htmlspecialchars(json_encode($entry['dosenPengampu']), ENT_QUOTES, 'UTF-8');
                           $pembimbing_or_pengampu = htmlspecialchars(implode(', ', $entry['dosenPengampu']));
                        } else {
                           $pembimbing_or_pengampu = htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                        }
                    }

                    if ($entry['statusPersetujuan'] === true) {
                        $row_props_js = "data-id='".htmlspecialchars($entry['id'] ?? '')."'"
                                    . " data-nim='".htmlspecialchars($entry['nim'] ?? '')."'"
                                    . " data-nama='".htmlspecialchars($entry['nama'] ?? '')."'"
                                    . " data-judul='". $judul_or_matkul ."'"
                                    . " data-pembimbing='". $pembimbing_or_pengampu ."'"
                                    . " data-prodi='".htmlspecialchars($entry['prodi'] ?? 'Teknologi Rekayasa Perangkat Lunak')."'"
                                    . " data-tipe-sidang='".htmlspecialchars($entry['tipeSidang'] ?? '')."'"
                                    . " data-pengampu='".$dosen_pengampu_json."'";
                        $action_button = "<button type=\"button\" class=\"btn detail-btn\" onclick='event.stopPropagation(); openJadwalModal(this.closest(\"tr\"))'>"
                                    . "<i class=\"fa-solid fa-file-signature fs-5\"></i>"
                                    . "</button>";
                    } else {
                        $action_button = "<button type=\"button\" class=\"btn detail-btn action-disabled\">"
                                       . "<i class=\"fa-solid fa-file-signature fs-5\"></i>"
                                       . "</button>";
                    }
                ?>
                <tr class="isiTabel" <?= $row_props_js ?>>
                  <td data-label="ID"><?= htmlspecialchars($entry['id'] ?? 'N/A') ?></td>
                  <td data-label="NIM"><?= htmlspecialchars($entry['nim'] ?? 'N/A') ?></td>
                  <td data-label="Nama"><?= htmlspecialchars($entry['nama'] ?? 'N/A') ?></td>
                  <td data-label="<?= $dynamicHeaderText ?>"><?= $judul_or_matkul ?></td>
                  <td data-label="<?= $dynamicDosenHeaderText ?>"><?= $pembimbing_or_pengampu ?></td>
                  <td data-label="Aksi" style="text-align: center;"><?= $action_button ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <div class="pagination-container">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="pagination-controls"></ul>
            </nav>
        </div>
    </main>
  </div>
  
  <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h1 class="modal-title mx-auto fs-5" id="modalLogoutLabel">Perhatian!</h1>
            </div>
            <div class="modal-body text-center py-3">
                Apakah anda yakin ingin keluar?
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
            </div>
        </div>
    </div>
  </div> 

  <!-- Modals for scheduling -->
  <div class="modal fade" id="penjadwalanSidangTAModal" aria-labelledby="penjadwalanSidangTAModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content modal-content-custom-form">
              <div class="modal-body">
                  <h2>Penjadwalan Sidang TA</h2>
                  <form id="formDalamModal-ta" novalidate>
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-ta">NIM</label><input type="text" id="modal_nim-ta" readonly /></div>
                          <div class="form-group"><label for="modal_judul_sidang-ta">Judul Sidang</label><input type="text" id="modal_judul_sidang-ta" readonly /></div>
                          <div class="form-group"><label for="modal_pembimbing-ta">Pembimbing</label><input type="text" id="modal_pembimbing-ta" readonly /></div>
                          <div id="penguji-wrapper-ta">
                              <div class="form-group" id="penguji-form-ta-1">
                                  <label for="modal_penguji-ta-1">Penguji 1</label>
                                  <div class="input-with-buttons">
                                      <input type="text" id="modal_penguji-ta-1" name="penguji_nama[]" placeholder="Nama Penguji 1" />
                                      <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-1')">-</button>
                                          <input type="number" id="modal_qty_penguji-ta-1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-1')">+</button>
                                      </div>
                                      <div class="form-toggle-buttons">
                                          <button type="button" onclick="addPenguji()">+</button>
                                          <button type="button" onclick="removePenguji()">-</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group"><label for="modal_prodi-ta">Prodi</label><input type="text" id="modal_prodi-ta" readonly /></div>
                          <div class="form-group"><label for="modal_ruangan-ta">Ruangan</label><input type="text" id="modal_ruangan-ta" name="ruangan" /></div>
                          <div class="form-group"><label for="modal_tanggal-ta">Tanggal</label><input type="date" id="modal_tanggal-ta" name="tanggal" /></div>
                          <div class="form-group">
                              <label for="modal_jam_awal-ta">Jam</label>
                              <div class="time-input-range">
                                  <input type="time" id="modal_jam_awal-ta" name="jam_awal" /><span class="time-separator">-</span><input type="time" id="modal_jam_akhir-ta" name="jam_akhir" />
                              </div>
                          </div>
                          <div class="form-error-message" id="form-error-ta"></div>
                          <div class="form-actions">
                              <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                              <button type="submit" class="btn btn-submit">Buat Penjadwalan</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade" id="penjadwalanSidangSemModal" aria-labelledby="penjadwalanSidangSemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content modal-content-custom-form">
              <div class="modal-body">
                  <h2>Penjadwalan Sidang Semester</h2>
                  <form id="formDalamModal-sem" novalidate>
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-sem">NIM</label><input type="text" id="modal_nim-sem" readonly /></div>
                          <div class="form-group"><label for="modal_matkul-sem">Mata Kuliah</label><input type="text" id="modal_matkul-sem" readonly /></div>
                          <div id="pengampu-wrapper-sem">
                              <div class="form-group" id="pengampu-form-sem-1">
                                  <label for="modal_pengampu-sem-1">Pengampu 1</label>
                                  <div class="input-with-buttons">
                                      <input type="text" id="modal_pengampu-sem-1" name="pengampu_nama[]" placeholder="Nama Pengampu 1" />
                                      <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-1')">-</button>
                                          <input type="number" id="modal_qty_pengampu-sem-1" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" />
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-1')">+</button>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group" id="pengampu-form-sem-2">
                                  <label for="modal_pengampu-sem-2">Pengampu 2</label>
                                  <div class="input-with-buttons">
                                      <input type="text" id="modal_pengampu-sem-2" name="pengampu_nama[]" placeholder="Nama Pengampu 2" />
                                      <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-2')">-</button>
                                          <input type="number" id="modal_qty_pengampu-sem-2" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" />
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-2')">+</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group"><label for="modal_prodi-sem">Prodi</label><input type="text" id="modal_prodi-sem" readonly /></div>
                          <div class="form-group"><label for="modal_ruangan-sem">Ruangan</label><input type="text" id="modal_ruangan-sem" name="ruangan" /></div>
                          <div class="form-group"><label for="modal_tanggal-sem">Tanggal</label><input type="date" id="modal_tanggal-sem" name="tanggal" /></div>
                          <div class="form-group">
                              <label for="modal_jam_awal-sem">Jam</label>
                              <div class="time-input-range">
                                  <input type="time" id="modal_jam_awal-sem" name="jam_awal" /><span class="time-separator">-</span><input type="time" id="modal_jam_akhir-sem" name="jam_akhir" />
                              </div>
                          </div>
                          <div class="form-error-message" id="form-error-sem"></div>
                          <div class="form-actions">
                              <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                              <button type="submit" class="btn btn-submit">Buat Penjadwalan</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    let taModalInstance, semModalInstance;
    let pengujiCount = 1;

    document.addEventListener("DOMContentLoaded", function() {
        // --- PAGINATION SCRIPT (FROM aDaftarSidang.php) ---
        let currentPage = 1;
        const rowsPerPage = 10;
        const tableBody = document.getElementById('adminSidangContent');
        const activeRows = Array.from(tableBody.querySelectorAll('tr.isiTabel'));
        const paginationControls = document.getElementById('pagination-controls');

        function displayPage(page) {
            currentPage = page;
            activeRows.forEach(row => row.style.display = 'none');
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedRows = activeRows.slice(startIndex, endIndex);
            paginatedRows.forEach(row => { row.style.display = ''; });
            updatePaginationButtons();
        }

        function setupPagination() {
            paginationControls.innerHTML = '';
            const pageCount = Math.ceil(activeRows.length / rowsPerPage);
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displayPage(currentPage - 1);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => { e.preventDefault(); displayPage(i); });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                const totalPages = Math.ceil(activeRows.length / rowsPerPage);
                if (currentPage < totalPages) displayPage(currentPage + 1);
            });
            paginationControls.appendChild(nextButton);
            updatePaginationButtons();
        }

        function updatePaginationButtons() {
            const pageCount = Math.ceil(activeRows.length / rowsPerPage);
            const pageItems = paginationControls.querySelectorAll('.page-item');
            if (pageItems.length === 0) return;
            pageItems.forEach((item, index) => {
                item.classList.remove('active', 'disabled');
                if (index === 0) { if (currentPage === 1) item.classList.add('disabled'); }
                else if (index === pageItems.length - 1) { if (currentPage === pageCount) item.classList.add('disabled'); }
                else { if (index === currentPage) item.classList.add('active'); }
            });
        }
        
        // Initialize Pagination
        if(activeRows.length > 0) {
            setupPagination();
            displayPage(1);
        }

        // --- SEARCH SCRIPT ---
        const searchInput = document.querySelector('.search-input-group .form-control');
        const noDataRow = document.querySelector('.no-results-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase().trim();
                let visibleRows = [];

                activeRows.forEach(row => {
                    const namaCell = row.cells[2];
                    const namaText = namaCell.textContent.toLowerCase();
                    if (namaText.includes(searchText)) {
                        visibleRows.push(row);
                    }
                });

                // Re-paginate the filtered results
                currentPage = 1;
                activeRows.forEach(row => row.style.display = 'none'); // Hide all original rows first
                
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedVisibleRows = visibleRows.slice(startIndex, endIndex);

                paginatedVisibleRows.forEach(row => {
                    row.style.display = '';
                });

                // Update pagination controls for the new filtered set of rows
                const pageCount = Math.ceil(visibleRows.length / rowsPerPage);
                updatePaginationForSearch(pageCount, visibleRows);

                // Show/hide no results message
                if(noDataRow) noDataRow.style.display = visibleRows.length === 0 ? '' : 'none';
            });
        }

        function updatePaginationForSearch(pageCount, currentVisibleRows) {
            paginationControls.innerHTML = '';
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displaySearchedPage(currentPage - 1, currentVisibleRows);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => { e.preventDefault(); displaySearchedPage(i, currentVisibleRows); });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < pageCount) displaySearchedPage(currentPage + 1, currentVisibleRows);
            });
            paginationControls.appendChild(nextButton);

            displaySearchedPage(1, currentVisibleRows);
        }

        function displaySearchedPage(page, searchedRows) {
            currentPage = page;
            activeRows.forEach(r => r.style.display = 'none');
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedRows = searchedRows.slice(startIndex, endIndex);
            paginatedRows.forEach(row => { row.style.display = ''; });
            updatePaginationButtons(); // This function can be reused
        }
        
        // --- MOBILE MENU AND ICONS SCRIPT (from aDaftarSidang) ---
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");
        const desktopIconsContainer = document.getElementById('desktop-icons-container');
        const mobileIconsContainer = document.getElementById('mobile-icons-container');
        const headerIcons = desktopIconsContainer.querySelector('.header-icons');

        function handleIconPlacement() {
            if (window.innerWidth <= 992) {
                if (!mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
            } else {
                if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons);
            }
        }
        if (menuToggle && sidebar) {
            menuToggle.onclick = () => {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }
        handleIconPlacement();
        window.addEventListener('resize', handleIconPlacement);

        // --- ORIGINAL MODAL AND FORM SCRIPT ---
        const taModalEl = document.getElementById('penjadwalanSidangTAModal');
        if (taModalEl) taModalInstance = new bootstrap.Modal(taModalEl);
        
        const semModalEl = document.getElementById('penjadwalanSidangSemModal');
        if (semModalEl) semModalInstance = new bootstrap.Modal(semModalEl);

        const formTA = document.getElementById('formDalamModal-ta');
        if(formTA) formTA.addEventListener('submit', handleFormSubmit);

        const formSem = document.getElementById('formDalamModal-sem');
        if(formSem) formSem.addEventListener('submit', handleFormSubmit);
    });
    
    // Function to open modal (unchanged)
    function openJadwalModal(element) {
        const tipeSidang = element.dataset.tipeSidang;
        if (tipeSidang === 'TA') {
            resetAndPopulateTAModal(element);
            taModalInstance.show();
        } else if (tipeSidang === 'Semester') {
            populateSemModal(element);
            semModalInstance.show();
        }
    }
    // All other helper functions (resetAndPopulateTAModal, populateSemModal, etc.) remain the same
    function resetAndPopulateTAModal(el) {
        const wrapper = document.getElementById('penguji-wrapper-ta');
        wrapper.innerHTML = `
            <div class="form-group" id="penguji-form-ta-1">
                <label for="modal_penguji-ta-1">Penguji 1</label>
                <div class="input-with-buttons">
                    <input type="text" id="modal_penguji-ta-1" name="penguji_nama[]" placeholder="Nama Penguji 1" />
                    <div class="bobot-nilai-input-group">
                        <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-1')">-</button>
                        <input type="number" id="modal_qty_penguji-ta-1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                        <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-1')">+</button>
                    </div>
                    <div class="form-toggle-buttons">
                        <button type="button" onclick="addPenguji()">+</button>
                        <button type="button" onclick="removePenguji()">-</button>
                    </div>
                </div>
            </div>`;
        pengujiCount = 1;
        updateToggleButtonsVisibility();
        document.getElementById('modal_nim-ta').value = el.dataset.nim || '';
        document.getElementById('modal_nim-ta').dataset.id = el.dataset.id || '';
        document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
        document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
        document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
        document.getElementById('modal_ruangan-ta').value = '';
        document.getElementById('modal_tanggal-ta').value = '';
        document.getElementById('modal_jam_awal-ta').value = '';
        document.getElementById('modal_jam_akhir-ta').value = '';
        document.getElementById('form-error-ta').textContent = '';
    }
    function populateSemModal(el) {
        document.getElementById('modal_nim-sem').value = el.dataset.nim || '';
        document.getElementById('modal_nim-sem').dataset.id = el.dataset.id || '';
        document.getElementById('modal_matkul-sem').value = el.dataset.judul || '';
        document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
        const pengampu = JSON.parse(el.dataset.pengampu || '[]');
        document.getElementById('modal_pengampu-sem-1').value = pengampu[0] || '';
        document.getElementById('modal_pengampu-sem-2').value = pengampu[1] || '';
        document.getElementById('modal_ruangan-sem').value = '';
        document.getElementById('modal_tanggal-sem').value = '';
        document.getElementById('modal_jam_awal-sem').value = '';
        document.getElementById('modal_jam_akhir-sem').value = '';
        document.getElementById('form-error-sem').textContent = '';
    }
    function addPenguji() {
        pengujiCount++;
        const wrapper = document.getElementById('penguji-wrapper-ta');
        const div = document.createElement('div');
        div.className = 'form-group';
        div.id = `penguji-form-ta-${pengujiCount}`;
        div.innerHTML = `
            <label for="modal_penguji-ta-${pengujiCount}">Penguji ${pengujiCount}</label>
            <div class="input-with-buttons">
                <input type="text" id="modal_penguji-ta-${pengujiCount}" name="penguji_nama[]" placeholder="Nama Penguji ${pengujiCount}" />
                <div class="bobot-nilai-input-group">
                    <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-${pengujiCount}')">-</button>
                    <input type="number" id="modal_qty_penguji-ta-${pengujiCount}" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                    <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-${pengujiCount}')">+</button>
                </div>
                <div class="form-toggle-buttons">
                    <button type="button" onclick="addPenguji()">+</button>
                    <button type="button" onclick="removePenguji()">-</button>
                </div>
            </div>`;
        wrapper.appendChild(div);
        updateToggleButtonsVisibility();
    }
    function removePenguji() {
        if (pengujiCount > 1) {
            const lastForm = document.getElementById(`penguji-form-ta-${pengujiCount}`);
            if (lastForm) { lastForm.remove(); pengujiCount--; }
        }
        updateToggleButtonsVisibility();
    }
    function updateToggleButtonsVisibility() {
        const toggleButtons = document.querySelectorAll('#penguji-wrapper-ta .form-toggle-buttons');
        toggleButtons.forEach((btnGroup, index) => {
            if (index === toggleButtons.length - 1) {
                btnGroup.style.display = 'inline-flex';
                const removeBtn = btnGroup.querySelector('button[onclick="removePenguji()"]');
                if (removeBtn) { removeBtn.style.display = (pengujiCount <= 1) ? 'none' : 'block'; }
            } else { btnGroup.style.display = 'none'; }
        });
    }
    function incrementValue(inputId) { const input = document.getElementById(inputId); if (input) input.value = parseInt(input.value, 10) + 1; }
    function decrementValue(inputId) {
        const input = document.getElementById(inputId);
        if (input) { let val = parseInt(input.value, 10); if (val > (input.min || 0)) { input.value = val - 1; } }
    }
    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const modalType = form.id.includes('-ta') ? 'TA' : 'Sem';
        if (validateForm(modalType)) {
            const modalInstance = modalType === 'TA' ? taModalInstance : semModalInstance;
            modalInstance.hide();
            Swal.fire({
                title: 'Berhasil', text: 'Jadwal Berhasil Dibuat.', imageUrl: '../../assets/img/centang.svg',
                imageWidth: 120, imageHeight: 120, imageAlt: 'Success checkmark',
                confirmButtonText: 'OK', confirmButtonColor: '#4336F0'
            }).then(() => { location.reload(); });
        }
    }
    function validateForm(modalType) {
        const suffix = modalType === 'TA' ? '-ta' : '-sem';
        const errorBox = document.getElementById(`form-error${suffix}`);
        errorBox.textContent = ''; let errorMessage = '';
        const dosenInputs = document.querySelectorAll(`input[name="${modalType === 'TA' ? 'penguji' : 'pengampu'}_nama[]"]`);
        for (let i = 0; i < dosenInputs.length; i++) { if (dosenInputs[i].value.trim() === '') { errorMessage = `Nama ${modalType === 'TA' ? 'Penguji' : 'Pengampu'} ${i + 1} harus diisi!`; break; } }
        if (errorMessage) { errorBox.textContent = errorMessage; return false; }
        const ruangan = document.getElementById(`modal_ruangan${suffix}`).value.trim();
        if (ruangan === '') { errorBox.textContent = 'Ruangan harus diisi!'; return false; }
        const tanggal = document.getElementById(`modal_tanggal${suffix}`).value;
        if (tanggal === '') { errorBox.textContent = 'Tanggal harus dipilih!'; return false; }
        const today = new Date(); const selectedDate = new Date(tanggal); today.setHours(0, 0, 0, 0); 
        if (selectedDate < today) { errorBox.textContent = 'Tanggal tidak boleh kurang dari tanggal hari ini!'; return false; }
        const jamAwal = document.getElementById(`modal_jam_awal${suffix}`).value;
        const jamAkhir = document.getElementById(`modal_jam_akhir${suffix}`).value;
        if (jamAwal === '' || jamAkhir === '') { errorBox.textContent = 'Jam awal dan jam akhir harus diisi!'; return false; }
        if (jamAkhir <= jamAwal) { errorBox.textContent = 'Jam akhir harus setelah jam awal!'; return false; }
        return true;
    }
</script>
</body>
</html>