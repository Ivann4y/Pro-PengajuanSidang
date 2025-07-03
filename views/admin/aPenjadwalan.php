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
$allDosenList = [];
$queryAllDosen = "SELECT nama_dosen FROM Dosen ORDER BY nama_dosen";
$resultAllDosen = sqlsrv_query($conn, $queryAllDosen);
if ($resultAllDosen) {
    while ($row = sqlsrv_fetch_array($resultAllDosen, SQLSRV_FETCH_ASSOC)) {
        // Ciptakan struktur array of objects agar cocok dengan JS
        $allDosenList[] = ['nama' => $row['nama_dosen']];
    }
}

// --- MODIFIKASI: Baca parameter filter dari URL ---
$selectedTipe = $_GET['tipe'] ?? 'semua';
$selectedProdi = $_GET['prodi'] ?? 'semua'; // BARU: Baca filter prodi

// --- BARU: Query untuk mengambil daftar prodi unik ---
$prodiList = [];
$queryProdi = "SELECT DISTINCT prodi FROM Mahasiswa WHERE prodi IS NOT NULL ORDER BY prodi";
$resultProdi = sqlsrv_query($conn, $queryProdi);
if ($resultProdi) {
    while ($row = sqlsrv_fetch_array($resultProdi, SQLSRV_FETCH_ASSOC)) {
        $prodiList[] = $row['prodi'];
    }
}

// --- MODIFIKASI: Query utama untuk mengambil data sidang ---
$params = [];
$whereClauses = [];

// Filter WAJIB: Hanya tampilkan yang status ajuannya disetujui (1)
$whereClauses[] = "s.status_ajuan = 'Approve'";

// Filter OPSIONAL: Berdasarkan tipe sidang
if ($selectedTipe == 'TA') {
    $whereClauses[] = "k.jenis_sidang = 'TA'";
} elseif ($selectedTipe == 'Semester') {
    $whereClauses[] = "k.jenis_sidang = 'Semester'";
}

// --- BARU: Filter OPSIONAL: Berdasarkan prodi ---
if ($selectedProdi !== 'semua') {
    $whereClauses[] = "m.prodi = ?";
    $params[] = $selectedProdi;
}

$query = "SELECT 
    s.id_sidang AS id,
    s.id_kelompok,
    s.judul AS judulSidang,
    k.jenis_sidang AS tipeSidang, 
    MAX(m.prodi) as prodi,
    STRING_AGG(m.nama_mhs, ', ') AS namaList,
    MAX(mk.nama_matkul) AS mataKuliah,
    MAX(d_pembimbing.nama_dosen) AS pembimbing,
   (SELECT STRING_AGG(d.nama_dosen, CHAR(13) + CHAR(10))
     FROM Pengampu_Kelas pk
     JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
     WHERE 
        pk.id_matkul = (SELECT TOP 1 ds.id_matkul FROM Detail_Sidang ds WHERE ds.id_sidang = s.id_sidang)
        AND pk.id_kelas = (SELECT TOP 1 km.id_kelas
                           FROM Kelompok_Mahasiswa kpm
                           JOIN Kelas_Mahasiswa km ON kpm.nim = km.nim
                           WHERE kpm.id_kelompok = s.id_kelompok)
    ) AS dosenPengampuList
    FROM Sidang s
    INNER JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    INNER JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
    INNER JOIN Mahasiswa m ON km.nim = m.nim
    LEFT JOIN Bimbingan b ON s.id_kelompok = b.id_kelompok AND k.jenis_sidang = 'TA'
    LEFT JOIN Dosen d_pembimbing ON b.nomor_dosen = d_pembimbing.nomor_dosen
    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang AND k.jenis_sidang = 'Semester'
    LEFT JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
    WHERE " . implode(' AND ', $whereClauses) . "
     AND NOT EXISTS (SELECT 1 FROM Jadwal j WHERE j.id_sidang = s.id_sidang)
    -- PERBAIKAN: GROUP BY menggunakan k.jenis_sidang
    GROUP BY s.id_sidang, s.id_kelompok, s.judul, k.jenis_sidang
    ORDER BY s.id_sidang";


$result = sqlsrv_query($conn, $query, $params);
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

$data = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $row['dosenPengampu'] = !empty($row['dosenPengampuList']) ? explode(', ', $row['dosenPengampuList']) : [];
    $data[] = $row;
}

// --- MODIFIKASI: Variabel untuk teks tombol dan header ---
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'TA') $tipeButtonText = 'Sidang TA';
elseif ($selectedTipe == 'Semester') $tipeButtonText = 'Sidang Semester';

// BARU: Teks untuk tombol filter prodi
$prodiButtonText = 'Semua Prodi';
if ($selectedProdi !== 'semua') {
    $prodiButtonText = htmlspecialchars($selectedProdi);
}

$dynamicHeaderText = 'Judul/Mata Kuliah';
if ($selectedTipe == 'TA') $dynamicHeaderText = 'Judul Sidang';
elseif ($selectedTipe == 'Semester') $dynamicHeaderText = 'Mata Kuliah';

