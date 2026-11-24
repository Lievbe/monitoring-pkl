<x-guru-layout>
    <x-slot name="header">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 border border-slate-200">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Penilaian PKL
                    </div>
                    <h1 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Beri Nilai Siswa</h1>
                    <p class="mt-1 text-sm text-slate-600">Penilaian dibagi menjadi 2 aspek: <span class="font-medium text-slate-800">Sikap</span> dan <span class="font-medium text-slate-800">Kompetensi</span> (teknis).</p>
                </div>
                <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition">&larr; Kembali ke dashboard</a>
            </div>
        </div>
    </x-slot>

    @php
        $prevSikap = (int) old('soft_skill', $pklSiswa->penilaian?->soft_skill ?? 75);
        $prevKompetensi = (int) old('hard_skill', $pklSiswa->penilaian?->hard_skill ?? 75);
        $sikapDefaults = [];
        foreach ($sikapIndicators as $k => $label) {
            $sikapDefaults[$k] = (int) old("sikap.$k", $prevSikap);
        }
        $kompetensiDefaults = [];
        foreach ($kompetensiIndicators as $k => $label) {
            $kompetensiDefaults[$k] = (int) old("kompetensi.$k", $prevKompetensi);
        }
    @endphp

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
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

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">{{ $pklSiswa->siswa?->siswaProfile?->nama_lengkap ?? $pklSiswa->siswa?->name }}</h2>
                    <p class="mt-0.5 text-sm text-slate-500">{{ $pklSiswa->siswa?->siswaProfile?->kelas }} · {{ $pklSiswa->nama_perusahaan ?: ($pklSiswa->industri?->industriProfile?->nama_perusahaan ?? '—') }}</p>
                </div>
                <div class="text-xs text-slate-500">Periode: {{ $pklSiswa->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }} – {{ $pklSiswa->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</div>
            </div>
            <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                <p class="font-semibold text-blue-900">Panduan singkat (rubrik umum PKL SMK)</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs leading-relaxed text-blue-800">
                    <li><span class="font-medium">Sikap</span>: kedisiplinan & kehadiran, tanggung jawab, komunikasi & kerja sama, etika/sikap kerja.</li>
                    <li><span class="font-medium">Kompetensi</span>: penguasaan teknis & tugas, kualitas hasil, penerapan prosedur & K3, kemandirian & efisiensi.</li>
                    <li>Skala 0–100 per indikator. Rata-rata tiap aspek dihitung otomatis. Nilai akhir = 60% Kompetensi + 40% Sikap.</li>
                    <li>Predikat: A Sangat Baik (≥85), B Baik (75–84), C Cukup (65–74), D Kurang (55–64), E Sangat Kurang (&lt;55).</li>
                </ul>
            </div>
        </section>

        <form method="POST" action="{{ route('guru.penilaian.store', $pklSiswa) }}" class="space-y-6" id="form-penilaian">
            @csrf

            <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-amber-50/60 px-5 py-4 sm:px-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">1. Penilaian Sikap</h3>
                            <p class="text-sm text-slate-600">Non-teknis: perilaku, kedisiplinan, dan cara bekerja.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Rata-rata Sikap</p>
                            <p class="text-2xl font-semibold text-amber-700"><span id="avg-sikap">{{ $prevSikap }}</span><span class="text-sm font-medium text-slate-500">/100</span></p>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($sikapIndicators as $key => $label)
                        <div class="grid gap-3 px-5 py-4 sm:grid-cols-[1fr,220px] sm:items-center sm:px-6">
                            <div>
                                <label for="sikap_{{ $key }}" class="block text-sm font-medium text-slate-800">{{ $label }}</label>
                                <x-input-error :messages="$errors->get('sikap.'.$key)" class="mt-1" />
                            </div>
                            <div class="flex items-center gap-3">
                                <input id="sikap_{{ $key }}" name="sikap[{{ $key }}]" type="range" min="0" max="100" step="1" value="{{ $sikapDefaults[$key] }}" class="sikap-input h-2 w-full cursor-pointer accent-amber-600" data-target="sikap_val_{{ $key }}">
                                <input id="sikap_val_{{ $key }}" type="number" min="0" max="100" value="{{ $sikapDefaults[$key] }}" class="sikap-num w-20 rounded-md border-slate-300 text-center text-sm font-semibold text-slate-800 shadow-sm focus:border-amber-500 focus:ring-amber-500" data-source="sikap_{{ $key }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-blue-50/60 px-5 py-4 sm:px-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">2. Penilaian Kompetensi</h3>
                            <p class="text-sm text-slate-600">Teknis: kemampuan dan hasil kerja sesuai bidang PKL.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Rata-rata Kompetensi</p>
                            <p class="text-2xl font-semibold text-blue-700"><span id="avg-kompetensi">{{ $prevKompetensi }}</span><span class="text-sm font-medium text-slate-500">/100</span></p>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($kompetensiIndicators as $key => $label)
                        <div class="grid gap-3 px-5 py-4 sm:grid-cols-[1fr,220px] sm:items-center sm:px-6">
                            <div>
                                <label for="kompetensi_{{ $key }}" class="block text-sm font-medium text-slate-800">{{ $label }}</label>
                                <x-input-error :messages="$errors->get('kompetensi.'.$key)" class="mt-1" />
                            </div>
                            <div class="flex items-center gap-3">
                                <input id="kompetensi_{{ $key }}" name="kompetensi[{{ $key }}]" type="range" min="0" max="100" step="1" value="{{ $kompetensiDefaults[$key] }}" class="kompetensi-input h-2 w-full cursor-pointer accent-blue-600" data-target="kompetensi_val_{{ $key }}">
                                <input id="kompetensi_val_{{ $key }}" type="number" min="0" max="100" value="{{ $kompetensiDefaults[$key] }}" class="kompetensi-num w-20 rounded-md border-slate-300 text-center text-sm font-semibold text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500" data-source="kompetensi_{{ $key }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm sm:p-6">
                <div class="grid gap-4 sm:grid-cols-3 sm:items-end">
                    <div class="rounded-lg bg-white border border-slate-200 p-4 text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Nilai Sikap</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900"><span id="sum-sikap">{{ $prevSikap }}</span></p>
                        <p class="text-xs text-slate-500">bobot 40%</p>
                    </div>
                    <div class="rounded-lg bg-white border border-slate-200 p-4 text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Nilai Kompetensi</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900"><span id="sum-kompetensi">{{ $prevKompetensi }}</span></p>
                        <p class="text-xs text-slate-500">bobot 60%</p>
                    </div>
                    <div class="rounded-lg bg-blue-600 p-4 text-center text-white">
                        <p class="text-xs font-medium uppercase tracking-wider text-blue-100">Nilai Akhir</p>
                        <p class="mt-1 text-3xl font-semibold"><span id="sum-akhir">{{ (int) round($prevKompetensi*0.6 + $prevSikap*0.4) }}</span></p>
                        <p class="text-sm font-medium text-blue-100">Predikat <span id="sum-predikat">-</span></p>
                    </div>
                </div>
                <p class="mt-3 text-center text-xs text-slate-500">Nilai akhir dan predikat dihitung otomatis saat disimpan (berdasarkan rata-rata indikator).</p>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 space-y-5">
                <div>
                    <x-input-label for="tanggal_penilaian" value="Tanggal Penilaian" />
                    <x-text-input id="tanggal_penilaian" name="tanggal_penilaian" type="date" class="mt-1 block w-full sm:max-w-xs" :value="old('tanggal_penilaian', $pklSiswa->penilaian?->tanggal_penilaian?->toDateString() ?? now()->toDateString())" required />
                    <x-input-error :messages="$errors->get('tanggal_penilaian')" class="mt-1.5" />
                </div>
                <div>
                    <x-input-label for="catatan" value="Catatan untuk Siswa (opsional)" />
                    <textarea id="catatan" name="catatan" rows="4" maxlength="2000" placeholder="Berikan masukan, evaluasi, atau saran pengembangan sikap dan kompetensi siswa..." class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan', $pklSiswa->penilaian?->catatan) }}</textarea>
                    <x-input-error :messages="$errors->get('catatan')" class="mt-1.5" />
                </div>
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('guru.dashboard') }}" class="inline-flex justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                    <button type="submit" class="inline-flex justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Simpan Penilaian</button>
                </div>
            </section>
        </form>
    </div>

    <script>
        (function () {
            function clamp(v){ v = parseInt(v, 10); if (isNaN(v)) v = 0; return Math.min(100, Math.max(0, v)); }
            function avg(selector){ var els = document.querySelectorAll(selector); if(!els.length) return 0; var s=0; els.forEach(function(e){ s += clamp(e.value); }); return Math.round(s / els.length); }
            function predikat(n){ if(n>=85) return 'A (Sangat Baik)'; if(n>=75) return 'B (Baik)'; if(n>=65) return 'C (Cukup)'; if(n>=55) return 'D (Kurang)'; return 'E (Sangat Kurang)'; }
            function update(){
                var s = avg('.sikap-input');
                var k = avg('.kompetensi-input');
                var a = Math.round(k*0.6 + s*0.4);
                document.getElementById('avg-sikap').textContent = s;
                document.getElementById('avg-kompetensi').textContent = k;
                document.getElementById('sum-sikap').textContent = s;
                document.getElementById('sum-kompetensi').textContent = k;
                document.getElementById('sum-akhir').textContent = a;
                document.getElementById('sum-predikat').textContent = predikat(a);
            }
            function bindPair(rangeSel, numSel){
                document.querySelectorAll(rangeSel).forEach(function(r){
                    r.addEventListener('input', function(){ var n=document.getElementById(r.dataset.target); if(n){ n.value=r.value; } update(); });
                });
                document.querySelectorAll(numSel).forEach(function(n){
                    n.addEventListener('input', function(){ n.value = clamp(n.value); var r=document.getElementById(n.dataset.source); if(r){ r.value=n.value; } update(); });
                });
            }
            bindPair('.sikap-input','.sikap-num');
            bindPair('.kompetensi-input','.kompetensi-num');
            update();
        })();
    </script>
</x-guru-layout>
