import tkinter as tk
from tkinter import ttk, messagebox, filedialog
import json
import os
import random
from datetime import datetime, timedelta

class DataEntryApp:
    def __init__(self, master):
        self.master = master
        master.title("Data Sidang Generator")
        master.geometry("450x550")

        self.entries = []
        self.current_id_counter = 1 # Start ID counter from 1

        # --- Input Fields ---
        tk.Label(master, text="ID (auto-generates):").grid(row=0, column=0, sticky="w", padx=5, pady=2)
        self.id_var = tk.StringVar()
        self.id_entry = tk.Entry(master, textvariable=self.id_var, state='readonly', width=30)
        self.id_entry.grid(row=0, column=1, padx=5, pady=2, sticky="ew")
        self.update_auto_id() # Set initial ID

        tk.Label(master, text="NIM:").grid(row=1, column=0, sticky="w", padx=5, pady=2)
        self.nim_entry = tk.Entry(master, width=30)
        self.nim_entry.grid(row=1, column=1, padx=5, pady=2, sticky="ew")

        tk.Label(master, text="Nama Mahasiswa:").grid(row=2, column=0, sticky="w", padx=5, pady=2)
        self.nama_entry = tk.Entry(master, width=30)
        self.nama_entry.grid(row=2, column=1, padx=5, pady=2, sticky="ew")

        tk.Label(master, text="Judul Sidang:").grid(row=3, column=0, sticky="w", padx=5, pady=2)
        self.judul_entry = tk.Entry(master, width=30)
        self.judul_entry.grid(row=3, column=1, padx=5, pady=2, sticky="ew")

        tk.Label(master, text="Pembimbing:").grid(row=4, column=0, sticky="w", padx=5, pady=2)
        self.pembimbing_entry = tk.Entry(master, width=30)
        self.pembimbing_entry.grid(row=4, column=1, padx=5, pady=2, sticky="ew")

        # --- Tipe Sidang (Radio Buttons) ---
        tk.Label(master, text="Tipe Sidang:").grid(row=5, column=0, sticky="w", padx=5, pady=2)
        self.tipe_sidang_var = tk.StringVar(value="TA") # Default to TA
        tk.Radiobutton(master, text="Sidang TA", variable=self.tipe_sidang_var, value="TA").grid(row=5, column=1, sticky="w", padx=5)
        tk.Radiobutton(master, text="Sidang Semester", variable=self.tipe_sidang_var, value="Semester").grid(row=6, column=1, sticky="w", padx=5)

        # --- Status Persetujuan (Checkbox) ---
        tk.Label(master, text="Status:").grid(row=7, column=0, sticky="w", padx=5, pady=2)
        self.status_persetujuan_var = tk.BooleanVar()
        tk.Checkbutton(master, text="Disetujui", variable=self.status_persetujuan_var).grid(row=7, column=1, sticky="w", padx=5)

        # --- Buttons ---
        self.add_button = tk.Button(master, text="Add Entry", command=self.add_entry, bg="#4CAF50", fg="white")
        self.add_button.grid(row=8, column=0, columnspan=2, pady=5, padx=5, sticky="ew")

        self.random_button = tk.Button(master, text="Generate Random Data", command=self.open_random_dialog, bg="#9C27B0", fg="white")
        self.random_button.grid(row=9, column=0, columnspan=2, pady=5, padx=5, sticky="ew")

        self.generate_button = tk.Button(master, text="Generate JSON File", command=self.generate_json, bg="#008CBA", fg="white")
        self.generate_button.grid(row=10, column=0, columnspan=2, pady=5, padx=5, sticky="ew")

        # --- Display Added Entries (Optional) ---
        tk.Label(master, text="Entries Added:").grid(row=11, column=0, columnspan=2, sticky="w", padx=5, pady=5)
        self.entries_listbox = tk.Listbox(master, height=6)
        self.entries_listbox.grid(row=12, column=0, columnspan=2, padx=5, pady=5, sticky="nsew")

        master.grid_columnconfigure(1, weight=1) # Make entry column expandable

    def update_auto_id(self):
        self.id_var.set(f"{self.current_id_counter:03d}")

    def add_entry(self):
        data = {
            "id": self.id_var.get(),
            "nim": self.nim_entry.get(),
            "nama": self.nama_entry.get(),
            "judulSidang": self.judul_entry.get(),
            "pembimbing": self.pembimbing_entry.get(),
            "tipeSidang": self.tipe_sidang_var.get(),
            "statusPersetujuan": self.status_persetujuan_var.get()
        }

        if not all([data["nim"], data["nama"], data["judulSidang"], data["pembimbing"]]):
            messagebox.showerror("Error", "All fields (except ID) must be filled.")
            return

        self.entries.append(data)
        self.entries_listbox.insert(tk.END, f"ID: {data['id']}, Nama: {data['nama']}, Tipe: {data['tipeSidang']}, Status: {'Disetujui' if data['statusPersetujuan'] else 'Belum Disetujui'}")
        messagebox.showinfo("Success", "Entry added!")

        # Clear fields for next entry (optional)
        self.nim_entry.delete(0, tk.END)
        self.nama_entry.delete(0, tk.END)
        self.judul_entry.delete(0, tk.END)
        self.pembimbing_entry.delete(0, tk.END)
        self.tipe_sidang_var.set("TA") # Reset radio
        self.status_persetujuan_var.set(False) # Reset checkbox
        self.nim_entry.focus()

        # Increment and update ID for next entry
        self.current_id_counter += 1
        self.update_auto_id()

    def open_random_dialog(self):
        random_dialog = tk.Toplevel(self.master)
        random_dialog.title("Generate Random Data")
        random_dialog.geometry("350x200")
        random_dialog.resizable(False, False)
        random_dialog.grab_set()

        tk.Label(random_dialog, text="Jumlah Data Sidang TA:").grid(row=0, column=0, padx=10, pady=10, sticky="w")
        ta_count_var = tk.StringVar(value="5")
        tk.Entry(random_dialog, textvariable=ta_count_var, width=10).grid(row=0, column=1, padx=10, pady=10)

        tk.Label(random_dialog, text="Jumlah Data Sidang Semester:").grid(row=1, column=0, padx=10, pady=10, sticky="w")
        semester_count_var = tk.StringVar(value="5")
        tk.Entry(random_dialog, textvariable=semester_count_var, width=10).grid(row=1, column=1, padx=10, pady=10)

        def generate_random():
            try:
                ta_count = int(ta_count_var.get())
                semester_count = int(semester_count_var.get())
                self.generate_random_data(ta_count, semester_count)
                random_dialog.destroy()
            except ValueError:
                messagebox.showerror("Error", "Masukkan angka yang valid!")

        tk.Button(random_dialog, text="Generate", command=generate_random, bg="#4CAF50", fg="white").grid(row=2, column=0, columnspan=2, pady=20)

    def generate_random_data(self, ta_count, semester_count):
        first_names = ["Ahmad", "Budi", "Citra", "Dewi", "Eka", "Fajar", "Gita", "Hadi", "Indra", "Joko", 
                      "Kartika", "Lina", "Maya", "Nina", "Oki", "Putri", "Rudi", "Sari", "Toni", "Wati"]
        last_names = ["Santoso", "Pratama", "Wijaya", "Kusuma", "Sari", "Nugroho", "Putra", "Setiawan", "Hadi", "Saputra"]
        subjects = ["Sistem", "Aplikasi", "Pengembangan", "Manajemen", "Analisis", "Perancangan", "Implementasi", "Monitoring", "Evaluasi", "Optimasi"]
        topics = ["Sidang", "TA", "Skripsi", "Tugas Akhir", "Proyek", "Kuliah", "Perangkat Lunak", "Website", "Mobile", "Database"]
        lecturers = ["Rida Indah Fariani", "Timotius Victory", "Yosep Setiawan", "Budi Santoso", "Citra Dewi", "Dian Prasetyo"]

        # Generate TA data
        for _ in range(ta_count):
            data = {
                "id": f"{self.current_id_counter:03d}",
                "nim": f"0{random.randint(2,9)}{random.randint(1000,9999)}{random.randint(1000,9999)}",
                "nama": f"{random.choice(first_names)} {random.choice(last_names)}",
                "judulSidang": f"Sistem {random.choice(subjects)} {random.choice(topics)}",
                "pembimbing": random.choice(lecturers),
                "tipeSidang": "TA",
                "statusPersetujuan": random.choice([True, False])
            }
            self.entries.append(data)
            self.entries_listbox.insert(tk.END, f"ID: {data['id']}, Nama: {data['nama']}, Tipe: {data['tipeSidang']}, Status: {'Disetujui' if data['statusPersetujuan'] else 'Belum Disetujui'}")
            self.current_id_counter += 1

        # Generate Semester data
        for _ in range(semester_count):
            data = {
                "id": f"{self.current_id_counter:03d}",
                "nim": f"0{random.randint(2,9)}{random.randint(1000,9999)}{random.randint(1000,9999)}",
                "nama": f"{random.choice(first_names)} {random.choice(last_names)}",
                "judulSidang": f"Proyek {random.choice(subjects)} {random.choice(topics)}",
                "pembimbing": random.choice(lecturers),
                "tipeSidang": "Semester",
                "statusPersetujuan": random.choice([True, False])
            }
            self.entries.append(data)
            self.entries_listbox.insert(tk.END, f"ID: {data['id']}, Nama: {data['nama']}, Tipe: {data['tipeSidang']}, Status: {'Disetujui' if data['statusPersetujuan'] else 'Belum Disetujui'}")
            self.current_id_counter += 1

        messagebox.showinfo("Success", f"Berhasil membuat {ta_count} data TA dan {semester_count} data Semester!")
        self.update_auto_id()

    def generate_json(self):
        if not self.entries:
            messagebox.showwarning("Warning", "No entries to generate.")
            return

        # Get the directory where the script is located
        script_dir = os.path.dirname(os.path.abspath(__file__))
        file_path = os.path.join(script_dir, "data_sidang.json")

        try:
            with open(file_path, 'w') as f:
                json.dump(self.entries, f, indent=2)
            messagebox.showinfo("Success", f"JSON file 'data_sidang.json' generated successfully in\n{file_path}")
            self.entries_listbox.delete(0, tk.END) # Clear listbox
            self.entries = [] # Clear internal list
            self.current_id_counter = 1 # Reset ID counter
            self.update_auto_id() # Update display
        except Exception as e:
            messagebox.showerror("Error", f"Failed to generate JSON: {e}")

if __name__ == "__main__":
    root = tk.Tk()
    app = DataEntryApp(root)
    root.mainloop()