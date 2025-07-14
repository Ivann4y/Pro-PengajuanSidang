<?php 
require "../../control/dosen/dNilaiAkhir_queries.php";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"  
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <link rel="stylesheet" href="../../css/button-styles.css" />
    <link rel="stylesheet" href="../../extra/style.css" />
    <link rel="stylesheet" href="../../assets/css/dNilaiAkhir.css" />
    <title>Dosen - Nilai Akhir</title>
    <style>
      /* Style untuk tab aktif */
      .nav-link.active-student-tab {
        font-weight: bold;
        color: var(--primary-color) !important;
        border-bottom: 2px solid var(--primary-color) !important;
      }
    </style>
  </head>
  <body>

   <div id="NavSide">
      <div id="main-sidebar" class="NavSide__sidebar">
        <div class="NavSide__sidebar-brand">
          <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
        </div>
        <ul class="NavSide__sidebar-nav">
          <li class="NavSide__sidebar-item ">
            <b></b>
            <b></b>
             <a href="dEvaluasiSidang.php">
              <span class="NavSide__sidebar-title fw-semibold">Evaluasi</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
            <b></b>
            <b></b>
            <a href="dDokumenRevisi.php">
              <span class="NavSide__sidebar-title fw-semibold">Dokumen</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
            <b></b>
            <b></b>
            <a href="dNilaiAkhir.php">
              <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
                     <b></b><b></b>
                     <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                 </li>
         </ul>
      </div>

      <div class="NavSide__topbar">
      <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
      </div>

      <main class="NavSide__main-content">
        <div class="col-12">
          <h2 class="text-heading text-black" style="font-weight: 700;">Nilai Akhir - <?= htmlspecialchars($judul) ?></h2>
        </div>
          <h2 class="fs-5 fw-semibold mb-0" style="margin-left: 15px; margin-top: 20px;">
              Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($nomor_kelompok ?? ''); ?>
          </h2><br>
          <div class="container-fluid">
           <div class="row mb-3">
       <div class="col-12">
         <ul class="nav nav-tabs">
            <?php foreach ($mahasiswa as $index => $mhs): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : ''; ?>"
                       href="dNilaiAkhir.php?id_sidang=<?php echo htmlspecialchars($id_sidang); ?>&nim=<?php echo htmlspecialchars($mhs['nim'] ?? ''); ?>">
                       <?php echo htmlspecialchars($mhs['nama_mhs'] ?? 'Mahasiswa ' . ($index + 1)); ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (empty($mahasiswa)): ?>
                <li class="nav-item">
                    <span class="nav-link disabled">Tidak ada mahasiswa dalam kelompok ini</span>
                </li>
            <?php endif; ?>
         </ul>
       </div>
     </div>
     <br>
        <form method="POST" id="penilaianForm">
         <input type="hidden" name="nim" id="currentNimInput" value="<?php echo htmlspecialchars($current_nim ?? ''); ?>">
         
         <div class="row align-items-stretch">
           <div class="col-lg-49 mb-4 d-flex">
   <div class="card flex-fill" id="carddataMahasiswa">
     <div class="card-body card-soft px-4 py-3">
       <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Data Mahasiswa</h3>
       <div class="d-flex flex-wrap gap-1 px-4 py-3">
         
         <div class="section" style="flex: 1 1 200px; margin-left:30px;  color: #333;">
           
           <div class="info-group mb-3">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-id-card"></i>
               <span class="fw-bold">NIM</span>
             </div>
             <div class="value-row text-secondary fw-bold" id="displayNim">
                <?php echo htmlspecialchars($current_nim ?? ''); ?>
             </div>
           </div>
           
           <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-user"></i>
               <span class="fw-bold">Nama</span>
             </div>
             <div class="value-row text-secondary fw-bold" id="displayNama">
                <?php
                    $initial_mhs_name = '';
                    foreach ($mahasiswa as $mhs) {
                        if ($mhs['nim'] == $current_nim) {
                            $initial_mhs_name = $mhs['nama_mhs'];
                            break;
                        }
                    }
                    echo htmlspecialchars($initial_mhs_name ?? '');
                ?>
             </div>
           </div>
         </div>
         
         <div class="section2" style="flex: 1 1 200px; color: #333;">
           
           <div class="info-group mb-3">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-book"></i>
               <span class="fw-bold">Mata Kuliah</span>
             </div>
             <div class="value-row text-secondary fw-bold">
                <?php echo htmlspecialchars($nama_matkul ?? ''); ?>
             </div>
           </div>
           
           <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-user-tie"></i>
               <span class="fw-bold">Dosen Pembimbing</span>
             </div>
             <div class="value-row text-secondary fw-bold">
                 <?php echo htmlspecialchars($dosen_terkait_sidang ?? ''); ?>
             </div>
             <!-- Debug Info -->
             <!-- <div style="font-size: 12px; color: #666; margin-top: 5px;">
                 Debug: jenis_sidang=<?php echo $jenis_sidang; ?>, 
                 id_kelompok=<?php echo $id_kelompok; ?>, 
                 id_matkul=<?php echo $id_matkul; ?>, 
                 dosen_terkait_sidang=<?php echo $dosen_terkait_sidang; ?>
             </div> -->
           </div>
         </div>
       </div>
     </div>
   </div>
