<x-admin-layout>
    <x-slot name="header">
        <div class="rounded-xl border border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Admin Hubin
                    </div>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        Edit Data Penempatan PKL
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Perbarui informasi siswa, guru pembimbing, mitra industri, atau periode PKL.
                    </p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700">
                    &larr; Kembali ke dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-semibold">Data belum dapat disimpan:</p>
                <ul class="mt-1.5 list-inside list-disc space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-50/50 px-6 py-4">
                <h2 class="font-bold text-base text-slate-900">{{ $placement->siswa?->siswaProfile?->nama_lengkap ?? $placement->siswa?->name }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">Perbarui rincian data penempatan PKL siswa.</p>
            </div>

            <form method="POST" action="{{ route('admin.placements.update', $placement) }}" class="grid gap-5 p-6 sm:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="sm:col-span-2">
                    <x-input-label for="siswa_id" value="Siswa" />
                    <select id="siswa_id" name="siswa_id" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected(old('siswa_id', $placement->siswa_id) == $student->id)>{{ $student->siswaProfile?->nama_lengkap ?? $student->name }} — {{ $student->siswaProfile?->kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="guru_id" value="Akun guru pembimbing" />
                    <select id="guru_id" name="guru_id" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                        <option value="">Pilih guru pembimbing</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(old('guru_id', $placement->guru_id) == $teacher->id)>{{ $teacher->guruProfile?->nama_lengkap ?? $teacher->name }}{{ $teacher->guruProfile?->nip ? ' — NIP '.$teacher->guruProfile->nip : '' }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('guru_id')" class="mt-1.5" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="industri_id" value="Akun perusahaan mitra (opsional)" />
                    <select id="industri_id" name="industri_id" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                        <option value="">Tanpa akun perusahaan</option>
                        @foreach ($industries as $industry)
                            <option value="{{ $industry->id }}" data-company="{{ $industry->industriProfile?->nama_perusahaan }}" data-mentor="{{ $industry->industriProfile?->nama_pembimbing }}" @selected(old('industri_id', $placement->industri_id) == $industry->id)>{{ $industry->industriProfile?->nama_perusahaan ?: $industry->name }} — {{ $industry->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('industri_id')" class="mt-1.5" />
                </div>
                <div>
                    <x-input-label for="nama_perusahaan" value="Nama Perusahaan / Mitra" />
                    <x-text-input id="nama_perusahaan" name="nama_perusahaan" type="text" class="mt-1 block w-full" :value="old('nama_perusahaan', $placement->nama_perusahaan ?: $placement->industri?->industriProfile?->nama_perusahaan)" required />
                    <x-input-error :messages="$errors->get('nama_perusahaan')" class="mt-1.5" />
                </div>
                <div>
                    <x-input-label for="nama_pembimbing" value="Nama Pembimbing Mitra" />
                    <x-text-input id="nama_pembimbing" name="nama_pembimbing" type="text" class="mt-1 block w-full" :value="old('nama_pembimbing', $placement->nama_pembimbing ?: $placement->industri?->industriProfile?->nama_pembimbing)" required />
                </div>
                <div>
                    <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" :value="old('tanggal_mulai', $placement->tanggal_mulai->toDateString())" required />
                </div>
                <div>
                    <x-input-label for="tanggal_selesai" value="Tanggal Selesai" />
                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" :value="old('tanggal_selesai', $placement->tanggal_selesai->toDateString())" required />
                    <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-1.5" />
                </div>
                <div>
                    <x-input-label for="status" value="Status PKL" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                        <option value="pending" @selected(old('status', $placement->status) === 'pending')>Menunggu</option>
                        <option value="aktif" @selected(old('status', $placement->status) === 'aktif')>Aktif</option>
                        <option value="selesai" @selected(old('status', $placement->status) === 'selesai')>Selesai</option>
                        <option value="dibatalkan" @selected(old('status', $placement->status) === 'dibatalkan')>Dibatalkan</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="catatan" value="Catatan (opsional)" />
                    <textarea id="catatan" name="catatan" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white p-3 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">{{ old('catatan', $placement->catatan) }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3 pt-2 sm:col-span-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.dashboard') }}#penempatan" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batal
                    </a>
                </div>
            </form>
            @if ($placement->logbooks_count === 0 && ! $placement->penilaian && ! $placement->sertifikat)
                <div class="border-t border-slate-100 px-6 py-4"><form method="POST" action="{{ route('admin.placements.destroy', $placement) }}">@csrf @method('DELETE')<button type="button" data-delete-trigger data-delete-name="penempatan {{ $placement->siswa?->siswaProfile?->nama_lengkap ?: $placement->siswa?->name }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-red-200 px-4 text-sm font-bold text-red-600 hover:bg-red-50"><i data-lucide="trash-2" class="h-4 w-4"></i>Hapus Penempatan</button></form></div>
            @endif
        </section>
    </div>
    <script>
        document.getElementById('industri_id').addEventListener('change', (event) => {
            const option = event.target.selectedOptions[0];
            if (option.value) {
                document.getElementById('nama_perusahaan').value = option.dataset.company || '';
                document.getElementById('nama_pembimbing').value = option.dataset.mentor || '';
            }
        });
    </script>
</x-admin-layout>
