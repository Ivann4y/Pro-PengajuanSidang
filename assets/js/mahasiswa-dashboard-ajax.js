document.addEventListener("DOMContentLoaded", function () {
  // Sidebar toggle logic
  let menuToggle = document.querySelector(".NavSide__toggle");
  let sidebar = document.getElementById("main-sidebar");
  menuToggle.onclick = function () {
    menuToggle.classList.toggle("NavSide__toggle--active");
    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
  };

  // Initialize dashboard data
  let dashboardData = {
    sidang_status: 0,
    penilaian_status: 0,
    tanggungan: [],
    sidang_mendatang: [],
  };

  // Function to load all dashboard data
  async function loadDashboardData() {
    try {
      // Create a single array of fetch promises
      const fetchPromises = [
        fetch(
          "../../control/mahasiswa/mBeranda_queries.php?action=sidang_status"
        ),
        fetch(
          "../../control/mahasiswa/mBeranda_queries.php?action=penilaian_status"
        ),
        fetch("../../control/mahasiswa/mBeranda_queries.php?action=tanggungan"),
        fetch(
          "../../control/mahasiswa/mBeranda_queries.php?action=sidang_mendatang"
        ),
      ];

      // Await all promises to resolve
      const responses = await Promise.all(fetchPromises);
      const [sidangData, penilaianData, tanggunganData, sidangMendatangData] =
        await Promise.all(responses.map((res) => res.json()));

      // Assign data safely
      if (sidangData.sidang_berlangsung !== undefined) {
        dashboardData.sidang_status = sidangData.sidang_berlangsung;
      }
      if (penilaianData.menunggu_penilaian !== undefined) {
        dashboardData.penilaian_status = penilaianData.menunggu_penilaian;
      }
      if (Array.isArray(tanggunganData.tanggungan)) {
        dashboardData.tanggungan = tanggunganData.tanggungan;
      }
      if (Array.isArray(sidangMendatangData.sidang_mendatang)) {
        dashboardData.sidang_mendatang = sidangMendatangData.sidang_mendatang;
      }

      // Update UI with loaded data
      updateDashboardUI();
    } catch (error) {
      console.error("Error loading dashboard data:", error);
      // You could display an error message to the user here
    }
  }

  // Function to update UI with dashboard data
  function updateDashboardUI() {
    // SIDANG STATUS CARD
    document.querySelector(".sidang-status-card .number").textContent =
      dashboardData.sidang_status ?? 0;

    // PENILAIAN STATUS CARD
    document.querySelector(".penilaian-status-card .number").textContent =
      dashboardData.penilaian_status ?? 0;

    // TANGGUNGAN CARD (Tugas)
    const tugasContainer = document.querySelector(".tanggungan-card");
    tugasContainer
      .querySelectorAll(".tanggungan-item, .text-muted")
      .forEach((e) => e.remove()); // Clear previous content

    // Remove title before adding new content
    const tugasTitle = tugasContainer.querySelector(".section-title");
    if (tugasTitle) tugasContainer.innerHTML = ""; // Clear all
    tugasContainer.appendChild(tugasTitle); // Add title back

    if (!dashboardData.tanggungan || dashboardData.tanggungan.length === 0) {
      const p = document.createElement("p");
      p.className = "text-center text-muted mt-3";
      p.textContent = "Tidak ada tugas yang perlu dikerjakan.";
      tugasContainer.appendChild(p);
    } else {
      dashboardData.tanggungan.forEach((item) => {
        const div = document.createElement("div");
        div.className = "tanggungan-item";
        // Using textContent is safe against XSS
        div.textContent = `Revisi ${item.judul}`;
        tugasContainer.appendChild(div);
      });
    }

    // SIDANG MENDATANG CARD & CALENDAR
    const sidangDates = (dashboardData.sidang_mendatang || []).map(
      (item) => item.tanggal_sidang.split("T")[0]
    );
    const sidangData = dashboardData.sidang_mendatang || [];

    renderSidangMendatang(sidangData);
    renderCalendar(sidangDates);
  }

  // --- Calendar and Sidang Mendatang Rendering ---
  const calendarTableBody = document.querySelector("#calendarTable tbody");
  const currentMonthYearHeader = document.getElementById("currentMonthYear");
  const prevMonthBtn = document.getElementById("prevMonth");
  const nextMonthBtn = document.getElementById("nextMonth");
  let activeDate = new Date();
  const monthNames = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];

  function renderCalendar(sidangDates) {
    const today = new Date();
    calendarTableBody.innerHTML = "";
    currentMonthYearHeader.textContent = `${
      monthNames[activeDate.getMonth()]
    } ${activeDate.getFullYear()}`;

    const year = activeDate.getFullYear();
    const month = activeDate.getMonth();
    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let date = 1;
    for (let i = 0; i < 6; i++) {
      const row = document.createElement("tr");
      for (let j = 0; j < 7; j++) {
        const cell = document.createElement("td");
        if (i === 0 && j < firstDayOfMonth) {
          // Empty cells before the 1st day of the month
        } else if (date > daysInMonth) {
          break;
        } else {
          const daySpan = document.createElement("span");
          daySpan.classList.add("calendar-day");
          daySpan.textContent = date;

          const thisDateStr = `${year}-${String(month + 1).padStart(
            2,
            "0"
          )}-${String(date).padStart(2, "0")}`;

          if (
            date === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear()
          ) {
            daySpan.classList.add("current-day");
          }
          if (sidangDates.includes(thisDateStr)) {
            daySpan.classList.add("has-sidang");
          }
          cell.appendChild(daySpan);
          date++;
        }
        row.appendChild(cell);
      }
      calendarTableBody.appendChild(row);
      if (date > daysInMonth) break;
    }
  }

  function renderSidangMendatang(data) {
    const sidangContainer = document.querySelector(".sidang-mendatang-card");

    // Clear previous items but keep the title
    const title = sidangContainer.querySelector(".section-title");
    sidangContainer.innerHTML = "";
    sidangContainer.appendChild(title);

    if (!data.length) {
      const p = document.createElement("p");
      p.className = "text-center text-muted mt-3";
      p.textContent = "Tidak ada sidang yang dijadwalkan.";
      sidangContainer.appendChild(p);
      return;
    }
    data.forEach((item) => {
      const tgl = new Date(item.tanggal_sidang);
      const day = String(tgl.getDate()).padStart(2, "0");
      const month = tgl.toLocaleString("id-ID", { month: "short" });

      const a = document.createElement("a");
      a.href = `mDetailSidang.php?id_sidang=${item.id_sidang}`; // Adjusted filename for consistency
      a.style.textDecoration = "none";
      a.style.color = "inherit";

      const div = document.createElement("div");
      div.className = "item";

      const dateBubble = document.createElement("div");
      dateBubble.className = "date-bubble";
      dateBubble.innerHTML = `<span class="day">${day}</span><span class="month">${month}</span>`;

      const infoSpan = document.createElement("span");
      infoSpan.className = "info";
      infoSpan.textContent = item.judul; // SAFE: Using textContent to prevent XSS

      const arrowSpan = document.createElement("span");
      arrowSpan.className = "arrow";
      arrowSpan.innerHTML = '<i class="bi bi-chevron-right"></i>';

      div.appendChild(dateBubble);
      div.appendChild(infoSpan);
      div.appendChild(arrowSpan);
      a.appendChild(div);
      sidangContainer.appendChild(a);
    });
  }

  // Add event listeners once
  prevMonthBtn.addEventListener("click", () => {
    activeDate.setMonth(activeDate.getMonth() - 1);
    loadDashboardData(); // Reload data for the new month view
  });
  nextMonthBtn.addEventListener("click", () => {
    activeDate.setMonth(activeDate.getMonth() + 1);
    loadDashboardData(); // Reload data for the new month view
  });

  // Load dashboard data when page loads
  loadDashboardData();
});
