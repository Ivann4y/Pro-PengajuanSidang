function setupPasswordToggle(inputId, toggleId) {
  const passwordInput = document.getElementById(inputId);
  const toggleIcon = document.getElementById(toggleId);

  toggleIcon.addEventListener("click", function () {
    // Toggle the type
    const type =
      passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);

    // Toggle the icon
    this.classList.toggle("bi-eye-slash-fill");
    this.classList.toggle("bi-eye-fill");
  });
}

// Terapkan fungsi ke kedua input password
setupPasswordToggle("newPassword", "toggleNewPassword");
setupPasswordToggle("confirmPassword", "toggleConfirmPassword");
