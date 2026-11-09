<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl border-2 border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Portal Pembimbing &amp; Pemantauan Orang Tua
                    </div>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-blue-950 sm:text-3xl">
                        Selamat Datang, {{ auth()->user()->name }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-600 max-w-2xl leading-relaxed">
                        Pantau kehadiran, aktivitas harian, dan perkembangan kompetensi siswa selama Praktik Kerja Lapangan (PKL) secara transparan dan mudah dipahami oleh guru serta orang tua/wali murid.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50/70 px-3.5 py-2 text-xs font-semibold text-blue-800">
                        <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <a href="#verifikasi" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Periksa Logbook</span>
                        @if ($stats['pending'] > 0)
                            <span class="rounded-full bg-white px-2 py-0.5 text-xs font-extrabold text-blue-700 shadow-sm">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-xl border-2 border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
                <span class="grid h-6 w-6 place-items-center rounded-full bg-emerald-600 text-xs font-bold text-white">✓</span>
                <div>
                    <strong class="font-bold">Berhasil Disimpan:</strong>
                    <span class="ml-1">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Section Item: Panduan Informasi untuk Orang Tua & Guru --}}
        <section class="rounded-2xl border-2 border-blue-200 bg-gradient-to-r from-blue-50/90 via-blue-50/50 to-white p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                </span>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-blue-900">
                            Panduan Pemantauan PKL (Untuk Guru &amp; Orang Tua / Wali Murid)
                        </h2>
                        <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-bold text-blue-700 border border-blue-200">
                            Transparan &amp; Terbuka
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                        Halaman ini dirancang agar pihak sekolah dan keluarga siswa dapat saling memantau proses belajar praktik kerja lapangan secara jelas dan teratur:
                    </p>

                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-blue-200 bg-white p-3.5 shadow-sm">
                            <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                1. Catatan Logbook Harian
                            </div>
                            <p class="mt-1.5 text-[12px] leading-snug text-slate-600">
                                Siswa wajib menuliskan kegiatan nyata yang dikerjakan di tempat kerja setiap hari beserta jam hadir.
                            </p>
                        </div>
                        <div class="rounded-xl border border-blue-200 bg-white p-3.5 shadow-sm">
                            <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                2. Verifikasi Guru Pembimbing
                            </div>
                            <p class="mt-1.5 text-[12px] leading-snug text-slate-600">
                                Guru memeriksa dan menyetujui logbook untuk memastikan tugas yang dilakukan siswa aman dan sesuai kurikulum.
                            </p>
                        </div>
                        <div class="rounded-xl border border-blue-200 bg-white p-3.5 shadow-sm">
                            <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                3. Evaluasi &amp; Nilai Resmi
                            </div>
                            <p class="mt-1.5 text-[12px] leading-snug text-slate-600">
                                Nilai keterampilan dan kedisiplinan siswa dihitung terbuka sebagai syarat kelulusan dan penerbitan sertifikat PKL.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Items: Kartu Ringkasan Statistik (Tone Biru Terang & Jelas) --}}
        <section class="grid gap-4 sm:grid-cols-3">
            {{-- Stat 1 --}}
            <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between">
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Anak Didik PKL
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 10v-2a4 4 0 0 0-3-3.87m-1-12.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-950">{{ $stats['placements'] }}</span>
                    <span class="text-sm font-semibold text-slate-500">Siswa</span>
                </div>
                <p class="mt-1 text-xs font-medium text-slate-600">Total siswa bimbingan aktif dalam program PKL</p>
                <div class="mt-3 border-t border-blue-50 pt-2 text-[11px] font-semibold text-blue-700 flex items-center gap-1">
                    <span>✓</span> Terdaftar resmi di sistem sekolah
                </div>
            </div>

            {{-- Stat 2 --}}
            <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between">
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        Perlu Diperiksa Guru
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-100 text-blue-700 border border-blue-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-600">{{ $stats['pending'] }}</span>
                    <span class="text-sm font-semibold text-slate-500">Catatan Harian</span>
                </div>
                <p class="mt-1 text-xs font-medium text-slate-600">Laporan siswa yang menunggu persetujuan guru</p>
                <div class="mt-3 border-t border-blue-50 pt-2 text-[11px] font-semibold text-slate-600 flex items-center gap-1">
                    <span>⏱</span> Diperiksa berkala demi kedisiplinan siswa
                </div>
            </div>

            {{-- Stat 3 --}}
            <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between">
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Aktivitas Sah (Disetujui)
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-700">{{ $stats['approved'] }}</span>
                    <span class="text-sm font-semibold text-slate-500">Laporan Disahkan</span>
                </div>
                <p class="mt-1 text-xs font-medium text-slate-600">Aktivitas belajar siswa yang telah terverifikasi valid</p>
                <div class="mt-3 border-t border-blue-50 pt-2 text-[11px] font-semibold text-emerald-700 flex items-center gap-1">
                    <span>✓</span> Aman, tercatat, dan diakui sekolah
                </div>
            </div>
        </section>

        {{-- Section 1: Daftar Siswa Bimbingan (Item dibuat serba biru & ramah orang tua) --}}
        <section class="rounded-2xl border-2 border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col justify-between gap-3 border-b-2 border-blue-100 bg-blue-50/70 px-6 py-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-blue-950">Daftar Siswa Bimbingan (Anak Didik)</h2>
                        <p class="text-xs text-slate-600">Informasi lengkap tempat kerja, pembimbing, dan perkembangan belajar setiap siswa.</p>
                    </div>
                </div>
                <span class="inline-flex rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 border border-blue-200 self-start sm:self-auto">
                    Total: {{ $placements->count() }} Siswa Bimbingan
                </span>
            </div>

            <div class="p-6 space-y-4">
                @forelse ($placements as $placement)
                    {{-- Section Item Siswa (Berwarna Biru Bersih & Jelas) --}}
                    <article class="rounded-xl border-2 border-blue-100 bg-blue-50/20 p-5 hover:border-blue-300 hover:bg-blue-50/40 transition-all shadow-xs">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            {{-- Info Utama Siswa --}}
                            <div class="flex items-start gap-4 min-w-0">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-blue-600 text-base font-extrabold text-white shadow-sm">
                                    {{ mb_strtoupper(mb_substr($placement->siswa?->siswaProfile?->nama_lengkap ?? $placement->siswa?->name ?? 'S', 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-extrabold text-base text-blue-950 truncate">
                                            {{ $placement->siswa?->siswaProfile?->nama_lengkap ?? $placement->siswa?->name }}
                                        </h3>
                                        <span class="inline-flex items-center rounded-md bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800 border border-blue-200">
                                            {{ $placement->siswa?->siswaProfile?->kelas ?? 'Kelas Siswa' }} · {{ $placement->siswa?->siswaProfile?->jurusan ?? 'Jurusan' }}
                                        </span>
                                        @if ($placement->status === 'aktif')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700 border border-blue-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-ping"></span>
                                                Sedang Praktik (Aktif)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                                                ✓ Telah Selesai PKL
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Keterangan Tempat & Periode Praktik (Mudah dipahami orang tua) --}}
                                    <div class="mt-2.5 grid gap-2 sm:grid-cols-2 text-xs text-slate-700">
                                        <div class="flex items-center gap-1.5 rounded-lg border border-blue-100 bg-white px-3 py-1.5">
                                            <span class="font-bold text-blue-900">Perusahaan / Mitra:</span>
                                            <span class="font-semibold text-slate-900 truncate">
                                                {{ $placement->nama_perusahaan ?: ($placement->industri?->industriProfile?->nama_perusahaan ?? 'Belum Ditentukan') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5 rounded-lg border border-blue-100 bg-white px-3 py-1.5">
                                            <span class="font-bold text-blue-900">Periode PKL:</span>
                                            <span class="font-semibold text-slate-900">
                                                {{ $placement->tanggal_mulai ? $placement->tanggal_mulai->translatedFormat('d M Y') : '-' }} s/d {{ $placement->tanggal_selesai ? $placement->tanggal_selesai->translatedFormat('d M Y') : '-' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Status Logbook & Pembimbing Mitra --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-100/70 px-2.5 py-1 font-bold text-blue-800 border border-blue-200">
                                            <svg class="h-3.5 w-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.5L19 7.5V19a2 2 0 0 1-2 2z" />
                                            </svg>
                                            {{ $placement->logbooks_count }} Laporan Harian Terkirim
                                        </span>
                                        @if ($placement->nama_pembimbing)
                                            <span class="text-slate-500">
                                                Pembimbing Lapangan: <strong class="text-slate-800">{{ $placement->nama_pembimbing }}</strong>
                                            </span>
                                        @endif
                                        @if ($placement->penilaian)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 font-bold text-emerald-800 border border-emerald-200">
                                                Nilai Akhir: {{ $placement->penilaian->nilai_akhir }} (Predikat {{ $placement->penilaian->predikat }})
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Aksi Pembimbing --}}
                            <div class="flex items-center gap-2.5 lg:flex-col lg:items-end justify-end pt-3 lg:pt-0 border-t border-blue-100 lg:border-t-0">
                                <a href="{{ route('guru.penilaian.create', $placement) }}"
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>{{ $placement->penilaian ? 'Perbarui Nilai PKL' : 'Beri Nilai & Evaluasi' }}</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-12 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-100 text-blue-600 border border-blue-200">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-blue-950">Belum Ada Siswa Bimbingan yang Ditugaskan</h3>
                        <p class="mt-1.5 text-xs text-slate-600 max-w-md mx-auto">
                            Daftar anak didik Anda akan otomatis tampil di sini begitu bagian Hubin (Hubungan Industri) menetapkan penempatan PKL siswa.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Section 2: Logbook Menunggu Verifikasi Guru (Section Item Dibuat Biru Bersih) --}}
        <section id="verifikasi" class="scroll-mt-6 rounded-2xl border-2 border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col justify-between gap-3 border-b-2 border-blue-100 bg-blue-50/70 px-6 py-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-blue-950">Logbook Menunggu Verifikasi Guru</h2>
                        <p class="text-xs text-slate-600">Periksa kegiatan nyata yang dikerjakan siswa untuk menjamin pembelajaran berjalan aman dan benar.</p>
                    </div>
                </div>
                @if ($stats['pending'] > 0)
                    <span class="inline-flex rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 border border-blue-200 self-start sm:self-auto">
                        {{ $stats['pending'] }} Laporan Perlu Diperiksa
                    </span>
                @endif
            </div>

            <div class="p-6 space-y-5">
                @forelse ($logbooks as $logbook)
                    {{-- Section Item Logbook (Berwarna Biru Jelas dengan Kotak Rincian) --}}
                    <article class="rounded-xl border-2 border-blue-200 bg-blue-50/30 p-5 hover:border-blue-300 transition-all shadow-xs">
                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
                            {{-- Sisi Kiri: Rincian Kegiatan Siswa --}}
                            <div class="space-y-3 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-blue-100">
                                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-blue-600 text-xs font-bold text-white shadow-sm">
                                        {{ mb_strtoupper(mb_substr($logbook->pklSiswa?->siswa?->siswaProfile?->nama_lengkap ?? $logbook->pklSiswa?->siswa?->name ?? 'S', 0, 1)) }}
                                    </span>
                                    <div>
                                        <h3 class="font-extrabold text-sm text-blue-950">
                                            {{ $logbook->pklSiswa?->siswa?->siswaProfile?->nama_lengkap ?? $logbook->pklSiswa?->siswa?->name }}
                                        </h3>
                                        <p class="text-[11px] text-slate-500">
                                            {{ $logbook->pklSiswa?->siswa?->siswaProfile?->kelas ?? 'Kelas Siswa' }} · {{ $logbook->pklSiswa?->nama_perusahaan ?: ($logbook->pklSiswa?->industri?->industriProfile?->nama_perusahaan ?? 'Tempat PKL') }}
                                        </p>
                                    </div>
                                    <div class="ml-auto flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800 border border-blue-200">
                                            📅 {{ $logbook->tanggal->translatedFormat('l, d F Y') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 border border-emerald-200 capitalize">
                                            ✓ Kehadiran: {{ $logbook->status_kehadiran }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Jam Hadir --}}
                                @if ($logbook->jam_masuk || $logbook->jam_keluar)
                                    <div class="inline-flex items-center gap-2 rounded-md bg-white px-3 py-1 text-xs font-semibold text-slate-700 border border-blue-100">
                                        <span class="text-blue-700 font-bold">Waktu Jam Kerja:</span>
                                        <span>{{ $logbook->jam_masuk ? $logbook->jam_masuk : '-' }} s/d {{ $logbook->jam_keluar ? $logbook->jam_keluar : '-' }} WIB</span>
                                    </div>
                                @endif

                                {{-- Isi Catatan Pekerjaan Siswa --}}
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-blue-900 block mb-1">
                                        Aktivitas Kerja yang Dilakukan Siswa:
                                    </span>
                                    <div class="rounded-xl border border-blue-200 bg-white p-4 text-sm leading-relaxed text-slate-800 shadow-xs whitespace-pre-line">
                                        {{ $logbook->kegiatan }}
                                    </div>
                                </div>

                                {{-- Catatan Siswa Jika Ada --}}
                                @if ($logbook->keterangan)
                                    <div class="rounded-lg border border-blue-200/80 bg-blue-50/70 p-3 text-xs text-blue-900">
                                        <strong class="font-bold text-blue-800">Catatan/Kendala Tambahan dari Siswa:</strong>
                                        <p class="mt-0.5 text-slate-700 italic">{{ $logbook->keterangan }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Sisi Kanan: Formulir Keputusan Guru (Section Item Biru Jelas) --}}
                            <form method="POST" action="{{ route('guru.logbook.verifikasi', $logbook) }}"
                                class="rounded-xl border-2 border-blue-200 bg-white p-4.5 space-y-3 shadow-sm flex flex-col justify-between">
                                @csrf
                                @method('PUT')

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between border-b border-blue-100 pb-2">
                                        <span class="text-xs font-extrabold uppercase tracking-wider text-blue-900">
                                            Tindakan Pemeriksaan Guru
                                        </span>
                                        <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                            Status Resmi
                                        </span>
                                    </div>

                                    <div>
                                        <label for="status-{{ $logbook->id }}" class="block text-xs font-bold text-slate-800 mb-1">
                                            Pilih Keputusan:
                                        </label>
                                        <select id="status-{{ $logbook->id }}" name="verifikasi_status" required
                                            class="block w-full rounded-lg border-2 border-blue-200 bg-blue-50/40 px-3 py-2 text-xs font-bold text-blue-950 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                                            <option value="disetujui">✓ Setujui (Kegiatan Valid &amp; Benar)</option>
                                            <option value="ditolak">✕ Minta Perbaikan (Kegiatan Belum Jelas)</option>
                                        </select>
                                        <p class="mt-1 text-[11px] text-slate-500">
                                            Jika disetujui, orang tua dan sekolah melihat laporan ini sah.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="catatan-{{ $logbook->id }}" class="block text-xs font-bold text-slate-800 mb-1">
                                            Pesan Pembimbing / Arahan (Opsional):
                                        </label>
                                        <textarea id="catatan-{{ $logbook->id }}" name="catatan_guru" rows="3" maxlength="2000"
                                            placeholder="Tulis masukan untuk siswa &amp; orang tua (contoh: 'Bagus, pertahankan kedisiplinan kerja!')"
                                            class="block w-full rounded-lg border border-blue-200 bg-white p-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"></textarea>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan &amp; Verifikasi Laporan</span>
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-12 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-100 text-emerald-700 border border-emerald-200">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-blue-950">Semua Laporan Harian Telah Selesai Diperiksa</h3>
                        <p class="mt-1 text-xs text-slate-600 max-w-md mx-auto">
                            Tidak ada logbook siswa yang tertunda. Seluruh aktivitas kerja anak didik telah terverifikasi aman dan tertib.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($logbooks->hasPages())
                <div class="border-t-2 border-blue-100 bg-blue-50/40 px-6 py-4">
                    {{ $logbooks->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
