import os

# --- KONFIGURASI ---
# Direktori target yang berisi file-file PHP mahasiswa
TARGET_DIR = 'views/mahasiswa'

# Atur ke False untuk benar-benar mengubah file. 
# SANGAT DISARANKAN untuk menjalankannya dengan True terlebih dahulu!
DRY_RUN = False 

# Penanda untuk mengetahui apakah file sudah diproses oleh skrip ini
PROCESSED_MARKER = "// SESI_VERIFIED_BY_SCRIPT"

# Blok kode PHP yang benar untuk verifikasi sesi
# Blok ini akan disisipkan setelah tag pembuka '<?php'
CORRECT_SESSION_BLOCK = f"""
{PROCESSED_MARKER}
// ===================================================================
//                 BLOK VERIFIKASI SESI (DITAMBAHKAN OTOMATIS)
// ===================================================================

// Selalu mulai sesi terlebih dahulu
if (session_status() === PHP_SESSION_NONE) {{
    session_start();
}}

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {{
    // Jika tidak, redirect ke halaman login
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    // Gunakan path absolut dari root web server Anda
    header("Location: /Sidang/Pro-PengajuanSidang/index.php"); 
    exit();
}}

// 2. Cek apakah role pengguna adalah 'mahasiswa'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {{
    // Jika role tidak cocok, redirect
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: /Sidang/Pro-PengajuanSidang/index.php");
    exit();
}}
// ===================================================================
//                  AKHIR BLOK VERIFIKASI SESI
// ===================================================================
"""

# Daftar pola/string dari kode lama yang perlu dihapus
# Skrip akan mencari baris yang mengandung teks ini dan menghapusnya.
# Dibuat cukup spesifik agar tidak menghapus kode yang valid.
PATTERNS_TO_REMOVE = [
    "session_start(); // Selalu mulai sesi terlebih dahulu",
    "if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true)",
    "if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa')",
    "if ($_SESSION['role'] !== 'mahasiswa')",
    "$_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';",
    "$_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';",
    "header(\"Location: /Sidang/Pro-PengajuanSidang/index.php\");",
    "header(\"Location: ../../index.php\");",
    "exit(); // Hentikan eksekusi skrip",
    "$nim_mahasiswa_logged_in = $_SESSION['user_data']['nim'];",
    "$nim_mahasiswa_logged_in = $_SESSION['user_nim'];"
]

def process_php_file(file_path):
    """Memproses satu file PHP untuk membersihkan dan menambahkan blok sesi."""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content_lines = f.readlines()
    except Exception as e:
        print(f"    [ERROR] Gagal membaca file: {e}")
        return

    # Cek apakah file sudah diproses sebelumnya
    if any(PROCESSED_MARKER in line for line in content_lines):
        print(f"    [SKIPPED] File sudah memiliki blok verifikasi.")
        return

    # Bersihkan kode lama
    cleaned_lines = []
    for line in content_lines:
        # Hapus spasi di awal/akhir baris untuk perbandingan yang andal
        stripped_line = line.strip()
        # Jika baris tidak mengandung pola lama, simpan baris tersebut
        if not any(pattern in stripped_line for pattern in PATTERNS_TO_REMOVE):
            cleaned_lines.append(line)
            
    # Hapus baris 'session_start();' yang mungkin masih tersisa
    cleaned_lines = [line for line in cleaned_lines if line.strip() != "session_start();"]

    # Cari posisi untuk menyisipkan blok baru
    insert_pos = -1
    for i, line in enumerate(cleaned_lines):
        if '<?php' in line:
            insert_pos = i + 1
            break
            
    if insert_pos == -1:
        print(f"    [WARNING] Tidak ditemukan tag '<?php' di file ini. Dilewati.")
        return

    # Gabungkan kembali menjadi konten baru
    # Hapus tag '<?php' dari blok yang akan ditambahkan karena sudah ada di file
    final_content = "".join(cleaned_lines[:insert_pos]) + CORRECT_SESSION_BLOCK + "".join(cleaned_lines[insert_pos:])
    
    # Tulis kembali ke file jika bukan DRY_RUN
    if not DRY_RUN:
        try:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(final_content)
            print(f"    [SUCCESS] File berhasil diperbarui.")
        except Exception as e:
            print(f"    [ERROR] Gagal menulis ke file: {e}")
    else:
        print(f"    [DRY RUN] File akan diperbarui.")


def main():
    """Fungsi utama untuk menjalankan skrip."""
    print("=" * 50)
    print("Memulai Skrip Pembaruan Sesi PHP")
    print("=" * 50)
    if DRY_RUN:
        print("\nPERHATIAN: Skrip berjalan dalam mode DRY RUN.")
        print("Tidak ada file yang akan diubah. Hanya menampilkan aksi yang akan dilakukan.")
        print("Ubah DRY_RUN = False untuk menerapkan perubahan.\n")
    
    # Pastikan direktori target ada
    if not os.path.isdir(TARGET_DIR):
        print(f"Error: Direktori '{TARGET_DIR}' tidak ditemukan. Pastikan Anda menjalankan skrip dari root folder proyek.")
        return

    # Loop melalui file di direktori target
    for filename in os.listdir(TARGET_DIR):
        if filename.endswith(".php"):
            file_path = os.path.join(TARGET_DIR, filename)
            print(f"-> Memproses file: {file_path}")
            process_php_file(file_path)

    print("\n" + "=" * 50)
    print("Skrip Selesai.")
    print("=" * 50)


if __name__ == "__main__":
    main()