</div>
   <div class="col-lg-49 mb-4 d-flex">
     <div class="card flex-fill" id="cardNilai">
       <div class="card-body card-soft px-4 py-3 text-center">
         <h3 class="card-title mb-3 text-black" style="padding:10px ;">Nilai Mahasiswa:</h3>
         <div>
           <input
             type="text"
             class="form-control form-control-lg text-center mx-auto"
             id="nilaiMahasiswa"
             placeholder="--"
             maxlength="1"
             style="cursor:pointer;"
             readonly
           />
         </div>
       </div>
     </div>
   </div>
   
             <div class="col-12 mb-4 d-flex">
               <div class="card flex-fill" id="carddetailPenilaian">

<div class="card-body" id="card-penilaian-body">
     <div class="d-flex justify-content-between align-items-center mb-4">
         <h3 class="card-title text-black mb-0">Detail Penilaian :</h3>
     </div>
    
     <div class="penilaian-container">
         <div class="penilaian-item">
             <label for="nilaiLaporanInput">Nilai laporan :</label> 
             <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan" id="nilaiLaporanInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_dokumen']); ?>"> 
         </div>
         <div class="penilaian-item">
             <label for="materiPresentasiInput">Materi Presentasi :</label> 
             <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi" id="materiPresentasiInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_presentasi']); ?>"> 
         </div>
         <div class="penilaian-item">
             <label for="tanyaJawabInput">Tanya Jawab :</label> 
             <input type="text" class="form-control text-center input-nilai" name="TanyaJawab" id="tanyaJawabInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_tanyajawab']); ?>"> 
         </div>
         <div class="penilaian-item">
             <label for="nilaiProyekInput">Nilai Proyek :</label> 
             <input type="text" class="form-control text-center input-nilai" name="NilaiProyek" id="nilaiProyekInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_proyek']); ?>"> 
         </div>
     </div>

     <div class="penilaian-grid-vertical">
         <label for="nilaiLaporanInput_v">Nilai laporan</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan_v" id="nilaiLaporanInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_dokumen']); ?>"> 
         
         <label for="materiPresentasiInput_v">Materi Presentasi</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi_v" id="materiPresentasiInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_presentasi']); ?>"> 
         
         <label for="tanyaJawabInput_v">Tanya Jawab</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="TanyaJawab_v" id="tanyaJawabInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_tanyajawab']); ?>"> 
         
         <label for="nilaiProyekInput_v">Nilai Proyek</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="NilaiProyek_v" id="nilaiProyekInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_proyek']); ?>"> 
     </div>
