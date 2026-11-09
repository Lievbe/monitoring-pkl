<x-app-layout>
    <x-slot name="header">
        <div class="mx-auto max-w-4xl">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Monitoring Hub</a>
            <p class="mt-5 text-xs font-bold uppercase tracking-[0.14em] text-blue-600">Kelola penempatan</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Penempatan PKL</h1>
            <p class="mt-2 text-sm text-slate-500">Masukkan nama guru pembimbing dan mitra secara langsung, tanpa membuat akun tambahan.</p>
        </div>
    </x-slot>

    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-semibold">Periksa kembali data yang diisi.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($students->isEmpty())
            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900 sm:flex-row sm:items-center sm:justify-between">
                <p>Belum ada siswa yang terdaftar. Daftarkan siswa terlebih dahulu sebelum membuat penempatan.</p>
                <a href="{{ route('admin.students.create') }}" class="w-fit rounded-lg bg-blue-600 px-4 py-2.5 font-semibold text-white hover:bg-blue-700">Daftarkan siswa</a>
            </div>
        @endif

        <section class="rounded-xl border border-slate-200/80 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                <h2 class="font-semibold text-slate-900">Detail penempatan</h2>
                <p class="mt-1 text-sm text-slate-500">Pilih siswa, lalu lengkapi informasi pembimbing dan periode PKL.</p>
            </div>
            <form method="POST" action="{{ route('admin.placements.store') }}" class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                @csrf
                <div class="sm:col-span-2">
                    <x-input-label for="siswa_id" value="Siswa" class="text-slate-800" />
                    <select id="siswa_id" name="siswa_id" required @disabled($students->isEmpty()) class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-slate-100">
                        <option value="">Pilih siswa dari daftar</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected(old('siswa_id') == $student->id)>{{ $student->siswaProfile?->nama_lengkap ?? $student->name }} — {{ $student->siswaProfile?->kelas }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('siswa_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_nama" value="Nama guru pembimbing" class="text-slate-800" />
                    <x-text-input id="guru_nama" name="guru_nama" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('guru_nama')" placeholder="Contoh: Ibu Siti Rahmawati" required />
                    <x-input-error :messages="$errors->get('guru_nama')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guru_nip" value="NIP guru (opsional)" class="text-slate-800" />
                    <x-text-input id="guru_nip" name="guru_nip" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('guru_nip')" />
                </div>
                <div>
                    <x-input-label for="nama_perusahaan" value="Nama perusahaan / mitra" class="text-slate-800" />
                    <x-text-input id="nama_perusahaan" name="nama_perusahaan" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('nama_perusahaan')" placeholder="Contoh: PT Maju Bersama" required />
                    <x-input-error :messages="$errors->get('nama_perusahaan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama_pembimbing" value="Nama pembimbing mitra" class="text-slate-800" />
                    <x-text-input id="nama_pembimbing" name="nama_pembimbing" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('nama_pembimbing')" placeholder="Contoh: Bapak Andi" required />
                    <x-input-error :messages="$errors->get('nama_pembimbing')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tanggal_mulai" value="Tanggal mulai" class="text-slate-800" />
                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('tanggal_mulai')" required />
                </div>
                <div>
                    <x-input-label for="tanggal_selesai" value="Tanggal selesai" class="text-slate-800" />
                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('tanggal_selesai')" required />
                    <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" value="Status awal" class="text-slate-800" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pending" @selected(old('status', 'aktif') === 'pending')>Menunggu</option>
                        <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                        <option value="selesai" @selected(old('status') === 'selesai')>Selesai</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="catatan" value="Catatan (opsional)" class="text-slate-800" />
                    <textarea id="catatan" name="catatan" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan') }}</textarea>
                </div>
                <div class="flex flex-wrap gap-3 pt-2 sm:col-span-2">
                    <button type="submit" @disabled($students->isEmpty()) class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-300">Simpan penempatan</button>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
                </div>
            </form>
        </section>
    </main>
</x-app-layout>
