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
      console.log("bacaModal() dipanggil"); // Log awal
      // Simpan referensi baris (tr) yang akan diubah
      rowToUpdate = spanElem.closest('tr');
      const modal = new bootstrap.Modal(document.getElementById("konfirmasiModalnotifikasi"));
      modal.show();
    }

    function lanjutkanAksi() {
      console.log("lanjutkanAksi() dipanggil"); // Log awal
      // Cek apakah rowToUpdate sudah di-set
      if (rowToUpdate) {
        const id_notifikasi = rowToUpdate.getAttribute('data-id');
        console.log("akan melakukan fetch POST", id_notifikasi); // Log sebelum fetch
        fetch('../../control/update_notifikasi_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({ id_notifikasi: id_notifikasi })
        })
        
        .then(data => {
            if (data.success) {
                // Ubah status pada kolom ke-4 dan hapus tombol span
                rowToUpdate.cells[3].innerText = "Sudah Dibaca";
                rowToUpdate.cells[4].innerHTML = ""; // Hapus span
                // Hapus class jadiBiru
                rowToUpdate.classList.remove("jadiBiru");
                // Pindahkan row ke tbody SudahDibaca
                document.getElementById("SudahDibaca").appendChild(rowToUpdate);
                rowToUpdate = null;
            } else {
                alert('Gagal memperbarui notifikasi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghubungi server.');
        });
      }
      // Tutup modal  
      const modalElement = document.getElementById("konfirmasiModalnotifikasi");
      if (modalElement) {
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
      }
    };

    document.getElementById("tidakmodal").onclick = function() {
      // Tutup modal tanpa mengubah apapun
      const modalElement = document.getElementById("konfirmasiModalnotifikasi");
      if (modalElement) {
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
      }
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

window.lanjutkanAksi = lanjutkanAksi;
window.bacaModal = bacaModal;