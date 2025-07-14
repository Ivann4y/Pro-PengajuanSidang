
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle Logic
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            if (menuToggle) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            // File Upload UI Logic
            const fileInput = document.getElementById('file_laporan');
            if (fileInput) {
                const uploadBox = document.getElementById('upload-box-label');
                const fileNameDisplay = document.getElementById('file-name-display');
                const uploadIcon = document.getElementById('upload-icon');
                const uploadText = document.getElementById('upload-text');

                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileNameDisplay.textContent = this.files[0].name;
                        uploadIcon.style.display = 'none';
                        uploadText.style.display = 'none';
                        uploadBox.classList.add('file-selected');
                    } else {
                        fileNameDisplay.textContent = '';
                        uploadIcon.style.display = 'block';
                        uploadText.style.display = 'block';
                        uploadBox.classList.remove('file-selected');
                    }
                });
            }

            // Submit Confirmation with SweetAlert
            const submitBtn = document.getElementById('btn-submit-final');
            const form = document.getElementById('pengajuan-form');
            if (submitBtn && form) {
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent form submission
                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: "Setelah submit, pengajuan tidak dapat diedit lagi.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4b68fb',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Submit Final!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Add a hidden input to signify final submission
                            let hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'submit_final';
                            hiddenInput.value = '1';
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    });
                });
            }
        });