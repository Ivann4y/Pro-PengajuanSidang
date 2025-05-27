import tkinter as tk
from tkinter import ttk, filedialog, messagebox
import os
import webbrowser
import sys # Added for sys.executable and sys.frozen

class ProjectTreeApp:
    def __init__(self, root):
        self.root = root
        self.root.title("PHP Project Tree Viewer")
        self.root.geometry("800x600")

        self.htdocs_root = None
        self.start_dir_selected = None
        self.project_root_name_to_find = "Pro-PengajuanSidang" # Target project folder

        # --- Menu ---
        menubar = tk.Menu(root)
        filemenu = tk.Menu(menubar, tearoff=0)
        filemenu.add_command(label="Select Project Directory...", command=self.select_directory)
        filemenu.add_separator()
        filemenu.add_command(label="Exit", command=root.quit)
        menubar.add_cascade(label="File", menu=filemenu)

        helpmenu = tk.Menu(menubar, tearoff=0)
        helpmenu.add_command(label="How to Package (.exe)...", command=self.show_packaging_info)
        menubar.add_cascade(label="Help", menu=helpmenu)
        root.config(menu=menubar)

        # --- Main Frame ---
        main_frame = ttk.Frame(root, padding="10")
        main_frame.pack(expand=True, fill=tk.BOTH)

        # --- Treeview Frame (for scrollbars) ---
        tree_frame = ttk.Frame(main_frame)
        tree_frame.pack(expand=True, fill=tk.BOTH)

        self.tree = ttk.Treeview(tree_frame, show="tree") # show="tree" hides the default "#0" column header
        
        # Scrollbars
        ysb = ttk.Scrollbar(tree_frame, orient=tk.VERTICAL, command=self.tree.yview)
        xsb = ttk.Scrollbar(tree_frame, orient=tk.HORIZONTAL, command=self.tree.xview)
        self.tree.configure(yscrollcommand=ysb.set, xscrollcommand=xsb.set)

        ysb.pack(side=tk.RIGHT, fill=tk.Y)
        xsb.pack(side=tk.BOTTOM, fill=tk.X)
        self.tree.pack(side=tk.LEFT, expand=True, fill=tk.BOTH)

        # --- Treeview Styling and Bindings ---
        self.tree.tag_configure('php_file', foreground='blue', font=('TkDefaultFont', 9, 'underline'))
        self.tree.tag_bind('php_file', '<Button-1>', self.on_php_file_click) # Single click

        self.tree.bind('<Motion>', self.on_tree_motion)
        self.tree.bind('<Leave>', lambda e: self.tree.config(cursor=""))
        
        # --- Status Bar ---
        self.status_var = tk.StringVar()
        self.status_var.set("Initializing...")
        status_bar = ttk.Label(root, textvariable=self.status_var, relief=tk.SUNKEN, anchor=tk.W, padding="2 5")
        status_bar.pack(side=tk.BOTTOM, fill=tk.X)

        # --- Attempt to auto-load the project on startup ---
        self.auto_load_project()


    def auto_load_project(self):
        try:
            base_dir_for_search = ""
            if getattr(sys, 'frozen', False):
                # Running as a PyInstaller bundle (sys.executable is the path to the .exe)
                base_dir_for_search = os.path.dirname(sys.executable)
            else:
                # Running as a Python script (__file__ is the path to this script)
                base_dir_for_search = os.path.dirname(os.path.abspath(__file__))

            if not base_dir_for_search: # Should not happen if os/sys paths are valid
                self.status_var.set("Error: Could not determine application's base directory. Please select manually.")
                return

            current_search_path = os.path.abspath(base_dir_for_search)
            found_project_root = None

            # Traverse upwards to find the project root directory (self.project_root_name_to_find)
            # Limit to a reasonable number of levels (e.g., 10) to prevent infinite loops
            for _ in range(10): # Max 10 levels up
                if os.path.basename(current_search_path).lower() == self.project_root_name_to_find.lower():
                    found_project_root = current_search_path
                    break
                
                parent_path = os.path.dirname(current_search_path)
                if parent_path == current_search_path:  # Reached filesystem root (e.g., "C:\" or "/")
                    break # Stop searching, project root not found in upward path
                current_search_path = parent_path
            
            if found_project_root:
                self.start_dir_selected = found_project_root
                self.htdocs_root = self.find_htdocs_path(self.start_dir_selected)
                
                if self.htdocs_root:
                    self.status_var.set(f"Auto-loaded: {self.start_dir_selected} | htdocs: {self.htdocs_root}")
                else:
                    self.status_var.set(f"Auto-loaded: {self.start_dir_selected} | WARNING: 'htdocs' not found for URL generation.")
                    self.htdocs_root = self.start_dir_selected # Fallback, as in original code
                self.populate_tree()
            else:
                self.status_var.set(f"Could not auto-find '{self.project_root_name_to_find}' from '{base_dir_for_search}'. Please use File > Select Project Directory...")
        except Exception as e:
            self.status_var.set(f"Error during auto-load: {e}. Please select directory manually.")
            # For debugging, you might want to print the traceback too:
            # import traceback
            # traceback.print_exc()


    def find_htdocs_path(self, start_path_abs):
        current_path = start_path_abs
        while True:
            if os.path.basename(current_path).lower() == 'htdocs':
                return current_path
            parent_path = os.path.dirname(current_path)
            if parent_path == current_path: # Reached filesystem root
                if os.path.basename(start_path_abs).lower() == 'htdocs': # Original check
                    return start_path_abs
                return None
            current_path = parent_path

    def select_directory(self):
        dir_path = filedialog.askdirectory(title="Select Your Project's Root Folder")
        if dir_path:
            self.start_dir_selected = os.path.abspath(dir_path)
            self.htdocs_root = self.find_htdocs_path(self.start_dir_selected)
            
            if self.htdocs_root:
                self.status_var.set(f"Selected: {self.start_dir_selected} | htdocs: {self.htdocs_root}")
            else:
                self.status_var.set(f"Selected: {self.start_dir_selected} | WARNING: 'htdocs' not found. PHP links might be incorrect.")
                self.htdocs_root = self.start_dir_selected 
            
            self.populate_tree()

    def populate_tree(self):
        for i in self.tree.get_children():
            self.tree.delete(i)

        if not self.start_dir_selected:
            return

        root_node_text = os.path.basename(self.start_dir_selected)
        root_node_iid = self.start_dir_selected
        
        self.tree.insert("", "end", text=f"{root_node_text}/", iid=root_node_iid, open=True, values=('dir', self.start_dir_selected))
        self._build_tree_recursive(self.start_dir_selected, root_node_iid)


    def _build_tree_recursive(self, current_dir_path, parent_iid):
        try:
            items = sorted(os.listdir(current_dir_path), key=lambda s: s.lower())
        except PermissionError:
            self.tree.insert(parent_iid, 'end', text="(Permission Denied)", values=('error',))
            return
        except FileNotFoundError:
            self.tree.insert(parent_iid, 'end', text="(File Not Found / Inaccessible)", values=('error',))
            return

        dirs = []
        files = []
        for item_name in items:
            # ---- IGNORE 'extra' FOLDER and '.git' and other common ones ----
            if item_name.lower() in ['extra', '.git', '.vscode', '__pycache__', 'node_modules', '.DS_Store']:
                continue # Skip this item
            # ---- Also ignore specific files like .gitignore ----
            if item_name.lower() in ['.gitignore', 'thumbs.db']:
                continue

            item_path = os.path.join(current_dir_path, item_name)
            if os.path.isdir(item_path):
                dirs.append((item_name, item_path))
            else:
                files.append((item_name, item_path))

        for dir_name, dir_path in dirs:
            dir_node_iid = dir_path 
            inserted_id = self.tree.insert(parent_iid, 'end', text=f"{dir_name}/", iid=dir_node_iid, open=False, values=('dir', dir_path))
            self._build_tree_recursive(dir_path, inserted_id)

        for file_name, file_path in files:
            item_iid = file_path
            if file_name.lower().endswith('.php'):
                if self.htdocs_root:
                    try:
                        relative_path = os.path.relpath(file_path, self.htdocs_root)
                        browser_url = f"http://localhost/{relative_path.replace(os.sep, '/')}"
                        self.tree.insert(parent_iid, 'end', text=file_name, iid=item_iid,
                                         values=('php', browser_url), tags=('php_file',))
                    except ValueError:
                        self.tree.insert(parent_iid, 'end', text=f"{file_name} (Error: Not in htdocs path)", iid=item_iid,
                                         values=('error', file_path))
                else:
                     self.tree.insert(parent_iid, 'end', text=f"{file_name} (Error: htdocs root not defined)", iid=item_iid,
                                     values=('error', file_path))
            else:
                # Only list specific known file types or exclude some, for cleaner tree
                # For now, listing all non-php files.
                # if file_name.lower().endswith(('.css', '.js', '.html', '.png', '.jpg', '.jpeg', '.gif', '.txt', '.md')):
                self.tree.insert(parent_iid, 'end', text=file_name, iid=item_iid,
                                values=('file', file_path))

    def on_php_file_click(self, event):
        item_id = self.tree.identify_row(event.y)
        if not item_id:
            return

        item_tags = self.tree.item(item_id, 'tags')
        if 'php_file' in item_tags:
            item_values = self.tree.item(item_id, 'values')
            if item_values and len(item_values) > 1 and item_values[0] == 'php':
                url = item_values[1]
                try:
                    webbrowser.open_new_tab(url)
                    self.status_var.set(f"Opening: {url}")
                except Exception as e:
                    self.status_var.set(f"Error opening URL: {e}")
                    messagebox.showerror("Browser Error", f"Could not open URL: {url}\n{e}")
            else:
                self.status_var.set("Clicked PHP item has no URL associated.")
        # else: # No need for this else, as non-php items won't have the tag
        #     self.status_var.set("Clicked item is not a configured PHP link.")

    def on_tree_motion(self, event):
        item_id = self.tree.identify_row(event.y)
        if item_id:
            tags = self.tree.item(item_id, 'tags')
            if 'php_file' in tags:
                self.tree.config(cursor="hand2")
            else:
                self.tree.config(cursor="")
        else:
            self.tree.config(cursor="")

    def show_packaging_info(self):
        info = """
How to Package this Script into an Executable (.exe for Windows):

1. Ensure Python is installed on your system.
2. Open a command prompt (CMD) or terminal.
3. Install PyInstaller (if you haven't already):
   pip install pyinstaller

4. Navigate to the directory where you saved this script (e.g., script_name.py).
   Example: cd C:\\path\\to\\your\\script

5. Run the PyInstaller command:
   pyinstaller --onefile --windowed script_name.py

   - `--onefile`: Bundles everything into a single executable file.
   - `--windowed`: Prevents a console window from appearing when your GUI app runs.
                  (For some systems or older PyInstaller, --noconsole might be used instead,
                   especially if targeting Windows specifically.)
   - Replace `script_name.py` with the actual name of this Python file.

6. PyInstaller will create a 'dist' folder in the script's directory.
   Inside 'dist', you'll find 'script_name.exe' (or similar).
   This is your standalone executable.
        """
        messagebox.showinfo("How to Package", info)

if __name__ == "__main__":
    root = tk.Tk()
    app = ProjectTreeApp(root)
    root.mainloop()