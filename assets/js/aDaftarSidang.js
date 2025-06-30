 document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            const desktopIconsContainer = document.getElementById('desktop-icons-container');
            const mobileIconsContainer = document.getElementById('mobile-icons-container');
            if (desktopIconsContainer) {
                const headerIcons = desktopIconsContainer.querySelector('.header-icons');
                function handleIconPlacement() {
                    if (window.innerWidth <= 992) {
                        if (mobileIconsContainer && !mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
                    } else { if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons); }
                }
                if (menuToggle && sidebar) {
                    menuToggle.onclick = () => {
                        menuToggle.classList.toggle("NavSide__toggle--active");
                        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                    };
                }
                handleIconPlacement();
                window.addEventListener('resize', handleIconPlacement);
            }
 });
        document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const tableRows = document.querySelectorAll('.table-admin-custom tbody tr.isiTabel');

  searchInput.addEventListener('input', function() {
    const keyword = searchInput.value.toLowerCase();
    tableRows.forEach(row => {
      const rowText = row.innerText.toLowerCase();
      row.style.display = rowText.includes(keyword) ? '' : 'none';
    });
  });
});