$dynamicDosenHeaderText = 'Pembimbing/Dosen';
if ($selectedTipe == 'TA') $dynamicDosenHeaderText = 'Pembimbing';
elseif ($selectedTipe == 'Semester') $dynamicDosenHeaderText = 'Dosen Pengampu';

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
                        <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($prodiButtonText) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <!-- MODIFIKASI: Tambahkan parameter tipe agar tidak ter-reset -->
                            <li><a class="dropdown-item" href="?tipe=<?= urlencode($selectedTipe) ?>&prodi=semua">Semua Prodi</a></li>
                            <?php foreach ($prodiList as $prodi): ?>
                                <li><a class="dropdown-item" href="?tipe=<?= urlencode($selectedTipe) ?>&prodi=<?= urlencode($prodi) ?>"><?= htmlspecialchars($prodi) ?></a></li>
                            <?php endforeach; ?>
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
                <th scope="col">Nomor</th>
                <th scope="col">Kelompok</th>
                <th scope="col"><?= htmlspecialchars($dynamicHeaderText) ?></th>
                <th scope="col"><?= htmlspecialchars($dynamicDosenHeaderText) ?></th>
                <th scope="col" style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody id="adminSidangContent">
              <?php if (empty($data)): ?>
                <tr class="no-results-row"><td colspan="5">Tidak ada data untuk dijadwalkan.</td></tr>
              <?php else: ?>
                <?php 
                $counter = 1;
                foreach ($data as $entry): 
                ?>
                <?php
                    // Logika untuk menyiapkan data modal tetap sama, karena tombol aksi masih memerlukannya
                    $judul_or_matkul = 'N/A';
                    $pembimbing_or_pengampu = 'N/A';
                    $dosen_pengampu_json = '[]';

                    if ($entry['tipeSidang'] == 'TA') {
                        $judul_or_matkul = htmlspecialchars($entry['judulSidang'] ?? 'N/A');
                        $pembimbing_or_pengampu = htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                    } elseif ($entry['tipeSidang'] == 'Semester') {
                       $judul_or_matkul = htmlspecialchars($entry['mataKuliah'] ?? 'N/A');
                        $dosenListString = $entry['dosenPengampuList'] ?? '';
                        $pembimbing_or_pengampu = nl2br(htmlspecialchars($dosenListString));
                        $dosenArray = !empty($dosenListString) ? preg_split('/\r\n|\r|\n/', $dosenListString) : [];
                        $dosen_pengampu_json = htmlspecialchars(json_encode($dosenArray), ENT_QUOTES, 'UTF-8');
                    }
                    
                    // Siapkan data-* attributes untuk dilempar ke modal Javascript
                     $row_props_js = "data-id='".htmlspecialchars($entry['id'] ?? '')."'" 
                                            . " data-kelompok='".htmlspecialchars($entry['id_kelompok'] ?? '')."'"
                                            . " data-nama-list='".htmlspecialchars($entry['namaList'] ?? '')."'"
                                            . " data-judul='". $judul_or_matkul ."'"
                                            . " data-pembimbing='". $pembimbing_or_pengampu ."'"
                                            . " data-prodi='".htmlspecialchars($entry['prodi'] ?? '')."'"
                                            . " data-tipe-sidang='".htmlspecialchars($entry['tipeSidang'] ?? '')."'"
                                            . " data-pengampu='".$dosen_pengampu_json."'";
                            ?>
                            <tr class="isiTabel" <?= $row_props_js ?>>
                                <td data-label="Nomor"><?= $counter++ ?></td>
                                <td data-label="Kelompok"><?= htmlspecialchars($entry['id_kelompok'] ?? 'N/A') ?></td>
                                <td data-label="<?= $dynamicHeaderText ?>"><?= $judul_or_matkul ?></td>
                                <td data-label="<?= $dynamicDosenHeaderText ?>"><?= $pembimbing_or_pengampu ?></td>
                                <td data-label="Aksi" style="text-align: center;">
                                    <button type="button" class="btn detail-btn" onclick='event.stopPropagation(); openJadwalModal(this.closest("tr"))'>
                                        <i class="fa-solid fa-file-signature fs-5"></i>
                                    </button>
                                </td>
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
                        <input type="hidden" name="id_sidang" id="modal_id_sidang-ta">
                        <input type="hidden" name="tipe_sidang" value="TA">
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-ta">Kelompok</label><input type="text" id="modal_nim-ta" readonly /></div>
                          <div class="form-group"><label for="modal_judul_sidang-ta">Judul Sidang</label><input type="text" id="modal_judul_sidang-ta" readonly /></div>
                          <div class="form-group"><label for="modal_pembimbing-ta">Pembimbing</label><input type="text" id="modal_pembimbing-ta" readonly /></div>
                          <div id="penguji-wrapper-ta">
                            <div class="form-group" id="penguji-form-ta-1">
                                <label for="modal_penguji-ta-1">Penguji 1</label>
                                <div class="input-with-buttons">
                                <!-- STRUKTUR AUTOCOMPLETE BARU DI SINI -->
                                <div class="autocomplete-container">
                                <input type="text"
                                        id="modal_penguji-ta-1"
                                        name="penguji_nama[]"
                                        placeholder="Ketik nama dosen penguji"
                                        oninput="searchPenguji(this, 1)"
                                        autocomplete="off">
                                    <div class="autocomplete-dropdown" id="autocomplete_penguji_1"></div>
                                </div>
                                <!-- Akhir struktur autocomplete -->
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
                        <input type="hidden" name="id_sidang" id="modal_id_sidang-sem">
                        <input type="hidden" name="tipe_sidang" value="Semester">
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-sem">Kelompok</label><input type="text" id="modal_nim-sem" readonly /></div>
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
    const dosenData = <?php echo json_encode($allDosenList); ?>;
  </script>
<script src="../../assets/js/aPenjadwalan.js"></script>                 
</body>
</html>