import os

# --- Configuration (Edit these lists to fit your needs) ---

# The name of the final combined output file.
OUTPUT_FILENAME = "combined_project_files.txt"

# The root directory to scan. "." means the current directory.
ROOT_DIRECTORY = "."

# Directories to completely exclude from both the tree and the content.
# 'vendor' and 'uploads' are common ones to add here if they are very large.
EXCLUDE_DIRS = {
    '.git',
    '.idea',
    '.vscode',
    '__pycache__',
    'node_modules',
    # 'vendor',  # Uncomment to exclude the vendor directory
    # 'uploads'  # Uncomment to exclude the uploads directory
}

# Specific files to exclude from the content.
EXCLUDE_FILES = {
    '.DS_Store',
    'composer.lock',
    'package-lock.json'
    # The script will automatically exclude its own output file.
}

# File extensions to exclude from having their content read.
# This is useful for binary files that would produce garbled text.
EXCLUDE_EXTENSIONS = {
    # Images
    '.png', '.jpg', '.jpeg', '.gif', '.bmp', '.ico', '.svg',
    # Archives
    '.zip', '.gz', '.tar', '.rar', '.7z',
    # Documents
    '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
    # Media
    '.mp3', '.mp4', '.avi', '.mov', '.flv',
    # Executables & compiled files
    '.exe', '.dll', '.so', '.a', '.lib', '.o', '.stackdump'
}

# --- Mapping file extensions to Markdown language identifiers ---
LANG_MAP = {
    '.py': 'python',
    '.php': 'php',
    '.js': 'javascript',
    '.html': 'html',
    '.css': 'css',
    '.json': 'json',
    '.sql': 'sql',
    '.md': 'markdown',
    '.sh': 'bash',
    '.xml': 'xml',
    '.yml': 'yaml',
    '.yaml': 'yaml',
    '.gitignore': 'text',
}

# --- Core Logic ---

def generate_tree_structure(root_path):
    """Generates a string representing the directory tree."""
    tree_lines = [f"Folder PATH listing for volume System\nVolume serial number is XXXX-XXXX"]
    tree_lines.append(f"{os.path.abspath(root_path)}")

    def recurse_tree(directory, prefix=""):
        try:
            # Get items, filter out excluded directories, and sort
            all_items = sorted(os.listdir(directory), key=lambda s: s.lower())
            items_to_process = [
                item for item in all_items
                if not (os.path.isdir(os.path.join(directory, item)) and item in EXCLUDE_DIRS)
            ]
        except OSError:
            return  # Could not access directory

        for i, name in enumerate(items_to_process):
            is_last = (i == len(items_to_process) - 1)
            connector = "└───" if is_last else "├───"
            tree_lines.append(f"{prefix}{connector}{name}")

            path = os.path.join(directory, name)
            if os.path.isdir(path):
                extension = "    " if is_last else "│   "
                recurse_tree(path, prefix + extension)

    recurse_tree(root_path)
    return "\n".join(tree_lines)

def main():
    """Main function to generate the combined file."""
    # Ensure the output file itself is excluded
    EXCLUDE_FILES.add(OUTPUT_FILENAME)

    with open(OUTPUT_FILENAME, 'w', encoding='utf-8', errors='ignore') as outfile:
        # --- 1. Generate and write the directory tree ---
        print("Generating directory tree...")
        tree_str = generate_tree_structure(ROOT_DIRECTORY)
        outfile.write(tree_str)
        outfile.write("\n\n\n") # Add spacing before file contents

        # --- 2. Walk through the directory and append file contents ---
        print("Combining file contents...")
        for root, dirs, files in os.walk(ROOT_DIRECTORY, topdown=True):
            # Modify dirs in-place to prevent walking into excluded directories
            dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]
            
            # Sort files for consistent order
            files.sort(key=lambda s: s.lower())

            for filename in files:
                # Check if the file should be excluded
                if filename in EXCLUDE_FILES:
                    continue
                
                _, ext = os.path.splitext(filename)
                if ext in EXCLUDE_EXTENSIONS:
                    continue

                file_path = os.path.join(root, filename)
                relative_path = os.path.relpath(file_path, ROOT_DIRECTORY).replace(os.sep, '/')

                # Write file header
                outfile.write(f"[File Name: {relative_path}]\n")
                
                # Determine language for markdown code block
                lang = LANG_MAP.get(ext, '')
                outfile.write(f"```{lang}\n")
                
                # Write file content with error handling for binary files
                try:
                    with open(file_path, 'r', encoding='utf-8') as infile:
                        content = infile.read()
                        outfile.write(content)
                except UnicodeDecodeError:
                    outfile.write(f"[Content could not be read as text (likely a binary or non-UTF-8 file).]")
                except Exception as e:
                    outfile.write(f"[Error reading file: {e}]")
                
                outfile.write(f"\n```\n\n")

    print(f"✅ Successfully combined project files into '{OUTPUT_FILENAME}'")

if __name__ == "__main__":
    main()