import tkinter as tk
from tkinter import ttk, filedialog, messagebox
import os
import platform
import subprocess
from datetime import datetime

# Nama folder untuk menyimpan prompt yang dihasilkan, diterjemahkan ke Bahasa Indonesia
NAMA_FOLDER_PROMPT_YANG_DIHASILKAN = "Prompt_Dihasilkan"

class PromptGeneratorApp:
    # String konstanta untuk teks default dan pilihan, agar konsisten
    STR_PILIH_BAHASA_OPSIONAL = "Pilih Bahasa (Opsional)"
    STR_MASALAH_DEFAULT = "Kode ini memiliki kesalahan / perlu perbaikan karena..."
    STR_ERROR_OUTPUT_DEFAULT = "[Tempel pesan kesalahan atau keluaran konsol yang relevan di sini]"
    STR_SEBUTKAN_BAHASA = "[Sebutkan Bahasa]"

    def __init__(self, root):
        self.root = root
        self.root.title("Pembuat Prompt Kode AI") # Judul aplikasi
        self.root.geometry("700x920")

        # Daftar bahasa pemrograman (nama bahasa umumnya tidak diterjemahkan)
        self.languages = ["Python", "C", "C++", "PHP", "HTML", "CSS", "JavaScript", "Java", "SQL", "Ruby", "Go", "Swift", "Kotlin", "Rust", "TypeScript"]
        self.selected_languages_vars = []
        self.language_dropdowns = []
        self.max_languages = 3

        try:
            self.base_path = os.path.dirname(os.path.abspath(__file__))
        except NameError:
            self.base_path = os.getcwd()
        # Menggunakan nama folder yang sudah diterjemahkan
        self.prompts_dir = os.path.join(self.base_path, NAMA_FOLDER_PROMPT_YANG_DIHASILKAN)

        main_frame = ttk.Frame(root, padding="10")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Bagian 1: Bahasa Pemrograman
        lang_frame = ttk.LabelFrame(main_frame, text="1. Bahasa Pemrograman", padding="10")
        lang_frame.pack(fill=tk.X, pady=5)
        self.lang_dropdown_frame = ttk.Frame(lang_frame)
        self.lang_dropdown_frame.pack(fill=tk.X)
        self._add_language_dropdown()

        # Bagian 2: Deskripsi Masalah
        problem_frame = ttk.LabelFrame(main_frame, text="2. Deskripsi Masalah", padding="10")
        problem_frame.pack(fill=tk.X, pady=5)
        self.problem_text = tk.Text(problem_frame, height=4, width=70, wrap=tk.WORD)
        self.problem_text.pack(fill=tk.X, expand=True)
        self.problem_text.insert(tk.END, self.STR_MASALAH_DEFAULT)
        self._bind_ctrl_backspace(self.problem_text)

        # Bagian 3: Apa yang Saya Inginkan (Format Keluaran)
        what_i_want_frame = ttk.LabelFrame(main_frame, text="3. Apa yang Saya Inginkan (Format Keluaran)", padding="10")
        what_i_want_frame.pack(fill=tk.X, pady=5)
        self.what_i_want_text = tk.Text(what_i_want_frame, height=6, width=70, wrap=tk.WORD)
        self.what_i_want_text.pack(fill=tk.X, expand=True)
        self.what_i_want_text.insert(tk.END,
            "Ketika Anda memberikan kode yang telah diperbaiki, saya ingin Anda menampilkan **seluruh blok kode yang lengkap dan sepenuhnya diperbaiki**.\n"
            "1. Semua baris kode asli yang sudah benar harus dipertahankan.\n"
            "2. Semua perbaikan dan modifikasi Anda harus diintegrasikan langsung ke dalam kode.\n"
            "3. Keluaran harus berupa satu blok kode tunggal yang berkesinambungan yang mewakili versi lengkap yang telah diperbaiki.\n"
            "4. Saya ingin dapat menyalin dan menempel seluruh respons kode Anda secara langsung untuk menggantikan kode lama saya."
        )
        self._bind_ctrl_backspace(self.what_i_want_text)

        # Bagian 4: Batasan Penting (Tetap)
        fixed_instructions_frame = ttk.LabelFrame(main_frame, text="4. Batasan Penting (Tetap)", padding="10")
        fixed_instructions_frame.pack(fill=tk.X, pady=5)
        self.fixed_instructions_text_content = (
            "**Secara spesifik, JANGAN lakukan hal berikut:**\n"
            "*   JANGAN gunakan placeholder seperti `[pertahankan kode sebelumnya]`, `... (sisa kode tidak berubah) ...`, atau sejenisnya.\n"
            "*   JANGAN hanya menampilkan baris yang telah Anda ubah atau tambahkan.\n"
            "*   JANGAN berikan diff atau daftar perubahan.\n\n"
            "Saya membutuhkan *skrip/program/fungsi penuh* dengan semua perbaikan yang telah dimasukkan."
        )
        fixed_instructions_label = ttk.Label(fixed_instructions_frame, text=self.fixed_instructions_text_content, wraplength=650, justify=tk.LEFT)
        fixed_instructions_label.pack(fill=tk.X, expand=True)

        # Bagian 5: Keluaran Kesalahan (Opsional)
        error_output_frame = ttk.LabelFrame(main_frame, text="5. Keluaran Kesalahan (Opsional)", padding="10")
        error_output_frame.pack(fill=tk.X, pady=5)
        self.error_output_text = tk.Text(error_output_frame, height=5, width=70, wrap=tk.WORD)
        self.error_output_text.pack(fill=tk.X, expand=True)
        self.error_output_text.insert(tk.END, self.STR_ERROR_OUTPUT_DEFAULT)
        self._bind_ctrl_backspace(self.error_output_text)

        # Bagian 6: Kode Saat Ini
        current_code_frame = ttk.LabelFrame(main_frame, text="6. Kode Saat Ini", padding="10")
        current_code_frame.pack(fill=tk.BOTH, expand=True, pady=5)

        self.current_code_text = tk.Text(current_code_frame, height=10, width=70, wrap=tk.WORD)
        self.current_code_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        self._bind_ctrl_backspace(self.current_code_text)

        scrollbar = ttk.Scrollbar(current_code_frame, command=self.current_code_text.yview)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        self.current_code_text.config(yscrollcommand=scrollbar.set)

        choose_file_button = ttk.Button(current_code_frame, text="Pilih Berkas", command=self.choose_code_file)
        choose_file_button.pack(side=tk.BOTTOM, pady=5, anchor='se')

        generate_button = ttk.Button(main_frame, text="Hasilkan Prompt (.txt)", command=self.generate_prompt_file)
        generate_button.pack(pady=10)

    def _delete_word_left(self, event):
        try:
            widget = event.widget
            widget.delete("insert -1c wordstart", "insert")
        except tk.TclError:
            pass
        return "break"

    def _bind_ctrl_backspace(self, text_widget):
        text_widget.bind("<Control-BackSpace>", self._delete_word_left)
        if platform.system() == "Darwin": # macOS specific bindings
            text_widget.bind("<Meta-Delete>", self._delete_word_left)
            text_widget.bind("<Meta-BackSpace>", self._delete_word_left)
            text_widget.bind("<Option-Delete>", self._delete_word_left) # Option-Delete (delete word forward)
            text_widget.bind("<Option-BackSpace>", self._delete_word_left) # Option-Backspace (delete word backward)


    def _add_language_dropdown(self, event=None):
        if len(self.selected_languages_vars) < self.max_languages:
            var = tk.StringVar(self.root)
            if not self.selected_languages_vars: # First dropdown
                var.set(self.languages[0]) # Default to first language in the list
            else:
                var.set(self.STR_PILIH_BAHASA_OPSIONAL)

            dropdown = ttk.OptionMenu(self.lang_dropdown_frame, var, var.get(), *self.languages, command=self._on_language_select)
            dropdown.pack(side=tk.LEFT, padx=5, pady=2, fill=tk.X, expand=True)

            self.selected_languages_vars.append(var)
            self.language_dropdowns.append(dropdown)

    def _on_language_select(self, selected_value):
        last_var_index = len(self.selected_languages_vars) - 1
        # Jika bahasa dipilih (bukan placeholder) DAN itu adalah dropdown terakhir DAN belum mencapai maks bahasa
        if selected_value != self.STR_PILIH_BAHASA_OPSIONAL and \
           self.selected_languages_vars[last_var_index].get() == selected_value and \
           len(self.selected_languages_vars) < self.max_languages:
            # Pastikan kita hanya menambah dropdown jika yang terakhir sudah diisi dengan bahasa valid
            if len(self.selected_languages_vars) == len(self.language_dropdowns): # Safety check
                 self._add_language_dropdown()

    def choose_code_file(self):
        filepaths = filedialog.askopenfilenames(
            title="Pilih Berkas Kode", # Judul dialog
            filetypes=(("Semua berkas", "*.*"), # Jenis berkas
                       ("Berkas teks", "*.txt"), ("Berkas Python", "*.py"), ("Berkas JavaScript", "*.js"),
                       ("Berkas HTML", "*.html;*.htm"), ("Berkas CSS", "*.css"), ("Berkas C", "*.c;*.h"),
                       ("Berkas C++", "*.cpp;*.hpp"), ("Berkas PHP", "*.php"), ("Berkas Java", "*.java"))
        )
        if filepaths:
            existing_content = self.current_code_text.get("1.0", tk.END).strip()
            if existing_content and not existing_content.endswith("\n\n"):
                self.current_code_text.insert(tk.END, "\n\n")

            for filepath in filepaths:
                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        content = f.read()
                    filename = os.path.basename(filepath)
                    self.current_code_text.insert(tk.END, f"[Nama Berkas: {filename}]\n") # Label nama berkas
                    file_ext = os.path.splitext(filename)[1].lower()
                    lang_hint_map = { # Language hints for markdown, generally standard
                        ".py": "python", ".js": "javascript", ".html": "html", ".htm": "html",
                        ".css": "css", ".c": "c", ".h": "c", ".cpp": "cpp", ".hpp": "cpp",
                        ".php": "php", ".java": "java", ".sql": "sql", ".rb": "ruby",
                        ".go": "go", ".swift": "swift", ".kt": "kotlin", ".rs": "rust", ".ts": "typescript"
                    }
                    lang_hint = lang_hint_map.get(file_ext, "")
                    self.current_code_text.insert(tk.END, f"```{lang_hint}\n{content.strip()}\n```\n\n")
                except Exception as e:
                    messagebox.showerror("Kesalahan Membaca Berkas", f"Tidak dapat membaca berkas: {filepath}\n{e}")

            # Membersihkan spasi ekstra di akhir jika ada
            current_text = self.current_code_text.get("1.0", tk.END)
            if current_text.endswith("\n\n\n"): # Mengatasi kemungkinan tiga baris baru
                 current_text = current_text[:-1] # Hapus satu baris baru
            self.current_code_text.delete("1.0", tk.END)
            self.current_code_text.insert("1.0", current_text.strip()) # Hapus spasi di awal/akhir dan satu baris baru di akhir


    def _open_file_externally(self, filepath):
        try:
            if platform.system() == "Windows":
                os.startfile(filepath)
            elif platform.system() == "Darwin": # macOS
                subprocess.run(["open", filepath], check=True)
            else: # Linux and other Unix-like
                subprocess.run(["xdg-open", filepath], check=True)
        except FileNotFoundError:
            messagebox.showerror("Kesalahan", f"Tidak dapat menemukan aplikasi untuk membuka '{os.path.basename(filepath)}'.")
        except Exception as e:
            messagebox.showerror("Kesalahan Membuka Berkas", f"Tidak dapat membuka berkas '{os.path.basename(filepath)}':\n{e}")

    def generate_prompt_file(self):
        try:
            # Menggunakan nama folder yang sudah diterjemahkan
            os.makedirs(self.prompts_dir, exist_ok=True)
        except OSError as e:
            messagebox.showerror("Kesalahan Pembuatan Folder", f"Tidak dapat membuat atau mengakses folder '{NAMA_FOLDER_PROMPT_YANG_DIHASILKAN}':\n{e}")
            return

        selected_langs_list = [var.get() for var in self.selected_languages_vars if var.get() and var.get() != self.STR_PILIH_BAHASA_OPSIONAL]
        primary_lang = selected_langs_list[0] if selected_langs_list else self.STR_SEBUTKAN_BAHASA
        problem_desc = self.problem_text.get("1.0", tk.END).strip()
        what_i_want_desc = self.what_i_want_text.get("1.0", tk.END).strip()
        error_output_content = self.error_output_text.get("1.0", tk.END).strip()
        current_code_content = self.current_code_text.get("1.0", tk.END).strip()

        # Validasi input
        if not problem_desc or problem_desc == self.STR_MASALAH_DEFAULT:
            messagebox.showwarning("Informasi Kurang", "Harap deskripsikan masalahnya.")
            return
        if not current_code_content:
            messagebox.showwarning("Informasi Kurang", "Harap tambahkan atau muat kode saat ini.")
            return
        if error_output_content == self.STR_ERROR_OUTPUT_DEFAULT:
            error_output_content = "" # Kosongkan jika masih default

        timestamp = datetime.now().strftime("%Y-%m-%d_%H-%M")
        # Nama berkas dasar tetap menggunakan "Prompt" karena ini adalah bagian dari format yang mungkin dikenali
        base_filename = f"Prompt_{timestamp}.txt"
        counter = 1
        filepath = os.path.join(self.prompts_dir, base_filename)
        
        # Handle jika berkas sudah ada
        while os.path.exists(filepath):
            new_filename = f"Prompt_{timestamp}_{counter}.txt"
            filepath = os.path.join(self.prompts_dir, new_filename)
            counter += 1

        # Konten prompt yang diterjemahkan
        prompt_content = f"[permintaan]\n"
        prompt_content += f"Mohon analisis dan perbaiki kode yang akan saya berikan. Bahasa utama kemungkinan adalah {primary_lang}.\n"
        if len(selected_langs_list) > 1:
            additional_langs = ", ".join(selected_langs_list[1:])
            prompt_content += f"Proyek ini mungkin juga melibatkan teknologi terkait seperti: {additional_langs}.\n"
        prompt_content += "\n"
        prompt_content += f"[masalah]\n{problem_desc}\n\n"
        prompt_content += f"[apa yang saya inginkan]\n{what_i_want_desc}\n\n"
        prompt_content += f"[apa yang terjadi]\n"
        prompt_content += "Dalam pengalaman sebelumnya (atau dengan respons AI lain), ketika meminta perbaikan kode untuk blok seperti:\n"
        prompt_content += "```\n[contoh baris kode asli 1]\n[contoh baris kode asli 2 dengan kesalahan]\n[contoh baris kode asli 3]\n```\n"
        prompt_content += "AI terkadang memberikan perbaikan parsial, seperti:\n"
        prompt_content += "```\n[pertahankan kode sebelumnya]\n[contoh baris kode 2 yang diperbaiki]\n[pertahankan kode sebelumnya]\n```\n"
        prompt_content += "Ini BUKAN keluaran yang diinginkan.\n"
        prompt_content += "Sebaliknya, untuk contoh di atas, saya menginginkan:\n"
        prompt_content += "```\n[contoh baris kode asli 1]\n[contoh baris kode 2 yang diperbaiki]\n[contoh baris kode asli 3]\n```\n\n"
        prompt_content += "**Secara spesifik, JANGAN lakukan hal berikut:**\n"
        prompt_content += "*   JANGAN gunakan placeholder seperti `[pertahankan kode sebelumnya]`, `... (sisa kode tidak berubah) ...`, atau sejenisnya.\n"
        prompt_content += "*   JANGAN hanya menampilkan baris yang telah Anda ubah atau tambahkan.\n"
        prompt_content += "*   JANGAN berikan diff atau daftar perubahan.\n\n"
        prompt_content += "Saya membutuhkan *skrip/program/fungsi penuh* dengan semua perbaikan yang telah dimasukkan.\n\n"
        prompt_content += "[keluaran kesalahan]\n"
        if error_output_content:
            prompt_content += f"```\n{error_output_content}\n```\n\n"
        else:
            prompt_content += "[Tidak ada keluaran kesalahan spesifik yang diberikan oleh pengguna.]\n\n"
        prompt_content += f"[kode saat ini]\n"
        prompt_content += f"{current_code_content}\n"

        try:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(prompt_content)
            messagebox.showinfo("Sukses", f"Prompt berhasil disimpan secara otomatis ke:\n{filepath}")
            self._open_file_externally(filepath)
        except Exception as e:
            messagebox.showerror("Kesalahan Menyimpan Berkas", f"Tidak dapat menyimpan berkas:\n{e}")

if __name__ == "__main__":
    root = tk.Tk()
    app = PromptGeneratorApp(root)
    root.mainloop()