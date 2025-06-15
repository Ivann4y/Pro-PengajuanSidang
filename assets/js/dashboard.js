document.addEventListener("DOMContentLoaded", function() {
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

    // Fetch data sidang mendatang dari PHP
    let sidangData = [];
    let sidangDates = [];
    fetch("../../control/sidangMendatang.php")
        .then((response) => response.json())
        .then((data) => {
            sidangData = data;
            sidangDates = data.map((item) => item.tanggal_sidang);
            renderCalendar();
            renderSidangMendatang(data);
        });

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

                    // Format tanggal untuk pencocokan (YYYY-MM-DD)
                    const thisDate = new Date(year, month, date);
                    const dateStr = [
                        thisDate.getFullYear(),
                        String(thisDate.getMonth() + 1).padStart(2, "0"),
                        String(thisDate.getDate()).padStart(2, "0"),
                    ].join("-");

                    // Tandai hari ini
                    if (
                        date === currentDate.getDate() &&
                        month === currentDate.getMonth() &&
                        year === currentDate.getFullYear()
                    ) {
                        daySpan.classList.add("current-day");
                    }

                    // Tandai tanggal yang ada sidang
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

    //Generate item card sidang mendatang dari data...
    function renderSidangMendatang(data) {
        const sidangContainer = document.querySelector(".sidang-mendatang-card");
        // Hapus semua .item lama, kecuali judul
        sidangContainer.querySelectorAll(".item").forEach((e) => e.remove());

        // jika tidak ada sidang mendatang
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
            // Jika ada link_detail, jadikan clickable
            if (item.link_detail) {
                const a = document.createElement("a");
                a.href = item.link_detail;
                a.style.textDecoration = "none";
                a.style.color = "inherit";
                a.appendChild(div);
                sidangContainer.appendChild(a);
            } else {
                sidangContainer.appendChild(div);
            }
        });
    }
    fetch("../../control/jumlahPengajuan.php")
        .then((response) => response.json())
        .then((result) => {
            // result.data adalah array judul sidang yang perlu aksi pengajuannya
            renderAngkaJumlahPengajuan(result.jumlah_pengajuan_perlu_aksi);
        });

    // Render angka jumlah pengajuan
    function renderAngkaJumlahPengajuan(jumlah) {
        const card = document.querySelector(".pengajuan-status-card");
        if (!card) return;
        // Cari elemen .number di dalam card
        let numberDiv = card.querySelector(".number");
        if (!numberDiv) {
            // Jika belum ada, buat baru
            numberDiv = document.createElement("div");
            numberDiv.className = "number";
            // Sisipkan di awal card
            card.insertBefore(numberDiv, card.firstChild);
        }
        numberDiv.textContent = jumlah;
    }

    fetch("../../control/perluJumlahPenjadwalan.php")
        .then((response) => response.json())
        .then((result) => {
            // result.data adalah array judul sidang yang belum terjadwal
            renderTugas(result.data);
            renderAngkaJumlahPenjadwalan(result.jumlah);
        });

    function renderAngkaJumlahPenjadwalan(jumlah) {
        const card = document.querySelector(".penjadwalan-status-card");
        if (!card) return;
        // Cari elemen .number di dalam card
        let numberDiv = card.querySelector(".number");
        if (!numberDiv) {
            // Jika belum ada, buat baru
            numberDiv = document.createElement("div");
            numberDiv.className = "number";
            // Sisipkan di awal card
            card.insertBefore(numberDiv, card.firstChild);
        }
        numberDiv.textContent = jumlah;
    }

    // Render tugas item penjadwalan
    function renderTugas(data) {
        const tugasContainer = document.querySelector(".tugas-card");
        // Hapus semua tugas-item lama, kecuali judul
        tugasContainer.querySelectorAll(".tugas-item").forEach((e) => e.remove());

        if (!data.length) {
            const p = document.createElement("p");
            p.className = "text-center text-muted mt-3";
            p.textContent = "Tidak ada tugas penjadwalan.";
            tugasContainer.appendChild(p);
            return;
        }

        data.forEach((item) => {
            const div = document.createElement("div");
            div.className = "tugas-item";
            div.textContent = "Belum terjadwal " + item.judul;
            tugasContainer.appendChild(div);
        });
    }

    // // Navigasi bulan tanpa reload
    // document.getElementById("prevMonth").onclick = function() {
    //     renderCalendar();
    // };

    // document.getElementById("nextMonth").onclick = function() {
    //     renderCalendar();
    // };

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
});