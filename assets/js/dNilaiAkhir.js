// Menjalankan skrip setelah seluruh konten halaman HTML (DOM) selesai dimuat.
document.addEventListener('DOMContentLoaded', function () {
  
  // -- INISIALISASI MODAL KONFIRMASI AWAL --
  // Mencari modal dengan ID 'konfirmasiModal' dan langsung menampilkannya saat halaman dimuat.
  // Modal ini kemungkinan bertanya kepada pengguna apakah mereka ingin mengisi nilai secara otomatis.
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
  modal.show();

   // -- INISIALISASI TOOLTIP BOOTSTRAP --
  // Mencari semua elemen yang memiliki atribut 'data-bs-toggle="tooltip"' untuk mengaktifkan fungsionalitas tooltip dari Bootstrap.
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
    // -- LOGIKA UNTUK TOGGLE SIDEBAR (NAVIGASI SAMPING) --
  const menuToggle = document.querySelector(".NavSide__toggle"); // Tombol untuk membuka/menutup sidebar di tampilan mobile.
  const sidebar = document.getElementById("main-sidebar");// Elemen sidebar itu sendiri.
  if (menuToggle && sidebar) {
      // Menambahkan event listener 'onclick' pada tombol toggle.
    menuToggle.onclick = function() {
          // Menambah atau menghapus kelas 'NavSide__sidebar--active-mobile' pada sidebar untuk menampilkannya/menyembunyikannya.
      sidebar.classList.toggle("NavSide__sidebar--active-mobile"); 
        // Menambah atau menghapus kelas 'NavSide__toggle--active' pada tombol itu sendiri (misalnya untuk mengubah ikon hamburger menjadi 'X').
        menuToggle.classList.toggle("NavSide__toggle--active"); 
    };
  }
  
  // -- LOGIKA UNTUK ITEM SIDEBAR YANG AKTIF --
  // Mengambil semua item menu di dalam sidebar.
  const listItems = document.querySelectorAll(".NavSide__sidebar-item");
  listItems.forEach(item => {
     // Menambahkan event 'onclick' pada setiap item menu.
    item.onclick = function () {
      // Pertama, hapus kelas 'NavSide__sidebar-item--active' dari semua item menu.
      listItems.forEach(li => li.classList.remove("NavSide__sidebar-item--active"));
       // Kemudian, tambahkan kelas tersebut hanya pada item yang baru saja diklik.
      this.classList.add("NavSide__sidebar-item--active");
    };
  });
   // -- LOGIKA UNTUK INPUT NILAI --
  // Mengambil semua elemen input yang memiliki kelas 'input-nilai'.
  const detailInputs = document.querySelectorAll('.input-nilai');
  
  detailInputs.forEach(function (input) {
      // Setiap kali ada perubahan pada input, panggil fungsi untuk menghitung rata-rata.
    input.addEventListener('input', hitungRataRataDanSetNilai);
     // 2. Hapus angka nol di depan jika angka lebih dari satu digit (misal: '090' menjadi '90').
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value.length > 1 && this.value.startsWith('0')) {
        this.value = this.value.replace(/^0+/, '');
      }
        // 3. Batasi nilai maksimal menjadi 100.
      if (parseInt(this.value, 10) > 100) {
        this.value = '100';
      }
    });
  });
  // -- SINKRONISASI INPUT --
  // Memastikan pasangan input selalu memiliki nilai yang sama. Berguna jika ada input yang sama untuk tampilan desktop dan mobile.
  syncInputs('nilaiLaporan', 'nilaiLaporan_v');
  syncInputs('MateriPresentasi', 'MateriPresentasi_v');
  syncInputs('Penyampaian', 'Penyampaian_v');
  syncInputs('NilaiProyek', 'NilaiProyek_v');

}); 
/**
 * Fungsi untuk menampilkan tooltip 'pensil' secara manual.
 * Tooltip ini akan muncul selama 5 detik untuk memberi petunjuk kepada pengguna.
 */
function showTooltipPensil() {
  const tooltipTrigger = document.querySelector('[data-bs-toggle="tooltip"]');
  if (tooltipTrigger) {
    const tooltipInstance = bootstrap.Tooltip.getInstance(tooltipTrigger) || new bootstrap.Tooltip(tooltipTrigger);
    tooltipInstance.show();
    setTimeout(() => tooltipInstance.hide(), 5000);
  }
}
/**
 * Fungsi utilitas untuk menyinkronkan nilai antara dua input.
 * @param {string} name1 - Atribut 'name' dari input pertama.
 * @param {string} name2 - Atribut 'name' dari input kedua.
 */
function syncInputs(name1, name2) {
  const input1 = document.getElementsByName(name1)[0];
  const input2 = document.getElementsByName(name2)[0];
  if (input1 && input2) {
     // Jika input1 diubah, perbarui nilai input2.
    input1.addEventListener('input', () => { input2.value = input1.value; });
     // Jika input2 diubah, perbarui nilai input1.
    input2.addEventListener('input', () => { input1.value = input2.value; });
  }
}
/**
 * Fungsi untuk mengarahkan pengguna ke halaman 'dDaftarSidang.php'.
 */
function pindahKeHalamanDaftarSidang() {
  window.location.href = "dDaftarSidang.php";
}
/**
 * Fungsi untuk membuka modal konfirmasi secara manual.
 */
function bukaKonfirmasiModal() {
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
  modal.show();
}
/**
 * Fungsi yang dijalankan ketika pengguna memilih untuk menutup modal konfirmasi awal
 * tanpa mengisi nilai secara otomatis.
 */
