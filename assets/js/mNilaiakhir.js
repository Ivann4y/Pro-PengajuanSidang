//Argha arybawa Pasha
    // <!-- Memuat jQuery dari CDN. Diperlukan untuk beberapa fungsionalitas Bootstrap dan script custom. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    // <!-- === JAVASCRIPT KUSTOM UNTUK HALAMAN INI === -->    // Script ini menangani interaksi pada sidebar navigasi.

    // 1. Fungsionalitas Toggle Sidebar untuk Mobile
    // Memilih elemen tombol toggle (hamburger menu)
    let menuToggle = document.querySelector(".NavSide__toggle");

    // Memilih elemen sidebar utama
    let sidebar = document.getElementById("main-sidebar");

    // Menambahkan event listener 'click' pada tombol toggle
    menuToggle.onclick = function () {
        // Menambah/menghapus kelas '...--active' pada tombol, biasanya untuk mengubah ikon (dari hamburger ke 'x')
        menuToggle.classList.toggle("NavSide__toggle--active");
        // Menambah/menghapus kelas '...--active-mobile' pada sidebar untuk menampilkan atau menyembunyikannya
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    // 2. Fungsionalitas untuk Menandai Item Menu yang Aktif
    // Memilih semua item menu di sidebar
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    // Melakukan loop (iterasi) untuk setiap item menu
    for (let i = 0; i < listItems.length; i++) {
        // Menambahkan event listener 'click' pada setiap item menu
        listItems[i].onclick = function () {
            // Cek jika item yang diklik belum memiliki kelas '...--active'
            if(!this.classList.contains("NavSide__sidebar-item--active")) {
                // Hapus kelas '...--active' dari SEMUA item menu
                for (let j = 0; j < listItems.length; j++) {
                    listItems[j].classList.remove("NavSide__sidebar-item--active");
                }
                // Tambahkan kelas '...--active' HANYA ke item yang baru saja diklik
                this.classList.add("NavSide__sidebar-item--active");
            }
        };
    }
