import sys

file_path = 'c:/laragon/www/alkes-monitoring-bencana/resources/views/repairs/index.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

start_idx = 210
end_idx = 586

extracted_lines = lines[start_idx:end_idx+1]
del lines[start_idx:end_idx+1]

insert_idx = -1
for i, line in enumerate(lines):
    if 'id=\"modalTambah\"' in line:
        insert_idx = i
        break

if insert_idx != -1:
    wrapper_start = ['\n', '@foreach(\ as \)\n']
    wrapper_end = ['@endforeach\n', '\n']
    lines = lines[:insert_idx] + wrapper_start + extracted_lines + wrapper_end + lines[insert_idx:]
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.writelines(lines)
    print('Successfully moved modals out of the table.')
else:
    print('Could not find modalTambah.')
