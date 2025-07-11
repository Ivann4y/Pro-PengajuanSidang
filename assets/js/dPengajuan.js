// Untuk responsifitas sidebar
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

//Untuk filter pencarian
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchValue = this.value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('search', searchValue);
                //untuk search dr halaman pertama
                currentUrl.searchParams.delete('page');
                window.location.href = currentUrl.toString();
            }
        });
        
        // Inject nomor dosen login ke JS agar filter autocomplete pembimbing berjalan
        window.nomorDosenLogin = "<?= isset($_SESSION['user_data']['nomor_dosen']) ? htmlspecialchars($_SESSION['user_data']['nomor_dosen']) : '' ?>";