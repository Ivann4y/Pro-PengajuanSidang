document.addEventListener('DOMContentLoaded', function () {
    
    // Logika untuk sidebar toggle (menu buka-tutup)
    const menuToggle = document.querySelector(".NavSide__toggle");
    const sidebar = document.getElementById("main-sidebar");
    if (menuToggle && sidebar) {
        menuToggle.onclick = () => {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // Logika untuk penempatan ikon yang responsif
    const desktopIconsContainer = document.getElementById('desktop-icons-container');
    const mobileIconsContainer = document.getElementById('mobile-icons-container');
    if (desktopIconsContainer) {
        const headerIcons = desktopIconsContainer.querySelector('.header-icons');
        
        const handleIconPlacement = () => {
            // Pindahkan ikon ke topbar jika layar kecil
            if (window.innerWidth <= 992 && mobileIconsContainer) {
                if (!mobileIconsContainer.contains(headerIcons)) {
                    mobileIconsContainer.appendChild(headerIcons);
                }
            // Kembalikan ikon ke header jika layar besar
            } else {
                if (!desktopIconsContainer.contains(headerIcons)) {
                    desktopIconsContainer.appendChild(headerIcons);
                }
            }
        };

        // Jalankan saat halaman dimuat dan saat ukuran jendela diubah
        handleIconPlacement();
        window.addEventListener('resize', handleIconPlacement);
    }
});