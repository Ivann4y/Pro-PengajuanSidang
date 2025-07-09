// Mahasiswa Pengajuan JavaScript
// Tidak ada lagi AJAX/fetch, hanya search/filter pada elemen HTML yang sudah di-render PHP

// Search/filter tetap bisa digunakan pada elemen yang sudah ada

document.addEventListener("DOMContentLoaded", function () {
  // Setup search functionality jika ada
  const searchInput = document.getElementById("search-pengajuan");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase();
      const cards = document.querySelectorAll(".pengajuan-card");
      cards.forEach((card) => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(query) ? "block" : "none";
      });
    });
  }

  // Tambahkan filter lain jika diperlukan, langsung pada elemen HTML
});

// Fungsi-fungsi berikut tetap bisa digunakan jika ingin redirect ke halaman edit/detail
function editPengajuan(nomor_kelompok, tahun_ajaran, jenis_sidang, id_matkul) {
  window.location.href = `views/mahasiswa/mEditPengajuan.php?nomor_kelompok=${nomor_kelompok}&tahun_ajaran=${tahun_ajaran}&jenis_sidang=${jenis_sidang}&id_matkul=${id_matkul}`;
}

function submitPengajuan(
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  if (
    !confirm(
      "Apakah Anda yakin ingin submit pengajuan ini? Setelah submit, pengajuan tidak dapat diedit lagi."
    )
  ) {
    return;
  }
  window.location.href = `views/mahasiswa/mEditPengajuan.php?nomor_kelompok=${nomor_kelompok}&tahun_ajaran=${tahun_ajaran}&jenis_sidang=${jenis_sidang}&id_matkul=${id_matkul}`;
}

// showError dan showSuccess bisa dihapus jika tidak dipakai, atau tetap dibiarkan untuk kebutuhan lain
