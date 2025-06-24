import os
import re

# --- Configuration (Edit these lists to fit your needs) ---

# The name of the final combined output file.
OUTPUT_FILENAME = "combined_project_files.txt"

# The root directory to scan. "." means the current directory.
ROOT_DIRECTORY = "."

# Directories to completely exclude from both the tree and the content.
EXCLUDE_DIRS = {
    '.git',
    '.idea',
    '.vscode',
    '__pycache__',
    'node_modules',
    'vendor',
    'uploads' 
}

# Specific files to exclude from the content.
EXCLUDE_FILES = {
    '.DS_Store',
    'composer.lock',
    'package-lock.json'
    # The script will automatically exclude its own output file.
}

# File extensions to exclude from having their content read.
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
            all_items = sorted(os.listdir(directory), key=lambda s: s.lower())
            items_to_process = [
                item for item in all_items
                if not (os.path.isdir(os.path.join(directory, item)) and item in EXCLUDE_DIRS)
            ]
        except OSError:
            return

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

def is_excluded(file_path):
    """Check if a file or its directory should be excluded."""
    if os.path.basename(file_path) in EXCLUDE_FILES:
        return True
    if os.path.splitext(file_path)[1] in EXCLUDE_EXTENSIONS:
        return True
    
    parts = file_path.split(os.sep)
    if any(part in EXCLUDE_DIRS for part in parts):
        return True
        
    return False

def find_dependencies(file_content, current_file_dir):
    """Finds local file dependencies in the content of a file."""
    # Regex to find href, src, include, require, etc.
    # It captures the path inside single or double quotes.
    pattern = re.compile(
        r'(?:include|require|include_once|require_once|href|src)\s*=\s*["\']([^"\']+)["\']|'
        r'(?:include|require|include_once|require_once)\s*[\(\s]["\']([^"\']+)["\']'
    )
    matches = pattern.findall(file_content)
    
    dependencies = []
    for match in matches:
        # The regex returns a tuple, one part for each group. We need the non-empty one.
        path = next((s for s in match if s), None)
        if not path:
            continue
            
        # Ignore absolute URLs and network paths
        if re.match(r'^(https?|ftp)://|//', path):
            continue
            
        # Resolve the relative path to an absolute path
        dependency_path = os.path.join(current_file_dir, path)
        # Normalize the path (e.g., turn dir/../file into file)
        normalized_path = os.path.normpath(dependency_path)
        
        if os.path.isfile(normalized_path) and not is_excluded(normalized_path):
            dependencies.append(normalized_path)
            
    return dependencies

def write_file_content_to_output(outfile, file_path):
    """Writes a single file's content into the output file with formatting."""
    relative_path = os.path.relpath(file_path, ROOT_DIRECTORY).replace(os.sep, '/')
    
    # Write file header
    outfile.write(f"[File Name: {relative_path}]\n")
    
    # Determine language for markdown code block
    _, ext = os.path.splitext(file_path)
    lang = LANG_MAP.get(ext, '')
    outfile.write(f"```{lang}\n")
    
    # Write file content with error handling
    try:
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as infile:
            content = infile.read()
            outfile.write(content)
            return content
    except Exception as e:
        outfile.write(f"[Error reading file: {e}]")
    finally:
        outfile.write(f"\n```\n\n")
    return ""

def main():
    """Main function to generate the combined file."""
    # Ensure the output file itself is excluded
    EXCLUDE_FILES.add(OUTPUT_FILENAME)

    target_input = input(
        "Enter the main file path to analyze (or enter '0' to combine all files): "
    ).strip()

    with open(OUTPUT_FILENAME, 'w', encoding='utf-8', errors='ignore') as outfile:
        # --- 1. Generate and write the directory tree (always) ---
        print("Generating directory tree...")
        tree_str = generate_tree_structure(ROOT_DIRECTORY)
        outfile.write(tree_str)
        outfile.write("\n\n\n")

        # --- 2. Process files based on input ---
        if target_input == '0':
            # --- Original "Combine All" Logic ---
            print("Combining all project files (mode 0)...")
            for root, dirs, files in os.walk(ROOT_DIRECTORY, topdown=True):
                dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]
                files.sort(key=lambda s: s.lower())

                for filename in files:
                    file_path = os.path.join(root, filename)
                    if not is_excluded(file_path):
                        print(f"  Adding {file_path}")
                        write_file_content_to_output(outfile, file_path)
        else:
            # --- New "Focused" Logic ---
            if not os.path.isfile(target_input):
                print(f"❌ Error: The file '{target_input}' does not exist. Aborting.")
                return

            print(f"Analyzing dependencies for '{target_input}'...")
            
            files_to_process = [os.path.normpath(target_input)]
            processed_files = set()
            
            while files_to_process:
                current_file = files_to_process.pop(0)
                if current_file in processed_files:
                    continue
                
                processed_files.add(current_file)
                print(f"  Processing and adding {current_file}")

                content = write_file_content_to_output(outfile, current_file)

                if content:
                    current_dir = os.path.dirname(current_file)
                    dependencies = find_dependencies(content, current_dir)
                    for dep in dependencies:
                        if dep not in processed_files:
                            files_to_process.append(dep)

    print(f"\n✅ Successfully combined project files into '{OUTPUT_FILENAME}'")

if __name__ == "__main__":
    main()