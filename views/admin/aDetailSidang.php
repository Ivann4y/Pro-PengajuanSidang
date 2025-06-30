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

// Langkah 1: Jika ada 'id' di URL, simpan ke session dan redirect.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Simpan ID yang valid dari URL ke dalam session
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    
    // Redirect ke halaman yang sama TAPI TANPA parameter GET
    header("Location: aDetailSidang.php");
    exit();
}

// Langkah 2: Jika TIDAK ada 'id' di URL, ambil dari session.
if (isset($_SESSION['id_sidang_aktif']) && is_numeric($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {
    // Jika tidak ada di URL dan tidak ada di session, maka akses tidak valid.
    // Arahkan kembali ke halaman daftar sidang.
    $_SESSION['error_message'] = "ID Sidang tidak valid atau tidak ditemukan. Silakan pilih sidang dari daftar.";
    header("Location: aDaftarSidang.php");
    exit();
}

// Variabel penampung
$data_nim = [];
$nama_prodi = 'N/A';
$data_sidang = [];
$data_mahasiswa = [];
$dosen_pembimbing = null;
$dosen_penguji = [];
$dosen_pengampu = [];
$data_matkul = null;
$data_bobotPenilaian = [];

// 2. Query utama
$sql_utama = "SELECT 
                s.id_sidang, s.judul, 
                CASE 
                    WHEN s.status_sidang = 1 THEN 'Disetujui'
                    WHEN s.status_sidang = 0 THEN 'Ditolak'
                    ELSE 'Menunggu'
                END AS status_sidang_text, 
                CAST(s.jenis_sidang AS INT) AS jenis_sidang,
                s.id_kelompok
              FROM Sidang s
              WHERE s.id_sidang = ?";
$params_utama = array($id_sidang);
$stmt_utama = sqlsrv_query($conn, $sql_utama, $params_utama);
if ($stmt_utama === false) { die("Error pada query utama: " . print_r(sqlsrv_errors(), true)); }
$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) { die("Error: Data Sidang dengan ID $id_sidang tidak ditemukan."); }

// --- Query Jadwal
$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
$data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC) ?: [];

// --- Query Mahasiswa
$id_kelompok = $data_sidang['id_kelompok'];
$sql_mahasiswa = "SELECT m.prodi FROM Mahasiswa m JOIN Kelompok_Mahasiswa km ON m.nim = km.nim WHERE km.id_kelompok = ? AND m.prodi IS NOT NULL";
$stmt_mahasiswa = sqlsrv_query($conn, $sql_mahasiswa, array($id_kelompok));
if ($row = sqlsrv_fetch_array($stmt_mahasiswa, SQLSRV_FETCH_ASSOC)) {
    $nama_prodi = $row['prodi'];
}