</div>
             </div>
           </div>
           
           
             </div>
          <div class="row mt-5 justify-content-between">
           </div>
           <div class="col-12 d-flex justify-content-end">
             <button type="button" class="btn btn-setujui" id="btnKirim"
                onclick="bukaKonfirmasiModalKirim()">
               Kirim
             </button>
           </div>
       </form>
         </div>
     
     </main>
     
   </div>
   
   <div class="modal fade" id="konfirmasiModalKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
       <div class="modal-header border-0 justify-content-center">
                     <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                   </div>
       <div class="modal-body">
         <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah yakin ingin mengirim nilai akhir?</p>
         <div class="d-flex justify-content-between px-5">
           <button type="button" class="btnKonfirmasi  btn-tolak" id="tidakmodal" data-bs-dismiss="modal">Tidak</button>
           <button type="button" class="btnKonfirmasi  btn-setujui" id="iyamodal" onclick="kirimNilaiAkhir()">Iya</button>
         </div>
       </div>
     </div>
   </div>
</div>
<script src="../../assets/js/main.js"></script>

<script type="text/javascript">
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          menuToggle.classList.toggle("NavSide__toggle--active");
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
      }

      let menuItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (menuItems.length > 0) {
        menuItems.forEach(item => {
          item.onclick = function (event) {
            menuItems.forEach(innerItem => {
              innerItem.classList.remove("NavSide__sidebar-item--active");
            });
            this.classList.add("NavSide__sidebar-item--active");
          };
        });
      }
    </script>
