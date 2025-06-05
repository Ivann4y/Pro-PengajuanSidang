import tkinter as tk
from tkinter import ttk, filedialog, messagebox, simpledialog
import os
import platform
import subprocess
from datetime import datetime
import tempfile
import threading
import re
import pyperclip

PROMPTS_FOLDER_NAME = "Generated Prompts"
DEFAULT_LANGUAGE = "English"  # Constant for default language

class RunCodeWindow(tk.Toplevel):
    def __init__(self, parent, file_path, auto_run=False):
        super().__init__(parent)
        self.title("Run Code")
        self.geometry("700x500")
        self.file_path = file_path
        self.auto_run = auto_run
        self.process = None
        self.running = False
        
        # Command section
        command_frame = ttk.Frame(self, padding="10")
        command_frame.pack(fill=tk.X, pady=5)
        
        ttk.Label(command_frame, text="Command to run:").pack(anchor=tk.W)
        self.command_var = tk.StringVar()
        self.command_entry = ttk.Entry(command_frame, textvariable=self.command_var, width=80)
        self.command_entry.pack(fill=tk.X, pady=5)
        
        # Set default command based on file extension
        self._set_default_command()
        
        # Run button
        button_frame = ttk.Frame(self)
        button_frame.pack(fill=tk.X, pady=5)
        
        self.run_button = ttk.Button(button_frame, text="Run Code", command=self.run_code)
        self.run_button.pack(side=tk.LEFT, padx=5)
        
        # Terminal/Log section
        terminal_frame = ttk.LabelFrame(self, text="Terminal Output", padding="10")
        terminal_frame.pack(fill=tk.BOTH, expand=True, pady=5)
        
        self.terminal_text = tk.Text(terminal_frame, height=15, wrap=tk.WORD, state=tk.DISABLED)
        self.terminal_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        
        terminal_scrollbar = ttk.Scrollbar(terminal_frame, command=self.terminal_text.yview)
        terminal_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        self.terminal_text.config(yscrollcommand=terminal_scrollbar.set)
        
        # Auto-run if specified
        if self.auto_run:
            self.after(100, self.run_code)
    
    def _set_default_command(self):
        """Set default command based on file extension"""
        filename = os.path.basename(self.file_path)
        _, ext = os.path.splitext(filename)
        ext = ext.lower()
        
        commands = {
            '.py': f'python "{filename}"',
            '.js': f'node "{filename}"',
            '.rb': f'ruby "{filename}"',
            '.php': f'php "{filename}"',
            '.pl': f'perl "{filename}"',
            '.lua': f'lua "{filename}"',
            '.r': f'Rscript "{filename}"',
            '.sh': f'bash "{filename}"',
        }
        
        # Use the default command if available, otherwise just the filename
        default_cmd = commands.get(ext, f'"{filename}"')
        self.command_var.set(default_cmd)
    
    def run_code(self):
        if self.running:
            return
            
        command = self.command_var.get()
        if not command:
            return
            
        self.running = True
        self.run_button.config(state=tk.DISABLED)
        self._log_to_terminal(f">>> Running: {command}\n")
        
        # Run in a separate thread to prevent GUI freezing
        threading.Thread(target=self._execute_command, args=(command,), daemon=True).start()
    
    def _execute_command(self, command):
        try:
            # Create a temporary file for output
            output_file = tempfile.NamedTemporaryFile(delete=False, suffix=".log")
            output_file.close()
            
            self.process = subprocess.Popen(
                command,
                shell=True,
                cwd=os.path.dirname(self.file_path),
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT,
                text=True,
                encoding='utf-8',
                errors='replace'
            )
            
            # Stream output to terminal
            while True:
                output = self.process.stdout.readline()
                if output == '' and self.process.poll() is not None:
                    break
                if output:
                    self._log_to_terminal(output)
            
            return_code = self.process.poll()
            self._log_to_terminal(f"\n<<< Process finished with exit code {return_code}\n")
            
        except Exception as e:
            self._log_to_terminal(f"Error: {str(e)}\n")
        finally:
            self.running = False
            self.run_button.config(state=tk.NORMAL)
            os.unlink(output_file.name)  # Clean up temp file
    
    def _log_to_terminal(self, message):
        self.terminal_text.config(state=tk.NORMAL)
        self.terminal_text.insert(tk.END, message)
        self.terminal_text.see(tk.END)
        self.terminal_text.config(state=tk.DISABLED)


