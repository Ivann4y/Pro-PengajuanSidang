let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

    // Active menu item highlighting
     let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }
    let rowToUpdate = null;

    function bacaModal(spanElem) {
      // Simpan referensi baris (tr) yang akan diubah
      rowToUpdate = spanElem.closest('tr');
      const modal = new bootstrap.Modal(document.getElementById("konfirmasiModalnotifikasi"));
      modal.show();
    }

    function lanjutkanAksi() {
      // Cek apakah rowToUpdate sudah di-set

      document.getElementById("bacabutton")
      if (rowToUpdate) {
        // Ubah status pada kolom ke-4 dan hapus tombol span
        rowToUpdate.cells[3].innerText = "Sudah Dibaca";
        rowToUpdate.cells[4].innerHTML = ""; // Hapus span
        // Hapus class jadiBiru
        rowToUpdate.classList.remove("jadiBiru");
        // Pindahkan row ke tbody SudahDibaca
        document.getElementById("SudahDibaca").appendChild(rowToUpdate);
        rowToUpdate = null;
      }
      // Tutup modal
      bootstrap.Modal.getInstance(document.getElementById("konfirmasiModalnotifikasi")).hide();
    };

    document.getElementById("tidakmodal").onclick = function() {
      // Tutup modal tanpa mengubah apapun
      bootstrap.Modal.getInstance(document.getElementById("konfirmasiModalnotifikasi")).hide();
    };

    function switchMNotifikasi() {
      const mnotif = document.getElementById("BelumDibaca");
      const mnotif2 = document.getElementById("SudahDibaca");
      const mnotifButton = document.getElementById("ddMBelumDibaca");
      const mnotifmenu = document.getElementById("ddMSudahDibaca");
      if (mnotif && mnotif2) {
        if (
          mnotif.style.display === "none" ||
          getComputedStyle(mnotif).display === "none"
        ) {
          mnotif.style.display = "table-row-group";
          mnotif2.style.display = "none";
          mnotifButton.innerText = "Belum Dibaca";
          mnotifmenu.innerText = "Sudah Dibaca";
        } else {
          mnotif.style.display = "none";
          mnotif2.style.display = "table-row-group";
          mnotifButton.innerText = "Sudah Dibaca";
          mnotifmenu.innerText = "Belum Dibaca";
        }
      }
    }