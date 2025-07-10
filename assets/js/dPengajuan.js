
        // Sidebar Toggle Logic
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // [FIXED] Search functionality
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchValue = e.target.value;
                const currentFilter = '<?= htmlspecialchars($filter) ?>';
                // Redirect to the same page with the new search query and existing filter
                window.location.href = `?filter=${currentFilter}&search=${encodeURIComponent(searchValue)}`;
            }
        });
    
        // Inject nomor dosen login ke JS agar filter autocomplete pembimbing berjalan
        window.nomorDosenLogin = "<?= isset($_SESSION['user_data']['nomor_dosen']) ? htmlspecialchars($_SESSION['user_data']['nomor_dosen']) : '' ?>";
