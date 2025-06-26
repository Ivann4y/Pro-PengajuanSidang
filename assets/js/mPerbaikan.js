document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            if (menuToggle && sidebar) {
                menuToggle.onclick = () => {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }
            const fileInput = document.getElementById('fileInput');
            const openConfirmModalBtn = document.getElementById('openConfirmModalBtn');
            const initialState = document.getElementById('initial-state');
            const selectedState = document.getElementById('selected-state');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const uploadPromptText = document.getElementById('upload-prompt-text');
            if (fileInput && openConfirmModalBtn) {
                openConfirmModalBtn.disabled = true;
                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        initialState.classList.add('d-none');
                        selectedState.classList.remove('d-none');
                        fileNameDisplay.textContent = this.files[0].name;
                        uploadPromptText.classList.add('d-none');
                        openConfirmModalBtn.disabled = false;
                    } else {
                        initialState.classList.remove('d-none');
                        selectedState.classList.add('d-none');
                        fileNameDisplay.textContent = '';
                        uploadPromptText.classList.remove('d-none');
                        openConfirmModalBtn.disabled = true;
                    }
                });
            }
            const revisionForm = document.getElementById('revisionForm');
            const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
            if (revisionForm && confirmSubmitBtn) {
                confirmSubmitBtn.addEventListener('click', function () {
                    revisionForm.submit();
                });
            }
        });