function TutupKonfirmasiModal() {
  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
  modal.hide();
  // Setelah modal ditutup, tunggu 300ms, lalu fokuskan kursor ke input nilai akhir
  // dan tampilkan tooltip pensil sebagai petunjuk.
  setTimeout(() => {
    const input = document.getElementById("nilaiMahasiswa");
    input.focus();
    showTooltipPensil();
  }, 300);
}
/**
 * Fungsi untuk mengisi semua form nilai dengan data contoh secara otomatis.
 * Biasanya dipanggil dari tombol 'Ya' pada modal konfirmasi awal.
 */
function isiNilaiAkhir() {
  document.getElementsByName("nilaiLaporan")[0].value = "90";
  document.getElementsByName("nilaiLaporan_v")[0].value = "90";
  document.getElementsByName("MateriPresentasi")[0].value = "85";
  document.getElementsByName("MateriPresentasi_v")[0].value = "85";
  document.getElementsByName("Penyampaian")[0].value = "88";
  document.getElementsByName("Penyampaian_v")[0].value = "88";
  document.getElementsByName("NilaiProyek")[0].value = "92";
  document.getElementsByName("NilaiProyek_v")[0].value = "92";
 // Memanggil fungsi hitung untuk mengkalkulasi nilai akhir (huruf).
  hitungRataRataDanSetNilai(); 
 // Menutup modal dan menampilkan tooltip pensil.
  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
  modal.hide();
  showTooltipPensil();
}
/**
 * Fungsi utama untuk menghitung nilai rata-rata dari empat komponen nilai
 * dan mengonversinya menjadi nilai huruf (A, B, C, D, E).
 */
function hitungRataRataDanSetNilai() {
  // Mengambil elemen input dari setiap komponen nilai.
  const nilaiLaporanEl = document.getElementsByName("nilaiLaporan")[0];
  const materiPresentasiEl = document.getElementsByName("MateriPresentasi")[0];
  const penyampaianEl = document.getElementsByName("Penyampaian")[0];
  const nilaiProyekEl = document.getElementsByName("NilaiProyek")[0];
  // Jika salah satu input kosong, kosongkan nilai akhir dan hentikan fungsi.
  const inputs = [
    nilaiLaporanEl.value,
    materiPresentasiEl.value,
    penyampaianEl.value,
    nilaiProyekEl.value
  ];
  if (inputs.some(val => val === "")) {
    document.getElementById("nilaiMahasiswa").value = ""; 
    return;
  }
    // Mengubah nilai string dari input menjadi angka (float).
  const nilaiLaporan = parseFloat(nilaiLaporanEl.value);
  const materiPresentasi = parseFloat(materiPresentasiEl.value);
  const penyampaian = parseFloat(penyampaianEl.value);
  const nilaiProyek = parseFloat(nilaiProyekEl.value);
   // Menghitung rata-rata dari keempat nilai.
  const rataRata = (nilaiLaporan + materiPresentasi + penyampaian + nilaiProyek) / 4;
 // Menentukan nilai huruf berdasarkan rentang nilai rata-rata.
  let nilaiHuruf = "";
  if (rataRata >= 85) { nilaiHuruf = "A"; } 
  else if (rataRata >= 70) { nilaiHuruf = "B"; } 
  else if (rataRata >= 60) { nilaiHuruf = "C"; } 
  else if (rataRata >= 40) { nilaiHuruf = "D"; } 
  else { nilaiHuruf = "E"; }
   // Menetapkan nilai huruf yang sudah dihitung ke dalam input 'nilaiMahasiswa'.
  document.getElementById("nilaiMahasiswa").value = nilaiHuruf;
}
/**
 * Fungsi untuk menampilkan modal konfirmasi sebelum mengirim nilai akhir.
 */
function bukaKonfirmasiModalKirim() {
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModalKirim'));
  modal.show();
}
/**
 * Fungsi yang dijalankan saat pengguna mengonfirmasi pengiriman nilai.
 * Menggunakan SweetAlert2 untuk menampilkan notifikasi.
 */
function kirimNilaiAkhir() {
  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModalKirim'));
  // Mengambil semua nilai yang akan dikirim.
  const nilaiMahasiswa = document.getElementById("nilaiMahasiswa").value;
  const nilaiLaporan = document.getElementsByName("nilaiLaporan")[0].value;
  const materiPresentasi = document.getElementsByName("MateriPresentasi")[0].value;
  const penyampaian = document.getElementsByName("Penyampaian")[0].value;
  const nilaiProyek = document.getElementsByName("NilaiProyek")[0].value;
  // Validasi: Pastikan semua kolom nilai sudah terisi.
  if (nilaiMahasiswa === "" || nilaiLaporan === "" || materiPresentasi === "" || penyampaian === "" || nilaiProyek === "") {
     // Jika ada yang kosong, tampilkan pesan error dengan SweetAlert.
    Swal.fire({
      title: 'Semua nilai harus diisi sebelum mengirim!',
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    }).then(() => {
      if(modal) modal.hide(); // Tutup modal konfirmasi setelah menampilkan error.
    });
  } else {
    // Jika semua nilai terisi, tutup modal dan tampilkan pesan sukses.
     // NOTE: Kode ini hanya menampilkan notifikasi, belum ada logika pengiriman data ke server (misalnya via fetch atau AJAX).
    if(modal) modal.hide();
    Swal.fire({
      title: 'Nilai akhir telah dikirim.',
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
  }
}