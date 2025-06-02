import pandas as pd
import re

# Data teks yang Anda berikan
data_text = """
Selamat siang teman". untuk pemantauan progress ui, silahkan beri tanda ✅ pada bagian masing" yang sudah selesai dikerjakan ya.

1. Landing page - Nayaka
2. Login page - Nayaka
3. Lupa sandi - Nayaka

   DOSEN
4. Beranda dosen - Evan
5. Pengajuan sidang ✅ - Andien
6. Detail pengajuan - Izzat
7. Daftar sidang ✅ - Rakha
8. Evaluasi - Zafki
9. Dokumen revisi - Evan
10. Nilai akhir✅ - Abram
11. Keluar - Andien

   ADMIN
12. Beranda admin✅  - Jaisyu
13. Penjadwalan ✅ - Dhonnan
14. Daftar sidang - Sheva
15. Detail sidang - Zia
16. Evaluasi - Imel ✅
17. Nilai akhir - Widya
18. Keluar - Andien

   MAHASISWA
19. Beranda mahasiswa - Andrew
20. Notifikasi - Abram
21. Pengajuan ✅ - Vicky
22. Edit pengajuan ✅ - Nopal
23. Tambah pengajuan - Nopal
24. Daftar sidang ✅ - Haaris
25. Detail sidang - Zafki
26. Perbaikan ✅ - Sheva
27. Nilai akhir - Argha
28. Keluar - Andien
"""

# Definisi kolom untuk tabel pengujian pengguna (internal dalam Bahasa Inggris)
columns_en = [
    "ID",
    "Role",
    "Page / Feature Name",
    "UI Completion Status",
    "Assigned Petugas/Developer",
    "Tester/Penguji",
    "Test Date",
    "Scenario / Task Given to User",
    "Actual Observations & User Behavior (Qualitative)",
    "User Quotes (Verbatim)",
    "Task Completion Status",
    "Ease of Use Rating (1-7)",
    "Key Usability Issues Found",
    "Severity of Issue(s)",
    "Positive Feedback / What Worked Well",
    "Recommendations / Suggested Improvements",
    "Keterangan / Notes",
    "Issue Status"
]

# Kamus terjemahan untuk header kolom ke Bahasa Indonesia
column_translation_id = {
    "ID": "ID",
    "Role": "Peran",
    "Page / Feature Name": "Halaman / Nama Fitur",
    "UI Completion Status": "Status Penyelesaian UI",
    "Assigned Petugas/Developer": "Petugas/Pengembang Ditugaskan",
    "Tester/Penguji": "Penguji Pengguna",
    "Test Date": "Tanggal Uji",
    "Scenario / Task Given to User": "Skenario / Tugas Pengguna",
    "Actual Observations & User Behavior (Qualitative)": "Observasi Aktual & Perilaku Pengguna (Kualitatif)",
    "User Quotes (Verbatim)": "Kutipan Pengguna (Verbatim)",
    "Task Completion Status": "Status Penyelesaian Tugas",
    "Ease of Use Rating (1-7)": "Peringkat Kemudahan Penggunaan (1-7)",
    "Key Usability Issues Found": "Masalah Kegunaan Utama Ditemukan",
    "Severity of Issue(s)": "Tingkat Keparahan Masalah",
    "Positive Feedback / What Worked Well": "Umpan Balik Positif / Yang Berfungsi Baik",
    "Recommendations / Suggested Improvements": "Rekomendasi / Saran Perbaikan",
    "Keterangan / Notes": "Keterangan / Catatan",
    "Issue Status": "Status Masalah"
}


parsed_data = []
current_role = "UMUM" # Peran default untuk item awal

lines = data_text.strip().split('\n')

