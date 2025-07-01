// Jalankan kode setelah seluruh DOM dimuat 
document.addEventListener('DOMContentLoaded', function () {
   // Ambil elemen toggle sidebar dan sidebar utama
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
    // Ambil kontainer ikon desktop dan mobile
            const desktopIconsContainer = document.getElementById('desktop-icons-container');
            const mobileIconsContainer = document.getElementById('mobile-icons-container');
            if (desktopIconsContainer) {
              // Ambil elemen ikon header
              const headerIcons = desktopIconsContainer.querySelector('.header-icons');
               // Fungsi untuk memindahkan ikon header ke desktop/mobile sesuai lebar layar
                function handleIconPlacement() {
                  if (window.innerWidth <= 992) {
                      // Jika mobile, pindahkan ikon ke mobile container
                    if (mobileIconsContainer && !mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
                    // Jika desktop, pindahkan ikon ke desktop container
                    } else { if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons); }
              }
              // Event klik untuk toggle sidebar di mobile
                if (menuToggle && sidebar) {
                  menuToggle.onclick = () => {
                      
                        menuToggle.classList.toggle("NavSide__toggle--active");
                        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                    };
              }
               // Jalankan penempatan ikon saat pertama kali load
              handleIconPlacement();
                // Jalankan ulang saat window di-resize
                window.addEventListener('resize', handleIconPlacement);
            }
});
 // Jalankan kode search filter tabel setelah DOM siap
document.addEventListener('DOMContentLoaded', function () {
  // Ambil input search dan semua baris tabel
  const searchInput = document.getElementById('searchInput');
  const tableRows = document.querySelectorAll('.table-admin-custom tbody tr.isiTabel');
  // Event saat input search berubah
  searchInput.addEventListener('input', function() {
    const keyword = searchInput.value.toLowerCase();
    // Loop setiap baris, tampilkan jika cocok dengan keyword
    tableRows.forEach(row => {
      const rowText = row.innerText.toLowerCase();
      row.style.display = rowText.includes(keyword) ? '' : 'none';
    });
  });
});