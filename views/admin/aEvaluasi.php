
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Sidang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="../../css/button-styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../assets/css/aEvaluasi.css?v=<?= time() ?>">
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
                    <a href="aDetailSidangTA.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="aEvaluasi"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
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
        <main class="NavSide__main-content">

          <div class="d-flex justify-content-between align-items-center">
      <div>
         <h2 class="text-heading text-black mb-5" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
        
 <ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" href="#">mahasiswa1</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">mahasiswa2</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">mahasiswa3</a>
  </li>
</ul>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav-link");

    navLinks.forEach(function (link) {
      link.addEventListener("click", function (e) {
        e.preventDefault(); // biar gak reload
        navLinks.forEach(l => l.classList.remove("active")); // hapus semua active
        this.classList.add("active"); // tambahkan ke yang diklik
      });
    });
  });
</script>



        <h5 class="mt-5">Catatan Perbaikan</h5>
      </div>
      <span class="badge-custom">Status Revisi : Disetujui</span>
    </div>

    <!-- <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <strong>Timotius Victory, S.Kom, M.Kom - Penguji</strong>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
    </div> -->
 
    <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <h6 class= "card-h">Dr. Rida Indah Fariani, S.Kom, M.Kom – Pembimbing</h6>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
      <div class="approved-badge">Telah Menyetujui</div>
    </div>


    <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <h6 class= "card-h">Yosep Setiawan, S.Kom, M.Kom - Penguji</h6>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
      <div class="approved-badge">Telah Menyetujui</div>
    </div>

        <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <h6 class= "card-h">Yosep Setiawan, S.Kom, M.Kom - Penguji</h6>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
      <div class="approved-badge">Telah Menyetujui</div>
    </div>
<div class="revision-card shadow-sm">
  <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
  <div class="revision-cardUp">
  <p class="text-center text-muted small mt-2" ><b>berkas_laporan_kel.pdf</b></p>

  <div class="text-center mt-3">
    
    <a href="#" target="_blank" style="text-decoration:none">
       <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8d99ae" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.707 0H9.293zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 10.5a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 12a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>    
    <p class="text-center text-muted small mt-2" style="margin-top: 2rem " id="upload-prompt-text">Unduh berkas revisi dengan format pdf, docx, pptx, dan zip</p>
    </a>
    



</div>

  </div>
 <br/>
 <!-- HTML kosong -->
<div id="downloadContainer"></div>

<script>
    // Buat elemen div dengan class bootstrap
    const containerDiv = document.createElement("div");
    containerDiv.className = "d-flex justify-content-end mt-4";

    // Buat elemen a (link unduhan)
    const downloadLink = document.createElement("a");
    downloadLink.href = "aNilaiAkhir.php";
    downloadLink.className = "btn-custom-primaryUnd";
    downloadLink.id = "btnUnduh";
    downloadLink.setAttribute("download", ""); // penting! agar jadi tombol unduh
    downloadLink.textContent = "Unduh";

    // Masukkan link ke dalam div
    containerDiv.appendChild(downloadLink);

    // Tambahkan div ke elemen di halaman (misal ke <div id="downloadContainer">)
    document.getElementById("downloadContainer").appendChild(containerDiv);
</script>


<!-- 
</div>
            <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
          
            

            <div class="text-center mt-3"><p id="fileNameDisplay" class="fw-bold mb-0"></p></div>
           -->

      
             <!-- <div class="mt-4">
        <button type="button" id="btnKembali" class="btn btn-custom-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
            Kembali
        </button>
    </div> -->
          <div class="button-group-bottom mt-4">
                <button  id= "btnKembali"class="btn-custom-primary" onclick="location.href= 'aDaftarSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
</button>
    
        
    </div>

  

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 p-4">
          <div class="d-flex justify-content-between align-items-start">
            <h4 id="modalDetailLabel" class="fw-bold text-primary">Detail Catatan Perbaikan</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body pt-3 pb-2">
            <p>
              Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik,
              termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali penggunaan bahasa.
              Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa ilmiah yang baku dan konsisten.
              Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik,
              termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali penggunaan bahasa.
              Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa ilmiah yang baku dan konsisten.
            </p>
          </div>
          <div class="modal-footer border-0 justify-content-end">
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
          
      </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Sidebar Toggle Logic
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function () {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // Sidebar Active Item Logic (no change needed here as it's already functional)
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function () {
                if(!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }
    </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  
<script>


  <?php
  if (!empty($pesan) && strpos(strtolower($pesan), 'sukses') !== false):
  ?>
  <script>
      Swal.fire({
          title: 'Berhasil!',
          text: 'Dokumen Anda telah berhasil diunggah.',
          icon: 'success',
          confirmButtonColor: '#007bff'
      });
  </script>
  <?php endif; ?>


  
  
</body>
</html>