<?php
require_once '../../control/admin/aPenjadwalan_queries.php';
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
        <div class="header-icons-mobile header-icons">
            <a href="aNotifikasi.php" title="Notifikasi"><i class="fa-solid fa-bell"></i></a>
            <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="fa-solid fa-user"></i></a></div>
        </div>
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
                            <li><a class="dropdown-item" href="?tipe=semua&prodi=<?= htmlspecialchars($selectedProdi) ?>">Semua Tipe</a></li>
                            <li><a class="dropdown-item" href="?tipe=TA&prodi=<?= htmlspecialchars($selectedProdi) ?>">Sidang TA</a></li>
                            <li><a class="dropdown-item" href="?tipe=Semester&prodi=<?= htmlspecialchars($selectedProdi) ?>">Sidang Semester</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                         <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminProdiButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($prodiButtonText) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=<?= urlencode($selectedTipe) ?>&prodi=semua">Semua Prodi</a></li>
                            <?php foreach ($prodiList as $prodi): ?>
                                <li><a class="dropdown-item" href="?tipe=<?= urlencode($selectedTipe) ?>&prodi=<?= urlencode($prodi) ?>"><?= htmlspecialchars($prodi) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="header-right-panel">
                <div id="desktop-icons-container" class="header-icons">
                    <a href="aNotifikasi.php" title="Notifikasi"><i class="fa-solid fa-bell"></i></a>
                    <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="fa-solid fa-user"></i></a></div>
                </div>
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari" aria-label="Cari">
                </div>
            </div>
        </div>

       <div class="table-responsive">
         <table class="table-admin-custom">
           <thead>
             <tr>
                 <th scope="col">No</th>
                 <th scope="col">Kelompok</th>
                 <th scope="col">Judul</th>
                 <th scope="col">Mata Kuliah</th>
                 <th scope="col">Pembimbing/Pengampu</th>
                 <th scope="col" style="text-align: center;">Aksi</th>
             </tr>
           </thead>
           <tbody id="adminSidangContent">
               <?php if (empty($data)): ?>
                        <tr class="no-results-row"><td colspan="6">Tidak ada data untuk dijadwalkan.</td></tr>
                    <?php else: ?>
                        <?php 
                        $counter = 1;
                        foreach ($data as $entry):
                            // Menyiapkan variabel untuk ditampilkan
                            $judul_tampil = htmlspecialchars($entry['judulSidang']);
                            $matkul_tampil = 'N/A';
                            $dosen_tampil = 'N/A';
                            $dosen_pengampu_json = '[]';
                            
                            if ($entry['tipeSidang'] == 'Tugas Akhir') {
                                $dosen_tampil = htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                                $matkul_tampil = htmlspecialchars($entry['mataKuliah'] ?? 'N/A');
                            } elseif ($entry['tipeSidang'] == 'Semester') {
                                $matkul_tampil = htmlspecialchars($entry['mataKuliah'] ?? 'N/A');
                                $dosen_pengampu_list_string = $entry['dosenPengampuList'] ?? '';
                                $dosen_array = !empty($dosen_pengampu_list_string) ? preg_split('/\r\n|\r|\n/', $dosen_pengampu_list_string, -1, PREG_SPLIT_NO_EMPTY) : [];
                                $dosen_tampil = !empty($dosen_array) ? implode('<br>', array_map('htmlspecialchars', $dosen_array)) : 'N/A';
                                $dosen_pengampu_json = htmlspecialchars(json_encode($dosen_array), ENT_QUOTES, 'UTF-8');
                            }

                            // Siapkan data-* attributes untuk JavaScript
                            $row_props_js = "data-id='".htmlspecialchars($entry['id_sidang'])."'"
                                . " data-kelompok='".htmlspecialchars($entry['id_kelompok'])."'"
                                . " data-nama-list='".htmlspecialchars($entry['namaList'] ?? '')."'"
                                . " data-judul='".htmlspecialchars($entry['judulSidang'])."'"
                                . " data-matkul='".htmlspecialchars($entry['mataKuliah'] ?? 'N/A')."'"
                                . " data-pembimbing='".htmlspecialchars($entry['pembimbing'] ?? 'N/A')."'"
                                . " data-prodi='".htmlspecialchars($entry['prodi'])."'"
                                . " data-tipe-sidang='".htmlspecialchars($entry['tipeSidang'])."'"
                                . " data-pengampu='". $dosen_pengampu_json ."'";
                        ?>
                        <tr class="isiTabel" <?= $row_props_js ?>>
                            <td data-label="Nomor"><?= $counter++ ?></td>
                            <td data-label="Kelompok"><?= htmlspecialchars($entry['id_kelompok']) ?></td>
                            <td data-label="Judul"><?= $judul_tampil ?></td>
                            <td data-label="Mata Kuliah"><?= $matkul_tampil ?></td>
                            <td data-label="Pembimbing/Pengampu"><?= $dosen_tampil ?></td>
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

  <div class="modal fade" id="penjadwalanSidangTAModal" aria-labelledby="penjadwalanSidangTAModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content modal-content-custom-form">
              <div class="modal-body">
                  <h2>Penjadwalan Sidang TA</h2>
                  <form id="formDalamModal-ta" novalidate>
                      <input type="hidden" name="id_sidang" id="modal_id_sidang-ta">
                      <input type="hidden" name="tipe_sidang" value="Tugas Akhir">
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-ta">Kelompok</label><input type="text" id="modal_nim-ta" readonly /></div>
                          <div class="form-group"><label for="modal_judul_sidang-ta">Judul Sidang</label><input type="text" id="modal_judul_sidang-ta" readonly /></div>
                          
                          <div class="form-group">
                            <label for="modal_pembimbing-ta">Pembimbing</label>
                            <div class="input-with-buttons">
                                <div class="autocomplete-container">
                            <input type="text" id="modal_pembimbing-ta" name="pembimbing_nama" readonly />
                            </div>
                         <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-1')">-</button>
                                          <div class="input-with-percent">
                                          <input type="number" id="modal_pembimbing_bobot-ta" name="pembimbing_bobot" class="bobot-input-new" value="0" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Tugas Akhir');">
                                        <span class="percent-sign">%</span>
                                           </div>
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-1')">+</button>
                                      </div>
                        </div>
                        </div>
                          <div id="penguji-wrapper-ta">
                            <div class="form-group" id="penguji-form-ta-1">
                                <label for="modal_penguji-ta-1">Penguji 1</label>
                                <div class="input-with-buttons">
                                
                                <div class="autocomplete-container">
                                <input type="text"
                                        id="modal_penguji-ta-1"
                                        name="penguji_nama[]"
                                        placeholder="Ketik nama dosen penguji"
                                        oninput="searchPenguji(this, 1)"
                                        autocomplete="off">
                                    <div class="autocomplete-dropdown" id="autocomplete_penguji_1"></div>
                                </div>
                               <div class="bobot-nilai-input-group">
                                <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-1')">-</button>
                                <div class="input-with-percent">
                                    <input type="number" id="modal_qty_penguji-ta-1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Tugas Akhir');">
                                    <span class="percent-sign">%</span>
                                </div>

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
                          <div class="realtime-validation-message" id="realtime-validation-ta"></div>
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
                                          <div class="input-with-percent">
                                          <input type="number" id="modal_qty_pengampu-sem-1" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Semester');">/>
                                          <span class="percent-sign">%</span>
                                          </div>
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
                                          <div class="input-with-percent">
                                          <input type="number" id="modal_qty_pengampu-sem-2" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Semester');">/>
                                          <span class="percent-sign">%</span>
                                          </div>
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
                          <div class="realtime-validation-message" id="realtime-validation-ta"></div>
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