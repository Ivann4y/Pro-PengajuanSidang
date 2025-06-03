import pandas as pd
import re

# Data teks yang Anda berikan (daftar fitur dan petugas)
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

# Definisi kolom untuk tabel skenario uji (internal dalam Bahasa Inggris untuk kemudahan coding)
columns_en_scenario = [
    "Scenario_No",            # Test Scenario #
    "Requirement_ID",         # Requirement
    "Role",                   # Untuk pemisahan sheet
    "Test_Scenario_Description",# Test Scenario Description
    "Test_Cases",             # Test Cases (multi-line)
    "Assigned_Petugas",       # Petugas Ditugaskan (dari data_text)
    "UI_Completion_Status"    # Status Penyelesaian UI (dari data_text)
]

# Kamus terjemahan untuk header kolom ke Bahasa Indonesia untuk Excel
column_translation_id_scenario = {
    "Scenario_No": "No. Skenario Uji",
    "Requirement_ID": "ID Kebutuhan",
    "Role": "Peran", # Mungkin tidak ditampilkan jika sudah per sheet, tapi baik untuk data gabungan
    "Test_Scenario_Description": "Deskripsi Skenario Uji",
    "Test_Cases": "Kasus Uji",
    "Assigned_Petugas": "Petugas Ditugaskan",
    "UI_Completion_Status": "Status Penyelesaian UI"
}

# Fungsi untuk menghasilkan contoh kasus uji berdasarkan deskripsi skenario
def generate_placeholder_test_cases(scenario_description):
    description_lower = scenario_description.lower()
    cases = []
    if "login" in description_lower:
        cases = [
            "1. Verifikasi perilaku sistem ketika email/ID pengguna yang valid dan kata sandi yang valid dimasukkan.",
            "2. Verifikasi perilaku sistem ketika email/ID pengguna yang tidak valid dan kata sandi yang valid dimasukkan.",
            "3. Verifikasi perilaku sistem ketika email/ID pengguna yang valid dan kata sandi yang tidak valid dimasukkan.",
            "4. Verifikasi perilaku sistem ketika email/ID pengguna dan kata sandi yang tidak valid dimasukkan.",
            "5. Verifikasi perilaku sistem ketika kolom email/ID pengguna dan kata sandi dikosongkan dan tombol Masuk ditekan.",
            "6. Verifikasi fungsionalitas 'Lupa Kata Sandi' bekerja sesuai harapan (jika berlaku).",
            "7. Verifikasi opsi 'Biarkan saya tetap masuk' (Keep me signed in) berfungsi (jika berlaku)."
        ]
    elif "pengajuan" in description_lower or "tambah" in description_lower or "edit" in description_lower:
        cases = [
            "1. Verifikasi pengajuan/penambahan/edit data berhasil dengan semua input yang valid.",
            "2. Verifikasi validasi untuk kolom-kolom yang wajib diisi.",
            "3. Verifikasi validasi untuk format data yang salah (misal, tanggal, email).",
            "4. Verifikasi batas input untuk kolom teks (jika ada).",
            "5. Verifikasi pesan kesalahan yang ditampilkan jelas dan informatif.",
            "6. Verifikasi tombol 'Simpan', 'Batal', 'Kirim' berfungsi sesuai harapan."
        ]
    elif "daftar" in description_lower or "beranda" in description_lower or "dashboard" in description_lower:
        cases = [
            "1. Verifikasi semua elemen UI yang diharapkan (tabel, tombol, filter, data) ditampilkan dengan benar.",
            "2. Verifikasi fungsionalitas pencarian (jika ada) bekerja dengan benar.",
            "3. Verifikasi fungsionalitas filter (jika ada) bekerja dengan benar.",
            "4. Verifikasi paginasi (jika ada) bekerja dengan benar.",
            "5. Verifikasi data yang ditampilkan akurat."
        ]
    elif "detail" in description_lower:
        cases = [
            "1. Verifikasi semua informasi detail ditampilkan dengan benar dan akurat.",
            "2. Verifikasi tombol atau aksi yang tersedia pada halaman detail berfungsi (misal, edit, hapus, kembali).",
            "3. Verifikasi navigasi dari dan ke halaman detail."
        ]
    else:
        cases = [
            "1. Verifikasi fungsionalitas dasar berjalan sesuai harapan.",
            "2. Verifikasi penanganan input yang valid.",
            "3. Verifikasi penanganan input yang tidak valid/ekstrem.",
            "4. Verifikasi elemen UI ditampilkan dengan benar."
        ]
    
    cases.append(f"{len(cases) + 1}. [Tambahkan kasus uji spesifik lainnya di sini]")
    return "\n".join(cases)


parsed_scenario_data = []
current_role = "UMUM"
role_counters = {"LOGIN": 0, "DOSEN": 0, "ADMIN": 0, "MAHASISWA": 0, "UMUM": 0}

lines = data_text.strip().split('\n')

