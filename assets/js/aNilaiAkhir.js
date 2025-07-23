// Kode untuk toggle sidebar (tidak diubah)
let menuToggle = document.querySelector(".NavSide__toggle");
let sidebar = document.getElementById("main-sidebar");
menuToggle.onclick = function () {
  menuToggle.classList.toggle("NavSide__toggle--active");
  sidebar.classList.toggle("NavSide__sidebar--active-mobile");
};

// Kode untuk active item di sidebar (tidak diubah)
let listItems = document.querySelectorAll(".NavSide__sidebar-item");
for (let i = 0; i < listItems.length; i++) {
  listItems[i].onclick = function () {
    if (!this.classList.contains("NavSide__sidebar-item--active")) {
      for (let j = 0; j < listItems.length; j++) {
        listItems[j].classList.remove("NavSide__sidebar-item--active");
      }
      this.classList.add("NavSide__sidebar-item--active");
    }
  };
}
