<?php
require "../../koneksi/koneksiAndrew.php";

session_start();
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID Sidang tidak valid.");
} 
$id_sidang = (int)$_GET['id'];

// Variabel penampung
$data_nim = [];
$nama_prodi = 'N/A';
$data_sidang = [];
$data_mahasiswa = [];
$dosen_pembimbing = null;
$dosen_penguji = [];
$dosen_pengampu = [];
$data_matkul = null;

// 2. Query utama dengan CAST untuk jenis_sidang
$sql_utama = "SELECT 
                s.id_sidang, s.judul, 
                CASE 
                    WHEN s.status_sidang = 1 THEN 'Disetujui'
                    WHEN s.status_sidang = 0 THEN 'Ditolak'
                    ELSE 'Menunggu'
                END AS status_sidang_text, 
                CAST(s.jenis_sidang AS INT) AS jenis_sidang, -- Mengubah binary ke integer
                s.id_kelompok
              FROM Sidang s
              WHERE s.id_sidang = ?";
$params_utama = array($id_sidang);
$stmt_utama = sqlsrv_query($conn, $sql_utama, $params_utama);
if ($stmt_utama === false) {
    die("Error pada query utama: " . print_r(sqlsrv_errors(), true));
}
$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) {
    die("Error: Data Sidang dengan ID $id_sidang tidak ditemukan.");
}

// --- Query Terpisah untuk Jadwal (lebih aman dari NULL) ---
$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
$data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC);
if (!$data_jadwal) {
    $data_jadwal = [];
} // Jika tidak ada jadwal, buat array kosong

// 3. Query mahasiswa
$id_kelompok = $data_sidang['id_kelompok'];
$sql_mahasiswa = "SELECT m.prodi FROM Mahasiswa m
                  JOIN Kelompok_Mahasiswa km ON m.nim = km.nim
                  WHERE km.id_kelompok = ? AND m.prodi IS NOT NULL";
$params_mahasiswa = array($id_kelompok);
$stmt_mahasiswa = sqlsrv_query($conn, $sql_mahasiswa, $params_mahasiswa);
if ($stmt_mahasiswa === false) {
    die("Error pada query mahasiswa: " . print_r(sqlsrv_errors(), true));
}
// Ambil nama prodi dari baris pertama, karena semua anggota kelompok prodinya sama
if ($row = sqlsrv_fetch_array($stmt_mahasiswa, SQLSRV_FETCH_ASSOC)) {
    $nama_prodi = $row['prodi'];
}
$nim_str = implode(', ', $data_nim);