for line in lines:
    line = line.strip()
    if not line or "Selamat siang" in line or "silahkan beri tanda" in line:
        continue

    # Menentukan peran saat ini
    if line.upper() == "DOSEN":
        current_role = "DOSEN"
        continue
    elif line.upper() == "ADMIN":
        current_role = "ADMIN"
        continue
    elif line.upper() == "MAHASISWA":
        current_role = "MAHASISWA"
        continue
    
    # Regex untuk mengambil No, Deskripsi Fitur, tanda centang opsional, dan Petugas
    match = re.match(r"(\d+)\.\s*(.+?)\s*(✅)?\s*-\s*(.+)", line)
    if match:
        scenario_no_from_list = match.group(1) # Ini adalah "Test Scenario #" kita
        feature_name = match.group(2).strip()  # Ini adalah "Test Scenario Description"
        ui_completed = "✅ Selesai" if match.group(3) else "Tertunda"
        petugas = match.group(4).strip()

        # Menentukan peran aktual untuk item
        actual_role = current_role
        if current_role == "UMUM" and any(keyword in feature_name.lower() for keyword in ["landing", "login", "lupa sandi"]):
            actual_role = "LOGIN"
        
        # Membuat ID Kebutuhan
        role_counters[actual_role] += 1
        requirement_id = f"{actual_role[0]}-{role_counters[actual_role]}"

        # Menghasilkan placeholder kasus uji
        test_cases_content = generate_placeholder_test_cases(feature_name)

        parsed_scenario_data.append({
            "Scenario_No": scenario_no_from_list,
            "Requirement_ID": requirement_id,
            "Role": actual_role,
            "Test_Scenario_Description": feature_name,
            "Test_Cases": test_cases_content,
            "Assigned_Petugas": petugas,
            "UI_Completion_Status": ui_completed
        })

# Buat DataFrame tunggal dengan semua data
df_all_scenarios = pd.DataFrame(parsed_scenario_data, columns=columns_en_scenario)

# Pisahkan DataFrame berdasarkan peran
df_login_scenarios = df_all_scenarios[df_all_scenarios['Role'] == 'LOGIN'].copy()
df_dosen_scenarios = df_all_scenarios[df_all_scenarios['Role'] == 'DOSEN'].copy()
df_admin_scenarios = df_all_scenarios[df_all_scenarios['Role'] == 'ADMIN'].copy()
df_mahasiswa_scenarios = df_all_scenarios[df_all_scenarios['Role'] == 'MAHASISWA'].copy()

# Fungsi untuk mengganti nama kolom DataFrame ke Bahasa Indonesia
def rename_columns_to_indonesian(df, translation_dict):
    df_renamed = df.copy()
    # Hapus kolom 'Role' jika sudah dipisah per sheet, kecuali jika ingin tetap ditampilkan
    # if 'Role' in df_renamed.columns and len(df_renamed['Role'].unique()) == 1:
    #     df_renamed = df_renamed.drop(columns=['Role'])
    df_renamed.columns = [translation_dict.get(col, col) for col in df_renamed.columns]
    return df_renamed

# Ganti nama kolom sebelum menyimpan ke Excel
df_login_id_scenarios = rename_columns_to_indonesian(df_login_scenarios, column_translation_id_scenario)
df_dosen_id_scenarios = rename_columns_to_indonesian(df_dosen_scenarios, column_translation_id_scenario)
df_admin_id_scenarios = rename_columns_to_indonesian(df_admin_scenarios, column_translation_id_scenario)
df_mahasiswa_id_scenarios = rename_columns_to_indonesian(df_mahasiswa_scenarios, column_translation_id_scenario)


# Menyimpan ke Excel dengan beberapa sheet dan header Bahasa Indonesia
excel_file_name = "Templat_Skenario_Uji.xlsx"
with pd.ExcelWriter(excel_file_name, engine='openpyxl') as writer:
    df_login_id_scenarios.to_excel(writer, sheet_name="LOGIN", index=False)
    df_dosen_id_scenarios.to_excel(writer, sheet_name="DOSEN", index=False)
    df_admin_id_scenarios.to_excel(writer, sheet_name="ADMIN", index=False)
    df_mahasiswa_id_scenarios.to_excel(writer, sheet_name="MAHASISWA", index=False)
    
    # Menyesuaikan lebar kolom dan wrap text setelah menyimpan data
    # Ini perlu dilakukan per sheet
    workbook = writer.book
    for sheet_name in writer.sheets:
        worksheet = writer.sheets[sheet_name]
        # Atur lebar kolom (contoh, sesuaikan sesuai kebutuhan)
        worksheet.column_dimensions['A'].width = 15 # No. Skenario Uji
        worksheet.column_dimensions['B'].width = 15 # ID Kebutuhan
        worksheet.column_dimensions['C'].width = 15 # Peran (jika ditampilkan)
        worksheet.column_dimensions['D'].width = 30 # Deskripsi Skenario Uji
        worksheet.column_dimensions['E'].width = 60 # Kasus Uji
        worksheet.column_dimensions['F'].width = 20 # Petugas
        worksheet.column_dimensions['G'].width = 20 # Status UI

        # Aktifkan Wrap Text untuk kolom Kasus Uji (kolom ke-5, atau 'E')
        # dan kolom lain yang mungkin memerlukan
        for row in worksheet.iter_rows(min_row=1, max_col=worksheet.max_column, max_row=worksheet.max_row):
            for cell in row:
                if cell.column_letter == 'E': # Kolom Kasus Uji
                    cell.alignment = pd.io.excel._OpenpyxlExcelWriter.Alignment(wrap_text=True, vertical='top')
                elif cell.column_letter == 'D': # Kolom Deskripsi Skenario
                     cell.alignment = pd.io.excel._OpenpyxlExcelWriter.Alignment(wrap_text=True, vertical='top')


print(f"\nBerhasil membuat struktur DataFrame dan disimpan ke '{excel_file_name}'.")
print("File Excel seharusnya berada di direktori yang sama dengan skrip ini.")
print("Kolom 'Kasus Uji' akan berisi beberapa baris dan teksnya akan di-wrap.")