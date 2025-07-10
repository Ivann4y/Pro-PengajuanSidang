document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle logic
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    if (menuToggle && sidebar) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // Modal SweetAlert for Approve/Reject
    const modalSetujui = new bootstrap.Modal(document.getElementById('modalKonfirmasiSetujui'));
    const modalTolak = new bootstrap.Modal(document.getElementById('modalKonfirmasiTolak'));

    let btnSetujui = document.getElementById('btnSetujuiOpenModal');
    let btnTolak = document.getElementById('btnTolakOpenModal');

    if (btnSetujui) {
        btnSetujui.addEventListener('click', function () {
            modalSetujui.show();
        });
    }
    if (btnTolak) {
        btnTolak.addEventListener('click', function () {
            modalTolak.show();
        });
    }

    let confirmSetujuiBtn = document.getElementById('confirmSetujuiBtn');
    if (confirmSetujuiBtn) {
        confirmSetujuiBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Pengajuan Berhasil Disetujui!',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            }).then((result) => {
                if (result.isConfirmed) {
                    const approveForm = document.getElementById('approveForm');
                    let approveInput = approveForm.querySelector('input[name="approve"]');
                    if (!approveInput) {
                        approveInput = document.createElement('input');
                        approveInput.type = 'hidden';
                        approveInput.name = 'approve';
                        approveInput.value = 'Approve';
                        approveForm.appendChild(approveInput);
                    }
                    approveForm.submit();
                }
            });
        });
    }

    let rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const catatan = this.querySelector('textarea[name="catatan"]').value.trim();
            if (catatan === "") {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Silakan isi alasan penolakan terlebih dahulu.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                });
            } else {
                Swal.fire({
                    title: 'Pengajuan Telah Ditolak!',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let rejectInput = this.querySelector('input[name="reject"]');
                        if (!rejectInput) {
                            rejectInput = document.createElement('input');
                            rejectInput.type = 'hidden';
                            rejectInput.name = 'reject';
                            rejectInput.value = 'Reject';
                            this.appendChild(rejectInput);
                        }
                        this.submit();
                    }
                });
            }
        });
    }
});