<script>
    // Data Mahasiswa dari PHP (dilewatkan sebagai JSON)
    const allMahasiswa = <?php echo json_encode($mahasiswa); ?>;
    const currentSidangId = <?php echo json_encode($id_sidang); ?>;
    const loggedInNomorDosen = <?php echo json_encode($loggedInNomorDosen); ?>; // ID Dosen yang login

    // Status kelengkapan nilai semua mahasiswa oleh dosen ini
    let allStudentsGradedByThisDosen = <?php echo json_encode($allStudentsGradesComplete); ?>; // Initial status from PHP

    document.addEventListener('DOMContentLoaded', function() {
        // Logika saat halaman dimuat pertama kali
        const initialNim = document.getElementById('currentNimInput').value;
        if (initialNim) {
            // Nilai sudah dimuat dari PHP, hitung rata-rata
            calculateAndDisplayAverage();
        }

        // Perbarui status tombol "Kirim" saat halaman dimuat
        updateKirimButtonStatus();

        // Menampilkan pesan SweetAlert jika ada status dari redirect
        <?php if (!empty($display_success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?php echo htmlspecialchars($display_success_message); ?>', // Perbaikan
                confirmButtonText: 'OK'
            });
        <?php elseif (!empty($display_error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: '<?php echo htmlspecialchars($display_error_message); ?>', // Perbaikan
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });

    /**
     * Menghitung rata-rata nilai dari input detail penilaian dan mengonversinya ke nilai huruf.
     * Kemudian menampilkannya di kolom "Nilai Mahasiswa".
     */
    function calculateAndDisplayAverage() {
        // Ambil nilai dari input detail penilaian (prioritaskan input desktop)
        const nilaiLaporan = parseFloat(document.getElementById('nilaiLaporanInput').value);
        const materiPresentasi = parseFloat(document.getElementById('materiPresentasiInput').value);
        const tanyaJawab = parseFloat(document.getElementById('tanyaJawabInput').value);
        const nilaiProyek = parseFloat(document.getElementById('nilaiProyekInput').value);

        let totalScore = 0;
        let count = 0;

        // Hanya sertakan nilai yang valid (angka dan non-negatif) dalam perhitungan
        if (!isNaN(nilaiLaporan) && nilaiLaporan >= 0) {
            totalScore += nilaiLaporan;
            count++;
        }
        if (!isNaN(materiPresentasi) && materiPresentasi >= 0) {
            totalScore += materiPresentasi;
            count++;
        }
        if (!isNaN(tanyaJawab) && tanyaJawab >= 0) {
            totalScore += tanyaJawab;
            count++;
        }
        if (!isNaN(nilaiProyek) && nilaiProyek >= 0) {
            totalScore += nilaiProyek;
            count++;
        }

        // Hitung rata-rata, jika tidak ada nilai valid, set null
        let averageScore = (count > 0) ? (totalScore / count) : null;

        // Konversi rata-rata ke nilai huruf
        let nilaiHuruf = '--';
        if (averageScore !== null) {
            averageScore = Math.round(averageScore); // Bulatkan ke bilangan bulat terdekat
            if (averageScore >= 85) nilaiHuruf = 'A';
            else if (averageScore >= 75) nilaiHuruf = 'B';
            else if (averageScore >= 65) nilaiHuruf = 'C';
            else if (averageScore >= 50) nilaiHuruf = 'D';
            else nilaiHuruf = 'E';
        }
        
        document.getElementById('nilaiMahasiswa').value = nilaiHuruf;
    }

    /**
     * Dipicu saat tombol "Iya" diklik di modal konfirmasi nilai sementara.
     * Memuat ulang nilai mahasiswa dan memperbarui tampilan.
     */
    function checkAndFillGrades() {
        // Nilai sudah dimuat dari PHP, tidak perlu load ulang
        // Hanya tutup modal
        TutupKonfirmasiModal();
    }

    /**
     * Membuka modal konfirmasi sebelum mengirim nilai akhir.
     * Melakukan validasi sederhana pada input nilai untuk mahasiswa aktif.
     */
    function bukaKonfirmasiModalKirim() {
        // Validasi ini hanya memastikan nilai mahasiswa AKTIF sudah terisi.
        // Validasi bahwa SEMUA mahasiswa sudah dinilai dilakukan di PHP (allStudentsGradedByThisDosen).
        const nilaiLaporan = document.getElementById('nilaiLaporanInput').value;
        const materiPresentasi = document.getElementById('materiPresentasiInput').value;
        const tanyaJawab = document.getElementById('tanyaJawabInput').value;
        const nilaiProyek = document.getElementById('nilaiProyekInput').value;

        // Periksa apakah semua kolom nilai utama untuk mahasiswa aktif sudah terisi
        if (!nilaiLaporan || !materiPresentasi || !tanyaJawab || !nilaiProyek) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap!',
                text: 'Harap isi semua kolom penilaian (nilai laporan, materi presentasi, tanya jawab, dan nilai proyek) untuk mahasiswa ini sebelum mengirim.',
                confirmButtonText: 'OK'
            });
            return; // Hentikan fungsi jika validasi gagal
        }

        // Jika validasi sukses, tampilkan modal konfirmasi kirim
        var konfirmasiModalKirim = new bootstrap.Modal(document.getElementById('konfirmasiModalKirim'));
        konfirmasiModalKirim.show();
    }

    /**
     * Mengirim form penilaian setelah konfirmasi dari pengguna.
     */
    function kirimNilaiAkhir() {
        document.getElementById('penilaianForm').submit(); // Submit form
    }

    /**
     * Memperbarui status tombol "Kirim" berdasarkan variabel allStudentsGradedByThisDosen dari PHP.
     */
    function updateKirimButtonStatus() {
        const btnKirim = document.getElementById('btnKirim');
        if (btnKirim) {
            // Tombol aktif jika allStudentsGradedByThisDosen adalah TRUE DAN ada mahasiswa di kelompok
            btnKirim.disabled = !(allStudentsGradedByThisDosen && allMahasiswa.length > 0);
            
            // Opsional: Tambahkan tooltip informatif
            if (btnKirim.disabled) {
                if (allMahasiswa.length === 0) {
                    btnKirim.title = "Tidak ada mahasiswa dalam kelompok ini untuk dinilai.";
                } else {
                    btnKirim.title = "Harap selesaikan penilaian untuk semua mahasiswa dalam kelompok ini.";
                }
            } else {
                btnKirim.title = ""; // Hapus tooltip jika tombol aktif
            }
        }
    }