// 4. Logika kondisional
if ($data_sidang['jenis_sidang'] == 0) { // Asumsi 0 = TA
    // Ambil Dosen Pembimbing
    $sql_pembimbing = "SELECT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ?";
    $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, array($id_kelompok));
    if ($stmt_pembimbing) {
        $dosen_pembimbing = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC);
    }

    // Ambil Dosen Penguji
    $sql_penguji = "SELECT d.nama_dosen 
                FROM Dosen d 
                JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen 
                WHERE p.id_sidang = ? AND TRIM(p.peran_dosen) = 'Penguji'";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, array($id_sidang));
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosen_penguji[] = $row['nama_dosen'];
        }
    }
} elseif ($data_sidang['jenis_sidang'] == 1) { // Asumsi 1 = Semester
    // [FIX] Menggunakan TOP 1
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul, mk.id_matkul FROM MataKuliah mk
                   JOIN Detail_Sidang ds ON mk.id_matkul = ds.id_matkul
                   WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
    if ($stmt_matkul) {
        $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
    }

    if ($data_matkul) {
        $id_matkul = $data_matkul['id_matkul'];
        $sql_pengampu = "SELECT d.nama_dosen FROM Dosen d JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen WHERE pk.id_matkul = ?";
        $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, array($id_matkul));
        if ($stmt_pengampu) {
            while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
                $dosen_pengampu[] = $row['nama_dosen'];
            }
        }
    }
}
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
                                                $penguji_list = !empty($dosen_penguji) ? $dosen_penguji : ['']; // Jika kosong, buat 1 form
                                                foreach ($penguji_list as $index => $nama_penguji):
                                                ?>
                                                    <div class="form-group" id="penguji-form-<?php echo $index + 1; ?>">
                                                        <label for="modal_penguji<?php echo $index + 1; ?>">Penguji <?php echo $index + 1; ?></label>
                                                        <div class="input-with-buttons">
                                                            <input type="text" id="modal_penguji<?php echo $index + 1; ?>" name="penguji_nama[]" placeholder="Nama Penguji <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($nama_penguji); ?>">
                                                            <div class="input-with-percent">
                                                                <input type="number" name="penguji_bobot[]" class="form-control-bobot" min="0" placeholder="Bobot">
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
        function openModal() {
            // Reset form sebelum membuka modal
            document.getElementById('formDalamModal').reset();
            document.getElementById('form-error').textContent = ''; // Kosongkan pesan error
            // Buka modal
            var myModal = new bootstrap.Modal(document.getElementById('penjadwalanSidangModal'), {
                keyboard: false
            });
            myModal.show();
        }
        // Skrip untuk toggle sidebar dan active menu item
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        if (menuToggle && sidebar) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        if (listItems.length > 0) {
            for (let i = 0; i < listItems.length; i++) {
                listItems[i].onclick = function(event) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                };
            }
        }


        let pengujiCount = 1;

        // --- FUNGSI BARU UNTUK PENGUJI DINAMIS (PENDEKATAN GLOBAL) ---

        // Fungsi untuk menata ulang nomor urut semua elemen penguji
        function reorderPenguji() {
            const wrapper = document.getElementById('penguji-wrapper');
            const allPengujiForms = wrapper.querySelectorAll('.form-group'); // Target semua .form-group di dalam wrapper

            allPengujiForms.forEach((form, index) => {
                const newNumber = index + 1;

                // Perbarui ID dari form-group itu sendiri
                form.id = `penguji-form-${newNumber}`;

                // Perbarui label
                const label = form.querySelector('label');
                if (label) {
                    label.htmlFor = `modal_penguji${newNumber}`;
                    label.textContent = `Penguji ${newNumber}`;
                }

                // Perbarui input nama
                const inputNama = form.querySelector('input[name="penguji_nama[]"]');
                if (inputNama) {
                    inputNama.id = `modal_penguji${newNumber}`;
                    inputNama.placeholder = `Nama Penguji ${newNumber}`;
                }
            });
        }

        // Fungsi untuk menambah baris penguji baru
        function addPenguji() {
            const wrapper = document.getElementById('penguji-wrapper');
            const newPengujiDiv = document.createElement('div');
            newPengujiDiv.className = 'form-group';

            const newIndex = wrapper.children.length + 1; // Hitung elemen berikutnya

            // Template HTML untuk baris baru
            newPengujiDiv.innerHTML = `
        <label for="modal_penguji${newIndex}">Penguji ${newIndex}</label>
        <div class="input-with-buttons">
            <input type="text" id="modal_penguji${newIndex}" name="penguji_nama[]" placeholder="Nama Penguji ${newIndex}" />
            <div class="input-with-percent">
                <input type="number" name="penguji_bobot[]" class="form-control-bobot" min="0" placeholder="Bobot" />
                <span class="percent-sign">%</span>
            </div>
        </div>
    `;
            wrapper.appendChild(newPengujiDiv);

            // Tidak perlu panggil reorder di sini karena kita sudah menghitung index yang benar
        }

        // Fungsi untuk menghapus baris penguji TERAKHIR
        function removePenguji() {
            const wrapper = document.getElementById('penguji-wrapper');
            // Cek jika ada lebih dari satu elemen penguji
            if (wrapper.children.length > 1) {
                // Hapus elemen terakhir dari wrapper
                wrapper.lastElementChild.remove();
            } else {
                // Jika hanya sisa satu, jangan dihapus, tapi kosongkan nilainya
                const lastForm = wrapper.firstElementChild;
                if (lastForm) {
                    const inputNama = lastForm.querySelector('input[name="penguji_nama[]"]');
                    const inputBobot = lastForm.querySelector('input[name="penguji_bobot[]"]');
                    if (inputNama) inputNama.value = '';
                    if (inputBobot) inputBobot.value = '';
                }
            }
        }

        // Panggil reorder saat halaman pertama kali dimuat untuk memastikan penomoran awal sudah benar
        document.addEventListener('DOMContentLoaded', function() {
            reorderPenguji();
        });

        function incrementValue(inputId) {
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                let currentValue = parseInt(inputElement.value, 10);
                if (isNaN(currentValue)) currentValue = 0;
                inputElement.value = currentValue + 1;
            }
        }

        function decrementValue(inputId) {
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                let currentValue = parseInt(inputElement.value, 10);
                if (isNaN(currentValue)) currentValue = 0;
                const minValue = parseInt(inputElement.min, 10);
                inputElement.value = Math.max(minValue || 0, currentValue - 1);
            }
        }

      // --- Skrip Validasi Form ---
      // Ganti seluruh blok addEventListener Anda dengan ini