class PromptGeneratorApp:
    def __init__(self, root):
        self.root = root
        self.root.title("AI Code Prompt Generator")
        self.root.state('zoomed')  # Open in fullscreen
        
        # Language state
        self.current_language = DEFAULT_LANGUAGE
        self.translations = {
            "English": {
                "title": "AI Code Prompt Generator",
                "language": "1. Language(s)",
                "problem": "2. Problem Description",
                "constraints": "3. Important Constraints",
                "what_i_want": "4. What I Want",
                "error_output": "5. Error Output (Optional)",
                "current_code": "6. Included Code",
                "terminal": "Terminal",
                "clear": "Clear All",
                "include_dir": "Include Dir Listing",
                "instant_run": "Instant Run",
                "run_code": "Run Code",
                "generate": "Generate Prompt (.txt)",
                "copy_success": "Text has been copied to clipboard",
                "tree_depth": "Tree Depth:",
                "os_label": "OS:",
                "os_windows": "Windows",
                "os_linux": "Linux",
                "win_version": "Version:",
                "language_button": "Toggle Language (ID)",
                "modified_title": "Code Modified",
                "modified_msg": "Current code has been modified. Include changes?",
                "yes": "Yes",
                "no": "No",
            },
            "Bahasa Indonesia": {
                "title": "Generator Prompt Kode AI",
                "language": "1. Bahasa",
                "problem": "2. Deskripsi Masalah",
                "constraints": "3. Batasan Penting",
                "what_i_want": "4. Yang Saya Inginkan",
                "error_output": "5. Output Error (Opsional)",
                "current_code": "6. Kode Saat Ini",
                "terminal": "Terminal",
                "clear": "Hapus Semua",
                "include_dir": "Sertakan Daftar Direktori",
                "instant_run": "Jalankan Instan",
                "run_code": "Jalankan Kode",
                "generate": "Buat Prompt (.txt)",
                "copy_success": "Teks telah disalin ke clipboard",
                "tree_depth": "Kedalaman Tree:",
                "os_label": "Sistem Operasi:",
                "os_windows": "Windows",
                "os_linux": "Linux",
                "win_version": "Versi:",
                "language_button": "Ganti Bahasa (EN)",
                "modified_title": "Kode Dimodifikasi",
                "modified_msg": "Kode saat ini telah diubah. Sertakan perubahan?",
                "yes": "Ya",
                "no": "Tidak",
            }
        }
        
        self.languages = ["Python", "C", "C++", "PHP", "HTML", "CSS", "JavaScript", "Java", "SQL", "Ruby", "Go", "Swift", "Kotlin", "Rust", "TypeScript"]
        self.selected_languages_vars = []
        self.language_dropdowns = []
        self.max_languages = 3
        self.temp_dir = tempfile.mkdtemp(prefix="prompt_maker_")
        self.original_code_content = ""  # Store original code state
        self.code_modified = False

        try:
            self.base_path = os.path.dirname(os.path.abspath(__file__))
        except NameError:
            self.base_path = os.getcwd()  # Fallback
        self.prompts_dir = os.path.join(self.base_path, PROMPTS_FOLDER_NAME)

        # --- SCROLLABLE AREA SETUP ---
        self.canvas = tk.Canvas(root)
        self.scrollbar = ttk.Scrollbar(root, orient="vertical", command=self.canvas.yview)
        self.canvas.configure(yscrollcommand=self.scrollbar.set)

        self.scrollbar.pack(side="right", fill="y")
        self.canvas.pack(side="left", fill="both", expand=True)

        self.scrollable_frame = ttk.Frame(self.canvas, padding="10")
        self.canvas_frame_id = self.canvas.create_window((0, 0), window=self.scrollable_frame, anchor="nw")

        self.scrollable_frame.bind("<Configure>", self._on_frame_configure)
        self.canvas.bind("<Configure>", self._on_canvas_configure)

        self.root.bind_all("<MouseWheel>", self._on_mousewheel)
        self.root.bind_all("<Button-4>", self._on_mousewheel) 
        self.root.bind_all("<Button-5>", self._on_mousewheel)
        # --- END SCROLLABLE AREA SETUP ---

        # Top controls frame (language button)
        top_controls_frame = ttk.Frame(self.scrollable_frame)
        top_controls_frame.pack(fill=tk.X, pady=5)
        
        self.lang_button = ttk.Button(
            top_controls_frame, 
            text=self._tr("language_button"),
            command=self.toggle_language
        )
        self.lang_button.pack(side=tk.RIGHT, padx=5)

        # OS Selection
        os_frame = ttk.Frame(self.scrollable_frame)
        os_frame.pack(fill=tk.X, pady=5)
        
        ttk.Label(os_frame, text=self._tr("os_label")).pack(side=tk.LEFT, padx=5)
        self.os_var = tk.StringVar(value="Windows")
        ttk.Radiobutton(os_frame, text=self._tr("os_windows"), variable=self.os_var, value="Windows").pack(side=tk.LEFT, padx=5)
        ttk.Radiobutton(os_frame, text=self._tr("os_linux"), variable=self.os_var, value="Linux").pack(side=tk.LEFT, padx=5)
        
        # Windows version dropdown
        win_version_frame = ttk.Frame(os_frame)
        win_version_frame.pack(side=tk.LEFT, padx=5)
        ttk.Label(win_version_frame, text=self._tr("win_version")).pack(side=tk.LEFT)
        self.win_ver_var = tk.StringVar(value="10")
        win_ver_combo = ttk.Combobox(win_version_frame, textvariable=self.win_ver_var, width=5, state="readonly")
        win_ver_combo['values'] = ("7", "8", "10", "11")
        win_ver_combo.pack(side=tk.LEFT, padx=5)

        # Language selection
        lang_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("language"), padding="10")
        lang_frame.pack(fill=tk.X, pady=5)
        self.lang_dropdown_frame = ttk.Frame(lang_frame)
        self.lang_dropdown_frame.pack(fill=tk.X)
        self._add_language_dropdown()

        # Problem description
        problem_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("problem"), padding="10")
        problem_frame.pack(fill=tk.X, pady=5)
        self.problem_text = tk.Text(problem_frame, height=4, width=70, wrap=tk.WORD)
        self.problem_text.pack(fill=tk.X, expand=True)
        self.problem_text.insert(tk.END, "The code has an error / needs improvement because...")
        self._bind_ctrl_backspace(self.problem_text)
        self._bind_scroll(self.problem_text)

        # What I Want section
        what_i_want_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("what_i_want"), padding="10")
        what_i_want_frame.pack(fill=tk.X, pady=5)
        self.what_i_want_text = tk.Text(what_i_want_frame, height=6, width=70, wrap=tk.WORD)
        self.what_i_want_text.pack(fill=tk.X, expand=True)
        self.what_i_want_text.insert(tk.END,
            "[Describe what you want the AI to do or produce, e.g., fix the bug, add a feature, explain the code, etc.]"
        )
        self._bind_ctrl_backspace(self.what_i_want_text)
        self._bind_scroll(self.what_i_want_text)

        # Important Constraints (collapsible)
        constraints_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("constraints"), padding="10")
        constraints_frame.pack(fill=tk.X, pady=5)
        constraints_content = (
            "**Specifically, do NOT do the following:**\n"
            "*   Do NOT use placeholders like `[keep the previous code]`, `... (rest of the code unchanged) ...`, or similar.\n"
            "*   Do NOT only show the lines you have changed or added.\n"
            "*   Do NOT provide a diff or a list of changes.\n\n"
            "I need the *full script/program/function* with all fixes incorporated.\n\n"
            "**What I Want (Output Format):**\n"
            "When you provide the corrected code, I need you to output the **entire, complete, and fully corrected code block**.\n"
            "1. All original lines of code that are correct should be retained.\n"
            "2. All your fixes and modifications should be integrated directly into the code.\n"
            "3. The output should be a single, contiguous block of code representing the complete, fixed version.\n"
            "4. I want to be able to copy and paste your entire code response directly to replace my old code."
        )
        constraints_label = ttk.Label(constraints_frame, text=constraints_content, wraplength=620, justify=tk.LEFT)
        constraints_label.pack(fill=tk.X, expand=True)

        # Error output
        error_output_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("error_output"), padding="10")
        error_output_frame.pack(fill=tk.X, pady=5)
        self.error_output_text = tk.Text(error_output_frame, height=5, width=70, wrap=tk.WORD)
        self.error_output_text.pack(fill=tk.X, expand=True)
        self.error_output_text.insert(tk.END, "[Paste any relevant error messages or console output here]")
        self._bind_ctrl_backspace(self.error_output_text)
        self._bind_scroll(self.error_output_text)

        # Current code
        current_code_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("current_code"), padding="10")
        current_code_frame.pack(fill=tk.BOTH, expand=True, pady=5)
        
        code_controls_frame = ttk.Frame(current_code_frame)
        code_controls_frame.pack(fill=tk.X, side=tk.TOP, pady=(0,5))
        choose_file_button = ttk.Button(code_controls_frame, text="Choose File(s)", command=self.choose_code_file)
        choose_file_button.pack(side=tk.RIGHT)

        self.current_code_text = tk.Text(current_code_frame, height=10, width=70, wrap=tk.WORD)
        self.current_code_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        self._bind_ctrl_backspace(self.current_code_text)
        self._bind_scroll(self.current_code_text)
        
        # Bind text modification
        self.current_code_text.bind("<<Modified>>", self._on_code_modified)
        
        code_scrollbar = ttk.Scrollbar(current_code_frame, command=self.current_code_text.yview)
        code_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        self.current_code_text.config(yscrollcommand=code_scrollbar.set)

        # Terminal section
        terminal_frame = ttk.LabelFrame(self.scrollable_frame, text=self._tr("terminal"), padding="10")
        terminal_frame.pack(fill=tk.BOTH, expand=True, pady=5)
        
        self.terminal_text = tk.Text(terminal_frame, height=8, wrap=tk.WORD, state=tk.DISABLED)
        self.terminal_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        
        terminal_scrollbar = ttk.Scrollbar(terminal_frame, command=self.terminal_text.yview)
        terminal_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        self.terminal_text.config(yscrollcommand=terminal_scrollbar.set)
        
        # Log initial message
        self._log_to_terminal("Terminal ready. Use 'Run Code' to execute scripts.")

        # Directory listing controls
        dir_controls_frame = ttk.Frame(self.scrollable_frame)
        dir_controls_frame.pack(fill=tk.X, pady=5)
        
        self.include_dir_var = tk.BooleanVar(value=False)
        self.include_dir_check = ttk.Checkbutton(
            dir_controls_frame, 
            text=self._tr("include_dir"),
            variable=self.include_dir_var
        )
        self.include_dir_check.pack(side=tk.LEFT, padx=5)
        
        # Tree depth dropdown
        ttk.Label(dir_controls_frame, text=self._tr("tree_depth")).pack(side=tk.LEFT, padx=5)
        self.tree_depth_var = tk.IntVar(value=1)
        tree_depth_combo = ttk.Combobox(dir_controls_frame, textvariable=self.tree_depth_var, width=3, state="readonly")
        tree_depth_combo['values'] = (1, 2, 3, 4, 5)
        tree_depth_combo.pack(side=tk.LEFT, padx=5)

        # Bottom controls
        bottom_controls_frame = ttk.Frame(self.scrollable_frame)
        bottom_controls_frame.pack(fill=tk.X, pady=10, side=tk.BOTTOM, anchor='sw')

        clear_button = ttk.Button(bottom_controls_frame, text=self._tr("clear"), command=self.clear_form)
        clear_button.pack(side=tk.LEFT, padx=5)

        self.instant_run_button = ttk.Button(
            bottom_controls_frame, 
            text=self._tr("instant_run"), 
            command=self.instant_run
        )
        self.instant_run_button.pack(side=tk.LEFT, padx=5)
        self.instant_run_button.pack_forget()

        run_code_button = ttk.Button(
            bottom_controls_frame, 
            text=self._tr("run_code"), 
            command=self.run_code
        )
        run_code_button.pack(side=tk.LEFT, padx=5)

        self.generate_button = ttk.Button(
            bottom_controls_frame, 
            text=self._tr("generate"),
            command=self.generate_prompt_file
        )
        self.generate_button.pack(side=tk.LEFT, padx=5)
        
        # Clean up temp directory on exit
        self.root.protocol("WM_DELETE_WINDOW", self.on_close)

    def _tr(self, key):
        """Get translation for current language"""
        return self.translations[self.current_language][key]

    def toggle_language(self):
        """Switch between English and Bahasa Indonesia"""
        self.current_language = "Bahasa Indonesia" if self.current_language == "English" else "English"
        self.lang_button.config(text=self._tr("language_button"))
        self.update_ui_language()

    def update_ui_language(self):
        """Update all UI elements with current language"""
        self.root.title(self._tr("title"))
        
        # Update all label frames
        for widget in self.scrollable_frame.winfo_children():
            if isinstance(widget, ttk.LabelFrame):
                if widget.cget("text") in ["1. Language(s)", "1. Bahasa"]:
                    widget.config(text=self._tr("language"))
                elif widget.cget("text") in ["2. Problem Description", "2. Deskripsi Masalah"]:
                    widget.config(text=self._tr("problem"))
                elif widget.cget("text") in ["3. Important Constraints", "3. Batasan Penting"]:
                    widget.config(text=self._tr("constraints"))
                elif widget.cget("text") in ["4. What I Want", "4. Yang Saya Inginkan"]:
                    widget.config(text=self._tr("what_i_want"))
                elif widget.cget("text") in ["5. Error Output (Optional)", "5. Output Error (Opsional)"]:
                    widget.config(text=self._tr("error_output"))
                elif widget.cget("text") in ["6. Current Code", "6. Kode Saat Ini"]:
                    widget.config(text=self._tr("current_code"))
                elif widget.cget("text") in ["Terminal", "Terminal"]:
                    widget.config(text=self._tr("terminal"))
        
        # Update buttons and labels
        self.include_dir_check.config(text=self._tr("include_dir"))
        self.instant_run_button.config(text=self._tr("instant_run"))
        self.generate_button.config(text=self._tr("generate"))

    def _on_code_modified(self, event):
        """Track if code has been modified"""
        if event.widget.edit_modified():
            current_content = self.current_code_text.get("1.0", tk.END)
            self.code_modified = (current_content != self.original_code_content)
            event.widget.edit_modified(False)  # Reset modified flag

    def _log_to_terminal(self, message):
        self.terminal_text.config(state=tk.NORMAL)
        self.terminal_text.insert(tk.END, message)
        self.terminal_text.see(tk.END)
        self.terminal_text.config(state=tk.DISABLED)

    def clear_form(self):
        for dropdown in self.language_dropdowns:
            dropdown.destroy()
        self.selected_languages_vars = []
        self.language_dropdowns = []
        self._add_language_dropdown()
        
        self.problem_text.delete("1.0", tk.END)
        self.problem_text.insert(tk.END, "The code has an error / needs improvement because...")
        
        self.what_i_want_text.delete("1.0", tk.END)
        self.what_i_want_text.insert(tk.END,
            "[Describe what you want the AI to do or produce, e.g., fix the bug, add a feature, explain the code, etc.]"
        )
        
        self.error_output_text.delete("1.0", tk.END)
        self.error_output_text.insert(tk.END, "[Paste any relevant error messages or console output here]")
        
        self.current_code_text.delete("1.0", tk.END)
        self.original_code_content = ""
        self.code_modified = False
        
        self.include_dir_var.set(False)
        self.tree_depth_var.set(1)
        self.os_var.set("Windows")
        self.win_ver_var.set("10")
        
        self.terminal_text.config(state=tk.NORMAL)
        self.terminal_text.delete("1.0", tk.END)
        self.terminal_text.insert(tk.END, "Form cleared. Ready for new input.")
        self.terminal_text.config(state=tk.DISABLED)
        
        self.instant_run_button.pack_forget()
        self._log_to_terminal("\nForm cleared successfully.\n")

    def _on_frame_configure(self, event=None):
        self.canvas.configure(scrollregion=self.canvas.bbox("all"))

    def _on_canvas_configure(self, event=None):
        canvas_width = event.width
        self.canvas.itemconfig(self.canvas_frame_id, width=canvas_width)

    def _on_mousewheel(self, event):
        delta = 0
        if platform.system() == "Windows":
            delta = event.delta // 120
        elif platform.system() == "Darwin":
            delta = event.delta
        elif event.num == 4:
            delta = 1 
        elif event.num == 5:
            delta = -1
        
        if delta != 0:
            self.canvas.yview_scroll(-delta, "units")
            return "break"

    def _bind_scroll(self, widget):
        """Bind mousewheel to scroll specific widget"""
        widget.bind("<MouseWheel>", self._on_widget_scroll)
        widget.bind("<Button-4>", self._on_widget_scroll)
        widget.bind("<Button-5>", self._on_widget_scroll)
    
    def _on_widget_scroll(self, event):
        """Handle scrolling for individual widgets"""
        if event.delta:
            delta = -1 if event.delta < 0 else 1
        elif event.num == 4:
            delta = -1
        elif event.num == 5:
            delta = 1
        else:
            return
            
        event.widget.yview_scroll(delta, "units")
        return "break"

    def _delete_word_left(self, event):
        try:
            widget = event.widget
            widget.delete("insert -1c wordstart", "insert")
        except tk.TclError:
            pass
        return "break"

    def _bind_ctrl_backspace(self, text_widget):
        text_widget.bind("<Control-BackSpace>", self._delete_word_left)
        if platform.system() == "Darwin":
            text_widget.bind("<Meta-Delete>", self._delete_word_left)
            text_widget.bind("<Meta-BackSpace>", self._delete_word_left)
            text_widget.bind("<Option-Delete>", self._delete_word_left)
            text_widget.bind("<Option-BackSpace>", self._delete_word_left)

    def _add_language_dropdown(self, event=None):
        if len(self.selected_languages_vars) < self.max_languages:
            var = tk.StringVar(self.root)
            is_first_dropdown = not self.selected_languages_vars
            
            if is_first_dropdown:
                var.set(self.languages[0])
            else:
                var.set("Select Language (Optional)")

            dropdown = ttk.OptionMenu(self.lang_dropdown_frame, var, var.get(), *self.languages, command=self._on_language_select)
            dropdown.pack(side=tk.LEFT, padx=5, pady=2, fill=tk.X, expand=True)

            self.selected_languages_vars.append(var)
            self.language_dropdowns.append(dropdown)

            if is_first_dropdown and var.get() != "Select Language (Optional)":
                if len(self.selected_languages_vars) < self.max_languages:
                    self._add_language_dropdown()

    def _on_language_select(self, selected_value):
        actual_langs_selected_count = 0
        for var in self.selected_languages_vars:
            if var.get() != "Select Language (Optional)":
                actual_langs_selected_count += 1
        
        if actual_langs_selected_count == len(self.selected_languages_vars) and \
           len(self.selected_languages_vars) < self.max_languages:
            self._add_language_dropdown()

    def choose_code_file(self):
        filepaths = filedialog.askopenfilenames(
            title="Select Code File(s)",
            filetypes=(("All files", "*.*"),
                       ("Text files", "*.txt"), ("Python files", "*.py"), ("JavaScript files", "*.js"),
                       ("HTML files", "*.html;*.htm"), ("CSS files", "*.css"), ("C files", "*.c;*.h"),
                       ("C++ files", "*.cpp;*.hpp"), ("PHP files", "*.php"), ("Java files", "*.java"))
        )
        if filepaths:
            existing_content = self.current_code_text.get("1.0", tk.END).strip()
            if existing_content:
                if not existing_content.endswith("\n\n"):
                    if existing_content.endswith("\n"):
                        self.current_code_text.insert(tk.END, "\n")
                    else:
                        self.current_code_text.insert(tk.END, "\n\n")

            for filepath in filepaths:
                try:
                    with open(filepath, 'r', encoding='utf-8', errors='replace') as f:
                        content = f.read()
                    filename = os.path.basename(filepath)
                    self.current_code_text.insert(tk.END, f"[File Name: {filename}]\n")
                    file_ext = os.path.splitext(filename)[1].lower()
                    lang_hint_map = {
                        ".py": "python", ".js": "javascript", ".html": "html", ".htm": "html",
                        ".css": "css", ".c": "c", ".h": "c", ".cpp": "cpp", ".hpp": "cpp",
                        ".php": "php", ".java": "java", ".sql": "sql", ".rb": "ruby",
                        ".go": "go", ".swift": "swift", ".kt": "kotlin", ".rs": "rust", ".ts": "typescript"
                    }
                    lang_hint = lang_hint_map.get(file_ext, "")
                    self.current_code_text.insert(tk.END, f"```{lang_hint}\n{content.strip()}\n```\n\n")
                except Exception as e:
                    messagebox.showerror("Error Reading File", f"Could not read file: {filepath}\n{e}")

            current_text_full = self.current_code_text.get("1.0", tk.END)
            cleaned_text = current_text_full.rstrip()
            if cleaned_text:
                cleaned_text += "\n\n"
            self.current_code_text.delete("1.0", tk.END)
            self.current_code_text.insert("1.0", cleaned_text)
            
            # Store original content
            self.original_code_content = self.current_code_text.get("1.0", tk.END)
            self.code_modified = False
            
            self._update_instant_run_button()

    def _update_instant_run_button(self):
        text_content = self.current_code_text.get("1.0", tk.END)
        file_count = text_content.count("[File Name:")
        
        if file_count == 1:
            self.instant_run_button.pack(side=tk.LEFT, padx=5)
        else:
            self.instant_run_button.pack_forget()

    def _open_file_externally(self, filepath):
        try:
            if platform.system() == "Windows":
                os.startfile(filepath)
            elif platform.system() == "Darwin":
                subprocess.run(["open", filepath], check=True)
            else:
                subprocess.run(["xdg-open", filepath], check=True)
        except FileNotFoundError:
            messagebox.showerror("Error", f"Could not find application to open '{os.path.basename(filepath)}'. Please ensure you have a default application for .txt files.")
        except Exception as e:
            messagebox.showerror("Error Opening File", f"Could not open file '{os.path.basename(filepath)}':\n{e}")

    def _get_directory_listing(self):
        dir_listing_output = ""
        current_dir_to_list = ""
        depth = self.tree_depth_var.get()
        
        try:
            # Calculate target directory based on depth
            target_dir = self.base_path
            for _ in range(depth - 1):
                target_dir = os.path.dirname(target_dir)
            
            current_dir_to_list = target_dir
            command_str_display = ""
            
            if platform.system() == "Windows":
                command_to_run = f"tree /f"
                command_str_display = f"tree /f (in {current_dir_to_list})"
                result = subprocess.run(
                    command_to_run, 
                    shell=True, 
                    cwd=current_dir_to_list,
                    capture_output=True, 
                    text=True, 
                    check=False, 
                    encoding='utf-8', 
                    errors='replace'
                )
            else:
                command_to_run = ["tree", "-L", "3"]  # Default tree with depth 3
                command_str_display = f"tree -L 3 (in {current_dir_to_list})"
                result = subprocess.run(
                    command_to_run, 
                    cwd=current_dir_to_list, 
                    capture_output=True, 
                    text=True, 
                    check=False, 
                    encoding='utf-8', 
                    errors='replace'
                )
            
            if result.returncode == 0:
                dir_listing_output = f"Output of '{command_str_display}':\n```text\n"
                dir_listing_output += result.stdout.strip()
                dir_listing_output += "\n```"
            else:
                # Fallback to ls if tree fails
                fallback_cmd = ["ls", "-la"] if platform.system() != "Windows" else "dir"
                command_str_display = f"ls -la (in {current_dir_to_list})" if platform.system() != "Windows" else f"dir (in {current_dir_to_list})"
                result = subprocess.run(
                    fallback_cmd, 
                    shell=True if platform.system() == "Windows" else False,
                    cwd=current_dir_to_list,
                    capture_output=True, 
                    text=True, 
                    check=False, 
                    encoding='utf-8', 
                    errors='replace'
                )
                dir_listing_output = f"Output of '{command_str_display}':\n```text\n"
                dir_listing_output += result.stdout.strip() if result.stdout else result.stderr.strip()
                dir_listing_output += "\n```"
        except Exception as e:
            dir_listing_output = f"Exception while getting directory listing for '{current_dir_to_list}':\n```text\n{e}\n```"
        return dir_listing_output

    def generate_prompt_file(self):
        # Check for code modifications
        use_current_code = True
        if self.code_modified:
            response = messagebox.askyesno(
                self._tr("modified_title"),
                self._tr("modified_msg"),
                parent=self.root
            )
            if not response:
                use_current_code = False

        try:
            os.makedirs(self.prompts_dir, exist_ok=True)
        except OSError as e:
            messagebox.showerror("Folder Creation Error", f"Could not create or access '{PROMPTS_FOLDER_NAME}' folder:\n{e}")
            return

        selected_langs_list = [var.get() for var in self.selected_languages_vars if var.get() and var.get() != "Select Language (Optional)"]
        primary_lang = selected_langs_list[0] if selected_langs_list else "[Specify Language]"
        problem_desc = self.problem_text.get("1.0", tk.END).strip()
        what_i_want_desc = self.what_i_want_text.get("1.0", tk.END).strip()
        error_output_content = self.error_output_text.get("1.0", tk.END).strip()
        
        # Get code content based on modification choice
        if use_current_code:
            current_code_content = self.current_code_text.get("1.0", tk.END).strip()
        else:
            current_code_content = self.original_code_content.strip()

        if not problem_desc or problem_desc == "The code has an error / needs improvement because...":
            messagebox.showwarning("Missing Info", "Please describe the problem.")
            return
        if not current_code_content:
            messagebox.showwarning("Missing Info", "Please add or load the current code.")
            return
        if error_output_content == "[Paste any relevant error messages or console output here]":
            error_output_content = ""
        if what_i_want_desc == "[Describe what you want the AI to do or produce, e.g., fix the bug, add a feature, explain the code, etc.]":
            what_i_want_desc = "User did not specify explicit goals beyond fixing the problem described."


        timestamp = datetime.now().strftime("%Y-%m-%d_%H-%M")
        base_filename = f"Prompt_{timestamp}.txt"
        counter = 1
        filepath = os.path.join(self.prompts_dir, base_filename)
        
        while os.path.exists(filepath):
            new_filename = f"Prompt_{timestamp}_{counter}.txt"
            filepath = os.path.join(self.prompts_dir, new_filename)
            counter += 1

        # OS information
        os_info = ""
        if self.os_var.get() == "Windows":
            os_info = f"I'm on Windows {self.win_ver_var.get()}.\n"
        else:
            os_info = "I'm on Linux.\n"

        prompt_content = f"[request]\n"
        prompt_content += f"Please analyze and correct the code I will provide. The primary language is likely {primary_lang}.\n"
        prompt_content += os_info
        if len(selected_langs_list) > 1:
            additional_langs = ", ".join(selected_langs_list[1:])
            prompt_content += f"The project might also involve related technologies like: {additional_langs}.\n"
        prompt_content += "\n"
        prompt_content += f"[problem]\n{problem_desc}\n\n"
        prompt_content += f"[what i want]\n{what_i_want_desc}\n\n"
        
        prompt_content += f"[what happened]\n"
        prompt_content += "In previous experiences (or with other AI responses), when asking for code fixes for a block like:\n"
        prompt_content += "```\n[example original code line 1]\n[example original code line 2 with error]\n[example original code line 3]\n```\n"
        prompt_content += "The AI sometimes provided a partial fix, such as:\n"
        prompt_content += "```\n[keep the previous code]\n[example corrected code line 2]\n[keep the previous code]\n```\n"
        prompt_content += "This is NOT the desired output.\n"
        prompt_content += "Instead, for the example above, I would want:\n"
        prompt_content += "```\n[example original code line 1]\n[example corrected code line 2]\n[example original code line 3]\n```\n\n"
        
        prompt_content += f"[error output]\n"
        if error_output_content:
            prompt_content += f"```text\n{error_output_content}\n```\n\n"
        else:
            prompt_content += "[No specific error output was provided by the user.]\n\n"

        if self.include_dir_var.get():
            dir_listing_section_content = self._get_directory_listing()
            if dir_listing_section_content:
                prompt_content += f"[directory listing]\n{dir_listing_section_content}\n\n"
        
        prompt_content += f"[current code]\n"
        if current_code_content:
             prompt_content += f"{current_code_content}\n" 

        try:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(prompt_content)
            
            # Copy to clipboard
            pyperclip.copy(prompt_content)
            self._log_to_terminal(f"{self._tr('copy_success')}\n")
            messagebox.showinfo("Success", f"Prompt saved and copied to clipboard:\n{filepath}")
            self._log_to_terminal(f"Prompt generated: {os.path.basename(filepath)}\n")
            self._open_file_externally(filepath)
        except Exception as e:
            messagebox.showerror("Error Saving File", f"Could not save file:\n{e}")

    def _extract_code_files(self):
        for filename in os.listdir(self.temp_dir):
            file_path = os.path.join(self.temp_dir, filename)
            try:
                if os.path.isfile(file_path):
                    os.unlink(file_path)
            except Exception as e:
                self._log_to_terminal(f"Error deleting temp file: {e}\n")
        
        text_content = self.current_code_text.get("1.0", tk.END)
        pattern = r'\[File Name: (.*?)\].*?```(?:.*?)\n(.*?)```'
        matches = re.findall(pattern, text_content, re.DOTALL)
        
        saved_files = []
        for filename, code in matches:
            try:
                clean_filename = os.path.basename(filename.strip())
                file_path = os.path.join(self.temp_dir, clean_filename)
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(code.strip())
                saved_files.append(file_path)
                self._log_to_terminal(f"Saved: {clean_filename}\n")
            except Exception as e:
                self._log_to_terminal(f"Error saving {filename}: {e}\n")
        
        return saved_files

    def run_code(self):
        saved_files = self._extract_code_files()
        if not saved_files:
            self._log_to_terminal("No code files found to run.\n")
            return
            
        if len(saved_files) == 1:
            self._log_to_terminal("Opening run window for single file...\n")
            RunCodeWindow(self.root, saved_files[0], auto_run=True)
        else:
            self._log_to_terminal(f"Found {len(saved_files)} files. Opening selection window...\n")
            self._open_file_selection_window(saved_files)

    def instant_run(self):
        saved_files = self._extract_code_files()
        if len(saved_files) != 1:
            self._log_to_terminal("Instant Run requires exactly one code file.\n")
            return
            
        self._log_to_terminal("Starting instant run...\n")
        RunCodeWindow(self.root, saved_files[0], auto_run=True)

    def _open_file_selection_window(self, file_paths):
        top = tk.Toplevel(self.root)
        top.title("Select File to Run")
        top.geometry("300x200")
        
        ttk.Label(top, text="Select which file to run:").pack(pady=10)
        
        for file_path in file_paths:
            filename = os.path.basename(file_path)
            btn = ttk.Button(
                top, 
                text=filename, 
                command=lambda fp=file_path: self._on_file_selected(fp, top)
            )
            btn.pack(pady=5)

    def _on_file_selected(self, file_path, selection_window):
        selection_window.destroy()
        self._log_to_terminal(f"Selected: {os.path.basename(file_path)}\n")
        RunCodeWindow(self.root, file_path, auto_run=False)
        
    def on_close(self):
        try:
            for filename in os.listdir(self.temp_dir):
                file_path = os.path.join(self.temp_dir, filename)
                try:
                    if os.path.isfile(file_path):
                        os.unlink(file_path)
                except Exception as e:
                    print(f"Error deleting {file_path}: {e}")
            os.rmdir(self.temp_dir)
        except Exception as e:
            print(f"Error cleaning up: {e}")
        self.root.destroy()

if __name__ == "__main__":
    root = tk.Tk()
    app = PromptGeneratorApp(root)
    root.mainloop()