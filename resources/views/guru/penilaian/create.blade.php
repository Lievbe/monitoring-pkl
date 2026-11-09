<x-app-layout>
    <x-slot name="header">
        <div class="rounded-xl border border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Guru Pembimbing
                    </div>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        Penilaian Siswa PKL
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Berikan evaluasi kompetensi hard skill dan soft skill untuk siswa bimbingan.
                    </p>
                </div>
                <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700">
                    &larr; Kembali ke dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-semibold">Periksa kembali penilaian:</p>
                <ul class="mt-1.5 list-inside list-disc space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 border-b border-slate-100 pb-5">
                <h2 class="text-lg font-bold text-slate-900">{{ $pklSiswa->siswa?->siswaProfile?->nama_lengkap ?? $pklSiswa->siswa?->name }}</h2>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $pklSiswa->siswa?->siswaProfile?->kelas }} · {{ $pklSiswa->nama_perusahaan ?: ($pklSiswa->industri?->industriProfile?->nama_perusahaan ?? '—') }}
                </p>
            </div>

            <form method="POST" action="{{ route('guru.penilaian.store', $pklSiswa) }}" class="space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="hard_skill" value="Hard Skill (0–100)" />
                        <x-text-input id="hard_skill" name="hard_skill" type="number" min="0" max="100" class="mt-1 block w-full" :value="old('hard_skill', $pklSiswa->penilaian?->hard_skill)" required />
                        <x-input-error :messages="$errors->get('hard_skill')" class="mt-1.5" />
                    </div>
                    <div>
                        <x-input-label for="soft_skill" value="Soft Skill (0–100)" />
                        <x-text-input id="soft_skill" name="soft_skill" type="number" min="0" max="100" class="mt-1 block w-full" :value="old('soft_skill', $pklSiswa->penilaian?->soft_skill)" required />
                        <x-input-error :messages="$errors->get('soft_skill')" class="mt-1.5" />
                    </div>
                </div>

                <div class="rounded-lg border border-blue-100 bg-blue-50/70 p-4 text-xs leading-5 text-blue-900">
                    <strong class="font-semibold text-blue-800">Rumus Penilaian:</strong> Nilai akhir dihitung otomatis saat disimpan (60% hard skill + 40% soft skill).<br>
                    Predikat: <strong>A</strong> (85–100), <strong>B</strong> (75–84), <strong>C</strong> (65–74), <strong>D</strong> (55–64), <strong>E</strong> (&lt;55).
                </div>

                <div>
                    <x-input-label for="tanggal_penilaian" value="Tanggal Penilaian" />
                    <x-text-input id="tanggal_penilaian" name="tanggal_penilaian" type="date" class="mt-1 block w-full" :value="old('tanggal_penilaian', $pklSiswa->penilaian?->tanggal_penilaian?->toDateString() ?? now()->toDateString())" required />
                    <x-input-error :messages="$errors->get('tanggal_penilaian')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="catatan" value="Catatan untuk Siswa (opsional)" />
                    <textarea id="catatan" name="catatan" rows="4" maxlength="2000"
                        placeholder="Berikan masukan, evaluasi performa, atau saran pengembangan bagi siswa..."
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white p-3 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">{{ old('catatan', $pklSiswa->penilaian?->catatan) }}</textarea>
                    <x-input-error :messages="$errors->get('catatan')" class="mt-1.5" />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('guru.dashboard') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
