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
    pengajuan: 0,
    perbaikan: 0,
    penilaian: 0,
    sidang_mendatang: [],
  };

  // Function to load all dashboard data
  async function loadDashboardData() {
    try {
      // Load pengajuan count
      const pengajuanResponse = await fetch(
        "../../control/dosen/dBeranda_queries.php?action=pengajuan"
      );
      const pengajuanData = await pengajuanResponse.json();
      if (pengajuanData.total !== undefined) {
        dashboardData.pengajuan = pengajuanData.total;
      }

      // Load perbaikan count
      const perbaikanResponse = await fetch(
        "../../control/dosen/dBeranda_queries.php?action=perbaikan"
      );
      const perbaikanData = await perbaikanResponse.json();
      if (perbaikanData.total !== undefined) {
        dashboardData.perbaikan = perbaikanData.total;
      }

      // Load penilaian count
      const penilaianResponse = await fetch(
        "../../control/dosen/dBeranda_queries.php?action=penilaian"
      );
      const penilaianData = await penilaianResponse.json();
      if (penilaianData.total !== undefined) {
        dashboardData.penilaian = penilaianData.total;
      }

      // Load sidang mendatang
      const sidangMendatangResponse = await fetch(
        "../../control/dosen/dBeranda_queries.php?action=sidang_mendatang"
      );
      const sidangMendatangData = await sidangMendatangResponse.json();
      if (sidangMendatangData.sidang_mendatang !== undefined) {
        dashboardData.sidang_mendatang = sidangMendatangData.sidang_mendatang;
      }

      // Update UI with loaded data
      updateDashboardUI();
    } catch (error) {
      console.error("Error loading dashboard data:", error);
    }
  }

  // Function to update UI with dashboard data
  function updateDashboardUI() {
    // Update pengajuan card
    const pengajuanCard = document.querySelector(".card-pengajuan .number");
    if (pengajuanCard) {
      pengajuanCard.textContent = dashboardData.pengajuan ?? 0;
    }

    // Update perbaikan card
    const perbaikanCard = document.querySelector(".card-perbaikan .number");
    if (perbaikanCard) {
      perbaikanCard.textContent = dashboardData.perbaikan ?? 0;
    }

    // Update penilaian card
    const penilaianCard = document.querySelector(".card-penilaian .number");
    if (penilaianCard) {
      penilaianCard.textContent = dashboardData.penilaian ?? 0;
    }

    // Update sidang mendatang and calendar
    let sidangDates = (dashboardData.sidang_mendatang || []).map(
      (item) => item.tanggal_sidang
    );
    let sidangData = dashboardData.sidang_mendatang || [];

    // Calendar functionality
    const calendarTableBody = document.querySelector("#calendarTable tbody");
    const currentMonthYearHeader = document.getElementById("currentMonthYear");
    const prevMonthBtn = document.getElementById("prevMonth");
    const nextMonthBtn = document.getElementById("nextMonth");
    let currentDate = new Date();
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

    function renderCalendar() {
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
            cell.innerHTML = "";
          } else if (date > daysInMonth) {
            cell.innerHTML = "";
          } else {
            const daySpan = document.createElement("span");
            daySpan.classList.add("calendar-day");
            daySpan.textContent = date;
            const thisDate = new Date(year, month, date);
            const dateStr = [
              thisDate.getFullYear(),
              String(thisDate.getMonth() + 1).padStart(2, "0"),
              String(thisDate.getDate()).padStart(2, "0"),
            ].join("-");
            if (
              date === currentDate.getDate() &&
              month === currentDate.getMonth() &&
              year === currentDate.getFullYear()
            ) {
              daySpan.classList.add("current-day");
            }
            if (sidangDates.includes(dateStr)) {
              daySpan.classList.add("has-sidang");
            }
            cell.appendChild(daySpan);
            date++;
          }
          row.appendChild(cell);
        }
        calendarTableBody.appendChild(row);
      }
    }

    function renderSidangMendatang(data) {
      const sidangContainer = document.querySelector(".sidang-mendatang-card");
      if (!sidangContainer) return;

      sidangContainer
        .querySelectorAll(".item, .text-muted")
        .forEach((e) => e.remove());
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
        const month = tgl.toLocaleString("default", { month: "short" });
        const div = document.createElement("div");
        div.className = "item";
        div.innerHTML = `
                    <div class="date-bubble">
                        <span class="day">${day}</span>
                        <span class="month">${month}</span>
                    </div>
                    <span class="info">${item.judul}</span>
                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                `;
        const a = document.createElement("a");
        a.href = `dDetailPengajuan.php?id_sidang=${item.id_sidang}`;
        a.style.textDecoration = "none";
        a.style.color = "inherit";
        a.appendChild(div);
        sidangContainer.appendChild(a);
      });
    }

    // Render calendar and sidang mendatang
    renderSidangMendatang(sidangData);
    renderCalendar();

    // Calendar navigation
    if (prevMonthBtn) {
      prevMonthBtn.addEventListener("click", () => {
        activeDate.setMonth(activeDate.getMonth() - 1);
        activeDate.setDate(1);
        renderCalendar();
      });
    }
    if (nextMonthBtn) {
      nextMonthBtn.addEventListener("click", () => {
        activeDate.setMonth(activeDate.getMonth() + 1);
        activeDate.setDate(1);
        renderCalendar();
      });
    }
  }

  // Load dashboard data when page loads
  loadDashboardData();
});