</script>

<!-- SCRIPT NILAI SEMENTARA -->
<script>
(function() {
    var id_sidang = <?php echo json_encode($id_sidang); ?>;
    var mahasiswaList = <?php echo json_encode($mahasiswa); ?>;
    var btnKirim = document.getElementById('btnKirim');

    // Fungsi cek semua nilai sudah diisi
    function cekSemuaNilaiTerisi() {
        if (!mahasiswaList || mahasiswaList.length === 0) return false;
        for (var i = 0; i < mahasiswaList.length; i++) {
            var nim = mahasiswaList[i].nim;
            var storageKey = 'nilaiSementara_' + id_sidang + '_' + nim;
            var dataStr = sessionStorage.getItem(storageKey);
            if (!dataStr) return false;
            try {
                var data = JSON.parse(dataStr);
                if (!data.n_dokumen || !data.n_presentasi || !data.n_tanyajawab || !data.n_proyek) {
                    return false;
                }
            } catch(e) { return false; }
        }
        return true;
    }

    // Fungsi update status button
    function updateBtnKirim() {
        if (btnKirim) {
            btnKirim.disabled = !cekSemuaNilaiTerisi();
        }
    }

    // Set event listener ke semua input pada halaman ini
    var inputLaporan = document.getElementById('nilaiLaporanInput');
    var inputPresentasi = document.getElementById('materiPresentasiInput');
    var inputTanyaJawab = document.getElementById('tanyaJawabInput');
    var inputProyek = document.getElementById('nilaiProyekInput');
    function simpanNilaiSementara() {
        var nim = <?php echo json_encode($current_nim); ?>;
        var storageKey = 'nilaiSementara_' + id_sidang + '_' + nim;
        var data = {
            n_dokumen: inputLaporan ? inputLaporan.value : '',
            n_presentasi: inputPresentasi ? inputPresentasi.value : '',
            n_tanyajawab: inputTanyaJawab ? inputTanyaJawab.value : '',
            n_proyek: inputProyek ? inputProyek.value : ''
        };
        sessionStorage.setItem(storageKey, JSON.stringify(data));
        updateBtnKirim();
    }
    if (inputLaporan) inputLaporan.addEventListener('input', simpanNilaiSementara);
    if (inputPresentasi) inputPresentasi.addEventListener('input', simpanNilaiSementara);
    if (inputTanyaJawab) inputTanyaJawab.addEventListener('input', simpanNilaiSementara);
    if (inputProyek) inputProyek.addEventListener('input', simpanNilaiSementara);

    // Cek juga saat halaman dimuat
    window.addEventListener('DOMContentLoaded', function() {
        // Isi nilai dari sessionStorage jika ada (fungsi lama)
        var nim = <?php echo json_encode($current_nim); ?>;
        var storageKey = 'nilaiSementara_' + id_sidang + '_' + nim;
        var dataStr = sessionStorage.getItem(storageKey);
        if (dataStr) {
            try {
                var data = JSON.parse(dataStr);
                if (inputLaporan && data.n_dokumen !== undefined) inputLaporan.value = data.n_dokumen;
                if (inputPresentasi && data.n_presentasi !== undefined) inputPresentasi.value = data.n_presentasi;
                if (inputTanyaJawab && data.n_tanyajawab !== undefined) inputTanyaJawab.value = data.n_tanyajawab;
                if (inputProyek && data.n_proyek !== undefined) inputProyek.value = data.n_proyek;
            } catch(e) {}
        }
        updateBtnKirim();
    });
})();
</script>
  </body>
</html>