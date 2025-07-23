function kembaliKePilihRole() {
  window.location.href = "../../index.php";
}

const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#password");

togglePassword.addEventListener("click", function (e) {
  // HANYA TOGGLE KELAS CSS, BUKAN ATRIBUT TYPE
  password.classList.toggle("password-masked");

  // Logika untuk menukar ikon mata tetap sama
  this.classList.toggle("bi-eye-slash-fill");
  this.classList.toggle("bi-eye-fill");
});

function toLupaPassword() {
  window.location.href = "../../views/lupaPassword.php?role=" + ROLE;
}