// 4. Logika kondisional
if ($data_sidang['jenis_sidang'] == 0) { // Sidang TA

    // Query untuk mengambil dosen yang terlibat DAN bobot penilaian mereka
    $sql_dosen_terlibat = "SELECT 
            d.nama_dosen, 
            CAST(p.peran_dosen AS INT) AS peran_dosen,
            (SELECT TOP 1 pl.bobot_penilaian 
             FROM Penilaian pl 
             WHERE pl.id_sidang = p.id_sidang AND pl.nomor_dosen = p.nomor_dosen) AS bobot
        FROM Dosen d 
        JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen
        WHERE p.id_sidang = ?
    ";
    
    $stmt_dosen_terlibat = sqlsrv_query($conn, $sql_dosen_terlibat, array($id_sidang));
    
    if ($stmt_dosen_terlibat) {
        $dosen_penguji_data = []; // Buat array sementara untuk penguji & bobot
        while ($row = sqlsrv_fetch_array($stmt_dosen_terlibat, SQLSRV_FETCH_ASSOC)) {
            if ($row['peran_dosen'] == 1) { // 1 adalah Pembimbing
                $dosen_pembimbing = $row; 
            } elseif ($row['peran_dosen'] == 0) { // 0 adalah Penguji
                // Simpan nama dan bobotnya
                $dosen_penguji_data[] = [
                    'nama' => $row['nama_dosen'],
                    'bobot' => $row['bobot']
                ];
                // Simpan namanya saja untuk tampilan utama
                $dosen_penguji[] = $row['nama_dosen'];
            }
        }
    }
} elseif ($data_sidang['jenis_sidang'] == 1) { 
    // [LANGKAH 1] Ambil id_matkul dari detail sidang.
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul, mk.id_matkul 
                   FROM MataKuliah mk 
                   JOIN Detail_Sidang ds ON mk.id_matkul = ds.id_matkul 
                   WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
    if ($stmt_matkul) { 
        $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC); 
    }

    // [LANGKAH 2] Jika mata kuliah ditemukan, cari dosen pengampunya dengan benar.
    if ($data_matkul) {
        // Ambil variabel yang dibutuhkan dari data yang sudah ada
        $id_matkul = $data_matkul['id_matkul'];
        $id_kelompok = $data_sidang['id_kelompok']; // Kita sudah punya id_kelompok

        // [PERBAIKAN UTAMA] Query yang benar untuk mengambil dosen pengampu
        $sql_pengampu = "SELECT d.nama_dosen 
            FROM Dosen d 
            JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen 
            WHERE 
                -- Filter 1: Mencocokkan mata kuliah dengan placeholder
                pk.id_matkul = ?
                
                -- Filter 2: Mencocokkan kelas mahasiswa dengan subquery dan placeholder
                AND pk.id_kelas = (
                    SELECT TOP 1 km.id_kelas
                    FROM Kelompok_Mahasiswa kpm
                    JOIN Kelas_Mahasiswa km ON kpm.nim = km.nim
                    WHERE kpm.id_kelompok = ?
                )
        ";

        // [PERBAIKAN PARAMETER] Berikan DUA parameter yang dibutuhkan: id_matkul dan id_kelompok
        $params_pengampu = array($id_matkul, $id_kelompok);
        $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, $params_pengampu);

        if ($stmt_pengampu === false) {
            die("Error pada query pengampu: " . print_r(sqlsrv_errors(), true));
        }
        
        // Ambil semua nama dosen dan masukkan ke array
        while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
            $dosen_pengampu[] = $row['nama_dosen'];
        }
    }
}


