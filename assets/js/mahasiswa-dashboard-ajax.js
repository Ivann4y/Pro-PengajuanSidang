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
      // Load sidang status
      const sidangResponse = await fetch(
        "../../control/mahasiswa/mBeranda_queries.php?action=sidang_berlangsung"
      );
      const sidangData = await sidangResponse.json();
      if (sidangData.total !== undefined) {
        dashboardData.sidang_status = sidangData.total;
      }

      // Load penilaian status
      const penilaianResponse = await fetch(
        "../../control/mahasiswa/mBeranda_queries.php?action=penilaian_status"
      );
      const penilaianData = await penilaianResponse.json();
      if (penilaianData.menunggu_penilaian !== undefined) {
        dashboardData.penilaian_status = penilaianData.menunggu_penilaian;
      }

      // Load tanggungan
      const tanggunganResponse = await fetch(
        "../../control/mahasiswa/mBeranda_queries.php?action=tanggungan"
      );
      const tanggunganData = await tanggunganResponse.json();
      if (tanggunganData.tanggungan !== undefined) {
        dashboardData.tanggungan = tanggunganData.tanggungan;
      }

      // Load sidang mendatang
      const sidangMendatangResponse = await fetch(
        "../../control/mahasiswa/mBeranda_queries.php?action=sidang_mendatang"
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
    // SIDANG STATUS CARD
    document.querySelector(".sidang-status-card .number").textContent =
      dashboardData.sidang_status ?? 0;

    // PENILAIAN STATUS CARD
    document.querySelector(".penilaian-status-card .number").textContent =
      dashboardData.penilaian_status ?? 0;

    // TANGGUNGAN CARD
    const tugasContainer = document.querySelector(".tanggungan-card");
    tugasContainer
      .querySelectorAll(".tanggungan-item, .text-muted")
      .forEach((e) => e.remove());
    if (!dashboardData.tanggungan || dashboardData.tanggungan.length === 0) {
      const p = document.createElement("p");
      p.className = "text-center text-muted mt-3";
      p.textContent = "Tidak ada tugas yang perlu dikerjakan.";
      tugasContainer.appendChild(p);
    } else {
      dashboardData.tanggungan.forEach((item) => {
        const div = document.createElement("div");
        div.className = "tanggungan-item";
        div.textContent = `Revisi ${item.judul}`;
        tugasContainer.appendChild(div);
      });
    }

    // SIDANG MENDATANG CARD & CALENDAR
    const sidangDates = (dashboardData.sidang_mendatang || []).map((item) => {
      // Handle both formats: "Y-m-d\TH:i:s" and "Y-m-d"
      const dateStr = item.tanggal_sidang;
      if (dateStr.includes("T")) {
        return dateStr.split("T")[0]; // Extract Y-m-d part
      }
      return dateStr; // Already in Y-m-d format
    });
    const sidangData = dashboardData.sidang_mendatang || [];

    // Declare all calendar variables BEFORE calling renderCalendar
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
      // Clear previous items including forms
      sidangContainer.querySelectorAll(".item, .text-muted, form").forEach((e) => e.remove());

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

        // Create a form for each item
        const form = document.createElement("form");
        form.action = "mdetailSidang.php"; // Correct destination
        form.method = "POST";             // Use POST method
        form.style.margin = "0";

        // Create a hidden input to hold the sidang ID
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "id_sidang";
        hiddenInput.value = item.id_sidang;

        // Create a submit button that looks like your original item
        const submitButton = document.createElement("button");
        submitButton.type = "submit";
        submitButton.className = "item"; // Use the 'item' class for styling
        // Remove default button styles to make it look like a div
        submitButton.style.border = "none";
        submitButton.style.background = "none";
        submitButton.style.padding = "0";
        submitButton.style.textAlign = "left";
        submitButton.style.width = "100%";
        submitButton.style.cursor = "pointer";

        // Set the inner HTML of the button
        submitButton.innerHTML = `
            <div class="date-bubble">
                <span class="day">${day}</span>
                <span class="month">${month}</span>
            </div>
            <span class="info">${item.judul}</span>
            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
        `;
        
        // Append hidden input and the submit button to the form
        form.appendChild(hiddenInput);
        form.appendChild(submitButton);

        // Append the complete form to the container
        sidangContainer.appendChild(form);
      });
    }

    // Now call the functions after all variables are declared
    renderSidangMendatang(sidangData);
    renderCalendar();

    prevMonthBtn.addEventListener("click", () => {
      activeDate.setMonth(activeDate.getMonth() - 1);
      activeDate.setDate(1);
      renderCalendar();
    });
    nextMonthBtn.addEventListener("click", () => {
      activeDate.setMonth(activeDate.getMonth() + 1);
      activeDate.setDate(1);
      renderCalendar();
    });
  }

  // Load dashboard data when page loads
  loadDashboardData();
});
