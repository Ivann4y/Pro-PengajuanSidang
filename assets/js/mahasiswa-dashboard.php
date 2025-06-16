<?php
// PHP Logic
$mahasiswa_info = [
    'nama' => 'Nayaka'
];

// nnti pake query count kyny cek status_sidang
$card_dashboard = [
    'sidang_berlangsung' => 3,
    'menunggu_penilaian' => 2
];

$tugas_list = [
    'Revisi Sidang PRG',
    'Revisi Sidang Basdat',
    'Revisi Sidang TA',
    'Revisi Sidang Orkom',
    'Revisi Sidang Jaringan Komputer',
    'Revisi Sidang Sistem Informasi',
    'Revisi Sidang Sistem Terdistribusi',
    'Revisi Sidang Sistem Operasi',
    'Revisi Sidang Kecerdasan Buatan',
    'Revisi Sidang Pemrograman Web',
];

$sidang_mendatang = [
    ['tanggal_sidang' => '2025-06-02', 'judul' => 'Sistem Pengajuan Skripsi', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-05', 'judul' => 'Revisi Proposal KP', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-10', 'judul' => 'Sidang Akhir TA', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-15', 'judul' => 'Presentasi Proyek', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-07-02', 'judul' => 'Pengumpulan Laporan', 'link_detail' => 'mdetailsidangta.php'],
];

// Convert PHP arrays to JSON for JavaScript
$mahasiswa_info_json = json_encode($mahasiswa_info);
$card_dashboard_json = json_encode($card_dashboard);
$tugas_list_json = json_encode($tugas_list);
$sidang_mendatang_json = json_encode($sidang_mendatang);
?>

<script>
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
    // Data from PHP
    // =========================
    const mahasiswaInfo = <?php echo $mahasiswa_info_json; ?>;
    const cardDashboard = <?php echo $card_dashboard_json; ?>;
    const tugasList = <?php echo $tugas_list_json; ?>;
    const sidangData = <?php echo $sidang_mendatang_json; ?>;
    const sidangDates = sidangData.map(item => item.tanggal_sidang);

    // Update dashboard numbers
    document.querySelector('.sidang-status-card .number').textContent = cardDashboard.sidang_berlangsung;
    document.querySelector('.penilaian-status-card .number').textContent = cardDashboard.menunggu_penilaian;

    // Update welcome text
    document.querySelector('.welcome-text').textContent = `Selamat Datang, ${mahasiswaInfo.nama}!`;

    // Render tugas list
    const tugasContainer = document.querySelector('.tanggungan-card');
    if (tugasList.length === 0) {
        tugasContainer.innerHTML += '<p class="text-center text-muted mt-3">Tidak ada tugas yang perlu dikerjakan.</p>';
    } else {
        tugasList.forEach(tugas => {
            const div = document.createElement('div');
            div.className = 'tanggungan-item';
            div.textContent = tugas;
            tugasContainer.appendChild(div);
        });
    }

    // =========================
    // Kalender Interaktif
    // =========================
    const calendarTableBody = document.querySelector("#calendarTable tbody");
    const currentMonthYearHeader = document.getElementById("currentMonthYear");
    const prevMonthBtn = document.getElementById("prevMonth");
    const nextMonthBtn = document.getElementById("nextMonth");

    // Tanggal hari ini dan tanggal aktif
    let currentDate = new Date();
    let activeDate = new Date();

    // Nama-nama bulan dalam bahasa Indonesia
    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    // Fungsi untuk merender kalender
    function renderCalendar() {
        calendarTableBody.innerHTML = "";
        currentMonthYearHeader.textContent = `${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;

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
                        String(thisDate.getDate()).padStart(2, "0")
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

    // Generate item card sidang mendatang dari data
    function renderSidangMendatang(data) {
        const sidangContainer = document.querySelector(".sidang-mendatang-card");
        // Hapus semua .item lama, kecuali judul
        sidangContainer.querySelectorAll(".item").forEach(e => e.remove());

        // jika tidak ada sidang mendatang
        if (!data.length) {
            const p = document.createElement("p");
            p.className = "text-center text-muted mt-3";
            p.textContent = "Tidak ada sidang yang dijadwalkan.";
            sidangContainer.appendChild(p);
            return;
        }

        data.forEach(item => {
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

    // Render kalender dan sidang mendatang saat halaman dimuat
    renderCalendar();
    renderSidangMendatang(sidangData);
});
</script> 