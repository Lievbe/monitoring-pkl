import pathlib

p = pathlib.Path('resources/views/siswa/dashboard.blade.php')
lines = p.read_text(encoding='utf-8').split('\n')
lines = [l for l in lines if 'TEST_CSS_LINE' not in l]
p.write_text('\n'.join(lines) + '\n', encoding='utf-8')
print('cleaned OK')
