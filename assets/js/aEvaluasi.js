const modalDetail = document.getElementById('modalDetail');
    modalDetail.addEventListener('show.bs.modal', function(event) {
      const trigger = event.relatedTarget;
      const fullCatatan = trigger.getAttribute('data-catatan');
      document.getElementById('modalCatatanText').textContent = fullCatatan;
});

// Sidebar Toggle Logic
let menuToggle = document.querySelector(".NavSide__toggle");
let sidebar = document.getElementById("main-sidebar");

menuToggle.onclick = function() {
menuToggle.classList.toggle("NavSide__toggle--active");
sidebar.classList.toggle("NavSide__sidebar--active-mobile");
};

// Sidebar Active Item Logic (no change needed here as it's already functional)
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

