// Untuk responsifitas sidebar
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

//Untuk filter pencarian
        document.addEventListener("DOMContentLoaded", function() {
            // Logika untuk pencarian real-time pada tabel
            const searchInput = document.getElementById("searchInput");
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    const query = this.value.toLowerCase().trim();
                    const tableRows = document.querySelectorAll("tbody tr.isiTabel");

                    tableRows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        // Tampilkan baris jika cocok dengan query, sebaliknya sembunyikan
                        row.style.display = rowText.includes(query) ? "" : "none";
                    });
                });
            }
        });
            
        // Inject nomor dosen login ke JS agar filter autocomplete pembimbing berjalan
        window.nomorDosenLogin = "<?= isset($_SESSION['user_data']['nomor_dosen']) ? htmlspecialchars($_SESSION['user_data']['nomor_dosen']) : '' ?>";