// FUNGSI UNTUK ADMIN
// --- Tulis fungsi di sini ---


// FUNGSI UNTUK DOSEN
// --- Tulis fungsi di sini ---


// FUNGSI UNTUK MAHASISWA

function switchMSidang() {
    const msidang = document.getElementById("mSidangTA");
    const msidang2 = document.getElementById("mSidangSem");
    const msidangButton = document.getElementById("ddMSidang");
    const msidangmenu = document.getElementById("ddMSidangMenu");
    if (msidang && msidang2) {
        if (msidang.style.display === "none" || getComputedStyle(msidang).display === "none") {
            msidang.style.display = "table-row-group";
            msidang2.style.display = "none";
            msidangButton.innerText = "Sidang TA";
            msidangmenu.innerText = "Sidang Semester";
        } else {
            msidang.style.display = "none";
            msidang2.style.display = "table-row-group";
            msidangButton.innerText = "Sidang Semester";
            msidangmenu.innerText = "Sidang TA";
        }
    }
}