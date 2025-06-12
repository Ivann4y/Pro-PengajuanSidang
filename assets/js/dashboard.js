// =========================
// Sidebar Toggle Logic
// =========================
let menuToggle = document.querySelector(".NavSide__toggle");
let sidebar = document.getElementById("main-sidebar");

// Toggle sidebar untuk mobile
menuToggle.onclick = function() {
    menuToggle.classList.toggle("NavSide__toggle--active");
    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
};

// =========================
// Kalender Interaktif
// =========================

// Ambil elemen kalender
const calendarTableBody = document.querySelector("#calendarTable tbody");
const currentMonthYearHeader = document.getElementById("currentMonthYear");
const prevMonthBtn = document.getElementById("prevMonth");
const nextMonthBtn = document.getElementById("nextMonth");

// Tanggal hari ini dan tanggal aktif
let currentDate = new Date();
let activeDate = new Date();

// Nama-nama bulan dalam bahasa Indonesia
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

// Fungsi untuk merender kalender
function renderCalendar() {
    calendarTableBody.innerHTML = "";
    currentMonthYearHeader.textContent = `${
    monthNames[activeDate.getMonth()]
  } ${activeDate.getFullYear()}`;

    const year = activeDate.getFullYear();
    const month = activeDate.getMonth();

    // Hari pertama dalam bulan (0 = Minggu)
    const firstDayOfMonth = new Date(year, month, 1).getDay();
    // Jumlah hari dalam bulan
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let date = 1;
    // Render 6 baris (minggu)
    for (let i = 0; i < 6; i++) {
        const row = document.createElement("tr");

        // Render 7 kolom (hari)
        for (let j = 0; j < 7; j++) {
            const cell = document.createElement("td");
            // Kosongkan sel sebelum tanggal 1
            if (i === 0 && j < firstDayOfMonth) {
                cell.innerHTML = "";
            } else if (date > daysInMonth) {
                // Kosongkan sel setelah akhir bulan
                cell.innerHTML = "";
            } else {
                // Isi tanggal
                const daySpan = document.createElement("span");
                daySpan.classList.add("calendar-day");
                daySpan.textContent = date;

                // Tandai hari ini
                if (
                    date === currentDate.getDate() &&
                    month === currentDate.getMonth() &&
                    year === currentDate.getFullYear()
                ) {
                    daySpan.classList.add("current-day");
                }
                cell.appendChild(daySpan);
                date++;
            }
            row.appendChild(cell);
        }
        calendarTableBody.appendChild(row);
    }
}

// Navigasi bulan sebelumnya
prevMonthBtn.addEventListener("click", () => {
    activeDate.setMonth(activeDate.getMonth() - 1);
    activeDate.setDate(1);
    renderCalendar();
});

// Navigasi bulan berikutnya
nextMonthBtn.addEventListener("click", () => {
    activeDate.setMonth(activeDate.getMonth() + 1);
    activeDate.setDate(1);
    renderCalendar();
});

// Render kalender saat halaman dimuat
renderCalendar();