// Ambil daftar semua dosen untuk autocomplete
$dosen_list_penguji = [];
$sql_all_dosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen ASC";
$stmt_all_dosen = sqlsrv_query($conn, $sql_all_dosen);
if ($stmt_all_dosen) {
    while ($row = sqlsrv_fetch_array($stmt_all_dosen, SQLSRV_FETCH_ASSOC)) {
        $dosen_list_penguji[] = ['nama' => $row['nama_dosen']]; 
    }
}
$dosen_list_json = json_encode($dosen_list_penguji);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Zia Zahran Hadi-AliansiSidang_Kelompok5">
    <title>DetailSidang-Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/aDetailSidang.css">
    

    
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="aDetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
                    <!-- <a href="aEvaluasi.php?id=<?= $row['id'] ?>"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a> -->
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>
            </ul>
        </div>

        <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
            <div class="NavSide__topbar">
                <div class="NavSide__toggle">
                    <i class="bi bi-list open"></i>
                    <i class="bi bi-x-lg close"></i>
                </div>
            </div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang -
                    <?php
                    if ($data_sidang['jenis_sidang'] == 0 && !empty($data_sidang)) {
                        echo htmlspecialchars($data_sidang['judul']);
                    } elseif ($data_sidang['jenis_sidang'] == 1 && !empty($data_sidang)) {
                        echo htmlspecialchars($data_sidang['judul']);
                    }
                    ?></h2>
                <p class="page-nama">Kelompok <?php echo htmlspecialchars($data_sidang['id_kelompok']); ?></p>

                <div class="status-badge">Status Pengajuan : <?php echo htmlspecialchars($data_sidang['status_sidang_text']); ?></div>
                <div class="info-card">
                    <div class="section">
                        <!-- Tampilan akan dirender berdasarkan kondisi IF -->
                        <?php if ($data_sidang['jenis_sidang'] == 0): ?>
                            <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br><?php echo !empty($data_sidang['judul']) ? htmlspecialchars($data_sidang['judul']) : 'Belum ada judul'; ?></p>
                            <p><i class="fa-solid fa-user"></i><strong>Dosen Pembimbing</strong><br><?php echo !empty($dosen_pembimbing['nama_dosen']) ? htmlspecialchars($dosen_pembimbing['nama_dosen']) : 'Belum ditentukan'; ?></p>
                            <p><i class="fa-solid fa-users"></i><strong>Dosen Penguji</strong><br>
                                <?php
                                if (!empty($dosen_penguji)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosen_penguji));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?></p>
                        <?php elseif ($data_sidang['jenis_sidang'] == 1): ?>
                            <p><i class="fa-solid fa-book"></i><strong>Mata Kuliah</strong><br><?php echo !empty($data_matkul['nama_matkul']) ? htmlspecialchars($data_matkul['nama_matkul']) : 'N/A'; ?></p>
                            <p><i class="fa-solid fa-users"></i><strong>Dosen Pengampu</strong><br>
                                <?php
                                if (!empty($dosen_pengampu)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosen_pengampu));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?></p>
                        <?php else: ?>
                            <!-- Ini akan muncul jika jenis_sidang bukan 0 atau 1 -->
                            <p>Jenis sidang tidak dikenali.</p>
                        <?php endif; ?>
                    </div>
                    <div class="section">
                        <p><i class="fa-solid fa-door-open"></i><strong>Ruangan</strong><br><?php echo !empty($data_jadwal['ruang_sidang']) ? htmlspecialchars($data_jadwal['ruang_sidang']) : 'Belum Dijadwalkan'; ?></p>

                        <p><i class="fa-solid fa-calendar-days"></i><strong>Tanggal</strong><br>
                            <?php
                            if (!empty($data_jadwal['tanggal_sidang']) && $data_jadwal['tanggal_sidang'] instanceof DateTime) {
                                setlocale(LC_TIME, 'id_ID.utf8');
                                echo $data_jadwal['tanggal_sidang']->format('l, d F Y');
                            } else {
                                echo 'Belum Dijadwalkan';
                            }
                            ?>
                        </p>

                        <p><i class="fa-solid fa-clock"></i><strong>Jam</strong><br>
                            <?php
                            if (!empty($data_jadwal['jam_sidang']) && $data_jadwal['jam_sidang'] instanceof DateTime) {
                                echo $data_jadwal['jam_sidang']->format('H.i');
                                if (!empty($data_jadwal['jam_selesai']) && $data_jadwal['jam_selesai'] instanceof DateTime) {
                                    echo ' - ' . $data_jadwal['jam_selesai']->format('H.i');
                                }
                            } else {
                                echo 'Belum Dijadwalkan';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <h5 class="mt-4">Aksi</h5>
                <button class="btn-ubah" onclick="openModal()">Ubah Jadwal Sidang</button>
                <button class="btn-hapus" onclick="confirmDelete(<?php echo htmlspecialchars($id_sidang); ?>)">Hapus Sidang</button>
                <br><br>
                


                <div class="modal fade" id="penjadwalanSidangModal" aria-labelledby="penjadwalanSidangModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content modal-content-custom-form">
                            <div class="modal-body">
                                <h2>Penjadwalan Sidang</h2>
                                <div class="form-container">
                                    <form id="formDalamModal" novalidate>
                                        <input type="hidden" name="id_sidang" value="<?php echo htmlspecialchars($id_sidang); ?>">

                                        <!-- ====================================== -->
                                        <!--      FIELD UMUM UNTUK SEMUA JENIS      -->
                                        <!-- ====================================== -->
                                        <div class="form-group">
                                            <label>ID Kelompok</label>
                                            <p><?php echo htmlspecialchars($data_sidang['id_kelompok']); ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Prodi</label>
                                            <p><?php echo htmlspecialchars($nama_prodi); ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo ($data_sidang['jenis_sidang'] == 0) ? 'Judul Sidang' : 'Mata Kuliah'; ?></label>
                                            <p><?php echo htmlspecialchars(($data_sidang['jenis_sidang'] == 0) ? $data_sidang['judul'] : ($data_matkul['nama_matkul'] ?? 'N/A')); ?></p>
                                        </div>

                                        <!-- ====================================== -->
                                        <!--        KONTEN KHUSUS SIDANG TA         -->
                                        <!-- ====================================== -->
                                        <?php if ($data_sidang['jenis_sidang'] == 0): ?>
                                            <div class="form-group">
                                                <label>Pembimbing</label>
                                                <p><?php echo htmlspecialchars($dosen_pembimbing['nama_dosen'] ?? 'Belum ada'); ?></p>
                                            </div>
                                            <hr>
                                            <div id="penguji-wrapper">
    <?php
    $penguji_list_dengan_bobot = !empty($dosen_penguji_data) ? $dosen_penguji_data : [['nama' => '', 'bobot' => '']];
    
    foreach ($penguji_list_dengan_bobot as $index => $penguji):
    ?>
        <div class="form-group" id="penguji-form-<?php echo $index + 1; ?>">
            <label for="modal_penguji<?php echo $index + 1; ?>">Penguji <?php echo $index + 1; ?></label>
            <div class="input-with-buttons">
                 <div class="autocomplete-container">
                   <input type="text"
                    id="modal_penguji<?php echo $index + 1; ?>"
                    name="penguji_nama[]"
                    placeholder="Ketik nama dosen penguji"
                    oninput="searchDosen(this, <?php echo $index + 1; ?>)"
                    value="<?php echo htmlspecialchars($penguji['nama']); ?>"
                    autocomplete="off">
                    <div class="autocomplete-dropdown" id="autocomplete_penguji_<?php echo $index + 1; ?>" style="display: none;"></div>
                </div>
                <div class="input-with-percent">
                    <input type="number" name="penguji_bobot[]" class="form-control-bobot" min="0" placeholder="Bobot" value="<?php echo htmlspecialchars($penguji['bobot']); ?>">
                    <span class="percent-sign">%</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
                                            <div class="form-toggle-buttons">
                                                <button type="button" class="btn-tambah-penguji" onclick="addPenguji()"><i class="fa-solid fa-plus"></i> Tambah Penguji</button>
                                                <button type="button" class="btn-hapus-penguji" onclick="removePenguji()"><i class="fa-solid fa-minus"></i> Hapus Penguji</button>
                                            </div>
                                        <?php endif; ?>

                                        <!-- ====================================== -->
                                        <!--      KONTEN KHUSUS SIDANG SEMESTER     -->
                                        <!-- ====================================== -->
                                        <?php if ($data_sidang['jenis_sidang'] == 1): ?>
                                            <hr>
                                            <div id="pengampu-wrapper">
                                                <?php if (!empty($dosen_pengampu)): ?>
                                                    <?php foreach ($dosen_pengampu as $index => $nama_pengampu): ?>
                                                        <div class="form-group">
                                                            <label>Pengampu <?php echo $index + 1; ?></label>
                                                            <p><?php echo htmlspecialchars($nama_pengampu); ?></p>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p>Tidak ada dosen pengampu terdaftar.</p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- ====================================== -->
                                        <!--      FIELD JADWAL (UNTUK SEMUA)        -->
                                        <!-- ====================================== -->
                                        <hr>
                                        <div class="form-group">
                                            <label for="modal_ruangan">Ruangan</label>
                                            <input type="text" id="modal_ruangan" name="ruangan" value="<?php echo htmlspecialchars($data_jadwal['ruang_sidang'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_tanggal">Tanggal</label>
                                            <input type="date" id="modal_tanggal" name="tanggal" value="<?php echo !empty($data_jadwal['tanggal_sidang']) ? $data_jadwal['tanggal_sidang']->format('Y-m-d') : ''; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_jam_awal">Jam</label>
                                            <div class="time-input-range">
                                                <input type="time" id="modal_jam_awal" name="jam_awal" value="<?php echo !empty($data_jadwal['jam_sidang']) ? $data_jadwal['jam_sidang']->format('H:i') : ''; ?>">
                                                <span class="time-separator">-</span>
                                                <input type="time" id="modal_jam_akhir" name="jam_akhir" value="<?php echo !empty($data_jadwal['jam_selesai']) ? $data_jadwal['jam_selesai']->format('H:i') : ''; ?>">
                                            </div>
                                        </div>

                                        <div id="form-error" style="color: red; margin-bottom: 10px;"></div>
                                        <div class="form-actions">
                                            <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                                            <button type="submit" class="btn btn-submit">Ubah Penjadwalan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
    </main>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

   <script type="text/javascript">
        // Variabel-variabel ini akan menjadi global dan bisa diakses oleh aDetailSidang.js
        const dosenData = <?php echo $dosen_list_json; ?>;
        const isSidangTA = <?php echo ($data_sidang['jenis_sidang'] == 0) ? 'true' : 'false'; ?>;
    </script>
    
    <!-- Langkah 2: Panggil file JavaScript eksternal Anda -->
    <script src="../../assets/js/aDetailSidang.js"></script>
</body>

</html>