for line in lines:
    line = line.strip()
    if not line or "Selamat siang" in line or "silahkan beri tanda" in line:
        continue

    if line == "DOSEN":
        current_role = "DOSEN"
        continue
    elif line == "ADMIN":
        current_role = "ADMIN"
        continue
    elif line == "MAHASISWA":
        current_role = "MAHASISWA"
        continue

    match = re.match(r"(\d+)\.\s*(.+?)\s*(✅)?\s*-\s*(.+)", line)
    if match:
        item_id = match.group(1)
        feature_name = match.group(2).strip()
        ui_completed = "✅ Selesai" if match.group(3) else "Tertunda"
        petugas = match.group(4).strip()

        temp_role = current_role
        if current_role == "UMUM" and any(keyword in feature_name.lower() for keyword in ["landing", "login", "lupa sandi"]):
            temp_role = "LOGIN" # Tetap LOGIN karena ini adalah kategori khusus

        # Gunakan kunci Bahasa Inggris saat membuat data, akan diterjemahkan sebelum disimpan
        parsed_data.append({
            "ID": item_id,
            "Role": temp_role,
            "Page / Feature Name": feature_name,
            "UI Completion Status": ui_completed,
            "Assigned Petugas/Developer": petugas,
            "Tester/Penguji": "",
            "Test Date": "",
            "Scenario / Task Given to User": "",
            "Actual Observations & User Behavior (Qualitative)": "",
            "User Quotes (Verbatim)": "",
            "Task Completion Status": "",
            "Ease of Use Rating (1-7)": None,
            "Key Usability Issues Found": "",
            "Severity of Issue(s)": "",
            "Positive Feedback / What Worked Well": "",
            "Recommendations / Suggested Improvements": "",
            "Keterangan / Notes": "",
            "Issue Status": "Terbuka" # Status default
        })

# Buat DataFrame tunggal dengan semua data menggunakan nama kolom Inggris
df_all = pd.DataFrame(parsed_data, columns=columns_en)

# Pisahkan DataFrame berdasarkan peran
df_login = df_all[df_all['Role'] == 'LOGIN'].copy()
df_dosen = df_all[df_all['Role'] == 'DOSEN'].copy()
df_admin = df_all[df_all['Role'] == 'ADMIN'].copy()
df_mahasiswa = df_all[df_all['Role'] == 'MAHASISWA'].copy()

# Fungsi untuk mengganti nama kolom DataFrame ke Bahasa Indonesia
def rename_columns_to_indonesian(df):
    df_renamed = df.copy()
    df_renamed.columns = [column_translation_id.get(col, col) for col in df_renamed.columns]
    return df_renamed

# Ganti nama kolom sebelum menyimpan ke Excel
df_login_id = rename_columns_to_indonesian(df_login)
df_dosen_id = rename_columns_to_indonesian(df_dosen)
df_admin_id = rename_columns_to_indonesian(df_admin)
df_mahasiswa_id = rename_columns_to_indonesian(df_mahasiswa)
# Jika Anda juga ingin menyimpan df_all dengan header Indonesia:
# df_all_id = rename_columns_to_indonesian(df_all)


# Menampilkan head dari DataFrame (opsional, baik untuk pengecekan di konsol)
# Ini akan tetap menampilkan dengan header Inggris karena df_login, dll. belum diganti namanya
print("--- FITUR LOGIN (Header Internal) ---")
print(df_login.head().to_string())
print("\n--- FITUR DOSEN (Header Internal) ---")
print(df_dosen.head().to_string())


# Menyimpan ke Excel dengan beberapa sheet dan header Bahasa Indonesia
# Pastikan Anda telah menginstal 'openpyxl': pip install openpyxl
excel_file_name = "Data_Pengujian_Pengguna.xlsx"
with pd.ExcelWriter(excel_file_name) as writer:
    df_login_id.to_excel(writer, sheet_name="LOGIN", index=False)
    df_dosen_id.to_excel(writer, sheet_name="DOSEN", index=False)
    df_admin_id.to_excel(writer, sheet_name="ADMIN", index=False)
    df_mahasiswa_id.to_excel(writer, sheet_name="MAHASISWA", index=False)
    # Jika Anda ingin menyimpan semua fitur dalam satu sheet dengan header Indonesia:
    # df_all_id.to_excel(writer, sheet_name="SEMUA_FITUR", index=False)

print(f"\nBerhasil membuat struktur DataFrame dan disimpan ke '{excel_file_name}'.")
print("File Excel seharusnya berada di direktori yang sama dengan skrip ini dengan header dalam Bahasa Indonesia.")