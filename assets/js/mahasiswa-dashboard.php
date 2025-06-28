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
    ['tanggal_sidang' => '2025-06-18', 'judul' => 'Sistem Pengajuan Skripsi', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-20', 'judul' => 'Revisi Proposal KP', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-24', 'judul' => 'Sidang Akhir TA', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-30', 'judul' => 'Presentasi Proyek', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-07-02', 'judul' => 'Pengumpulan Laporan', 'link_detail' => 'mdetailsidangta.php'],
];

// Mengubah data PHP ke JSON untuk digunakan di JavaScript
$mahasiswa_info_json = json_encode($mahasiswa_info);
$card_dashboard_json = json_encode($card_dashboard);
$tugas_list_json = json_encode($tugas_list);
$sidang_mendatang_json = json_encode($sidang_mendatang);
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sidebar toggle logic
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    menuToggle.onclick = function() {
        menuToggle.classList.toggle("NavSide__toggle--active");
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    // Use dashboardData from PHP
    const data = window.dashboardData;

    // SIDANG STATUS CARD
    document.querySelector('.sidang-status-card .number').textContent = data.sidang_status ?? 0;

    // PENILAIAN STATUS CARD
    document.querySelector('.penilaian-status-card .number').textContent = data.penilaian_status ?? 0;

    // TANGGUNGAN CARD
    const tugasContainer = document.querySelector('.tanggungan-card');
    tugasContainer.querySelectorAll('.tanggungan-item, .text-muted').forEach(e => e.remove());
    if (!data.tanggungan || data.tanggungan.length === 0) {
        const p = document.createElement('p');
        p.className = 'text-center text-muted mt-3';
        p.textContent = 'Tidak ada tugas yang perlu dikerjakan.';
        tugasContainer.appendChild(p);
    } else {
        data.tanggungan.forEach(item => {
            const div = document.createElement('div');
            div.className = 'tanggungan-item';
            div.textContent = `Revisi ${item.judul}`;
            tugasContainer.appendChild(div);
        });
    }

    // SIDANG MENDATANG CARD & CALENDAR
    let sidangDates = (data.sidang_mendatang || []).map(item => item.tanggal_sidang);
    let sidangData = data.sidang_mendatang || [];

    // Declare all calendar variables BEFORE calling renderCalendar
    const calendarTableBody = document.querySelector("#calendarTable tbody");
    const currentMonthYearHeader = document.getElementById("currentMonthYear");
    const prevMonthBtn = document.getElementById("prevMonth");
    const nextMonthBtn = document.getElementById("nextMonth");
    let currentDate = new Date();
    let activeDate = new Date();
    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    function renderCalendar() {
        calendarTableBody.innerHTML = "";
        currentMonthYearHeader.textContent = `${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;
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
                        String(thisDate.getDate()).padStart(2, "0")
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
        sidangContainer.querySelectorAll(".item, .text-muted").forEach(e => e.remove());
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
            const a = document.createElement("a");
            a.href = `mdetailsidangta.php?id_sidang=${item.id_sidang}`;
            a.style.textDecoration = "none";
            a.style.color = "inherit";
            a.appendChild(div);
            sidangContainer.appendChild(a);
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
});
</script> 