document.getElementById('formDalamModal').addEventListener('submit', function(event) {
    event.preventDefault(); 

    const errorBox = document.getElementById("form-error");
    errorBox.textContent = ""; 
    
    let isValid = true;
    let errorMessage = "";

    // ... (kode validasi Anda tetap di sini, tidak perlu diubah) ...
    const pengujiInputs = document.querySelectorAll('input[name="penguji_nama[]"]');
    const isSidangTA = <?php echo ($data_sidang['jenis_sidang'] == 0) ? 'true' : 'false'; ?>;

    // Validasi penguji hanya untuk sidang TA
    if (isSidangTA) {
        pengujiInputs.forEach((input, index) => {
            if (isValid && input.value.trim() === "") {
                errorMessage = `Nama penguji ${index + 1} tidak boleh kosong!`;
                isValid = false;
            }
        });
    }

    const ruangan = document.getElementById("modal_ruangan").value.trim();
    const tanggal = document.getElementById("modal_tanggal").value;
    const jamAwal = document.getElementById("modal_jam_awal").value;
    const jamAkhir = document.getElementById("modal_jam_akhir").value;

    if (isValid && ruangan === "") {
        errorMessage = "Ruangan harus diisi!";
        isValid = false;
    } else if (isValid && tanggal === "") {
        errorMessage = "Tanggal harus dipilih!";
        isValid = false;
    } else if (isValid && (jamAwal === "" || jamAkhir === "")) {
        errorMessage = "Jam awal dan jam akhir harus diisi!";
        isValid = false;
    } else if (isValid && jamAkhir <= jamAwal) {
        errorMessage = "Jam akhir harus setelah jam awal!";
        isValid = false;
    }

    if (!isValid) {
        errorBox.textContent = errorMessage;
        return;
    }
    
    // --- BAGIAN BARU: MENGIRIM DATA DENGAN AJAX (FETCH) ---

    // Kumpulkan semua data dari form
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Nonaktifkan tombol saat proses pengiriman
    submitButton.disabled = true;
    submitButton.textContent = 'Menyimpan...';

    fetch('proses_ubah_jadwal.php', { // Target ke file PHP baru
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Ubah response menjadi JSON
    .then(data => {
        // Sembunyikan modal
        var myModalEl = document.getElementById('penjadwalanSidangModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide();

        if (data.status === 'success') {
            // Tampilkan notifikasi sukses yang sesungguhnya
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Muat ulang halaman untuk melihat perubahan
                }
            });
        } else {
            // Tampilkan notifikasi error dari server
            Swal.fire({
                title: 'Gagal!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ff5f5f'
            });
        }
    })
    .catch(error => {
        // Tangani error koneksi atau parsing JSON
        console.error('Error:', error);
        Swal.fire({
            title: 'Oops!',
            text: 'Terjadi kesalahan saat menghubungi server.',
            icon: 'error'
        });
    })
    .finally(() => {
        // Aktifkan kembali tombolnya setelah selesai
        submitButton.disabled = false;
        submitButton.textContent = 'Ubah Penjadwalan';
    });
});
    </script>
</body>

</html>