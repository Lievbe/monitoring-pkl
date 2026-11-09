<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl border-2 border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Portal Mitra Industri{{ $companyName ? ' · '.$companyName : '' }}
                    </div>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-blue-950 sm:text-3xl">
                        Pemantauan Siswa PKL di Perusahaan
                    </h1>
                    <p class="mt-1 text-sm text-slate-600 max-w-2xl">
                        Pantau kehadiran, kedisiplinan, dan aktivitas kerja siswa yang melaksanakan Praktik Kerja Lapangan di perusahaan Anda secara transparan bersama sekolah dan orang tua.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2.5 rounded-xl border border-blue-200 bg-blue-50/70 px-4 py-2.5 text-xs font-semibold text-blue-800 self-start sm:self-auto">
                    <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4.5 9h15M6 5h12a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 20H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5Z" />
                    </svg>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        {{-- Section Items: Stat Cards Biru --}}
        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-100">Siswa Ditugaskan</span>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 10v-2a4 4 0 0 0-3-3.87m-1-12.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-950">{{ $stats['students'] }}</span>
                    <span class="text-sm font-semibold text-slate-500">Siswa</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Total penempatan siswa di perusahaan Anda</p>
            </div>

            <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-100">Sedang Praktik Aktif</span>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-700">{{ $stats['active'] }}</span>
                    <span class="text-sm font-semibold text-slate-500">Aktif</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Siswa yang saat ini aktif menjalankan kegiatan kerja</p>
            </div>

            <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-800 border border-blue-100">Menunggu Tinjauan Guru</span>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-100 text-blue-700 border border-blue-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-600">{{ $stats['pending_logbooks'] }}</span>
                    <span class="text-sm font-semibold text-slate-500">Laporan</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Catatan kegiatan yang sedang ditinjau guru pembimbing</p>
            </div>
        </section>

        {{-- Section 1: Daftar Siswa di Tempat PKL --}}
        <section class="rounded-2xl border-2 border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col justify-between gap-2 border-b-2 border-blue-100 bg-blue-50/70 px-6 py-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-blue-600 text-white text-xs font-bold">🏢</span>
                    <div>
                        <h2 class="text-base font-extrabold text-blue-950">Daftar Siswa di Tempat Kerja</h2>
                        <p class="text-xs text-slate-600">Siswa yang ditempatkan pada perusahaan Anda untuk bimbingan praktik.</p>
                    </div>
                </div>
                <span class="inline-flex rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 border border-blue-200 self-start sm:self-auto">
                    {{ $placements->count() }} Siswa
                </span>
            </div>

            <div class="p-6 space-y-4">
                @forelse ($placements as $placement)
                    {{-- Section Item Siswa Industri (Biru Bersih) --}}
                    <article class="rounded-xl border-2 border-blue-100 bg-blue-50/20 p-5 hover:border-blue-300 hover:bg-blue-50/40 transition-all shadow-xs">
                        <div class="grid gap-3 sm:grid-cols-[1.5fr_1.5fr_auto] sm:items-center">
                            <div class="flex items-start gap-3.5">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-blue-600 text-sm font-extrabold text-white shadow-sm">
                                    {{ mb_strtoupper(mb_substr($placement->siswa?->siswaProfile?->nama_lengkap ?? $placement->siswa?->name ?? 'S', 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="font-extrabold text-sm text-blue-950 truncate">
                                        {{ $placement->siswa?->siswaProfile?->nama_lengkap ?? $placement->siswa?->name }}
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        {{ $placement->siswa?->siswaProfile?->jurusan ?? 'Jurusan Siswa' }} · {{ $placement->siswa?->siswaProfile?->kelas ?? 'Kelas Siswa' }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-xs text-slate-700 pl-14 sm:pl-0 space-y-1">
                                <p>
                                    <span class="text-slate-500">Guru Pembimbing Sekolah:</span>
                                    <strong class="text-blue-950">{{ $placement->guru_nama ?: ($placement->guru?->guruProfile?->nama_lengkap ?? $placement->guru?->name ?? '—') }}</strong>
                                </p>
                                <p class="text-slate-500">
                                    Periode: <span class="font-semibold text-slate-800">{{ $placement->tanggal_mulai ? $placement->tanggal_mulai->translatedFormat('d M Y') : '-' }} s/d {{ $placement->tanggal_selesai ? $placement->tanggal_selesai->translatedFormat('d M Y') : '-' }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center">
                                @if ($placement->status === 'aktif')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700 border border-blue-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                                        ✓ Selesai
                                    </span>
                                @endif
                                <span class="rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800 border border-blue-200">
                                    {{ $placement->logbooks_count }} Laporan
                                </span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-12 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-100 text-blue-600 border border-blue-200">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                            </svg>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-blue-950">Belum Ada Siswa yang Ditempatkan</h3>
                        <p class="mt-1 text-xs text-slate-600">Siswa yang ditugaskan di perusahaan Anda akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Section 2: Aktivitas Logbook Siswa --}}
        <section class="rounded-2xl border-2 border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b-2 border-blue-100 bg-blue-50/70 px-6 py-4">
                <h2 class="text-base font-extrabold text-blue-950">Aktivitas Harian Logbook Siswa</h2>
                <p class="text-xs text-slate-600">Pantau catatan kerja harian siswa. Verifikasi resmi tetap disahkan oleh guru pembimbing sekolah.</p>
            </div>

            <div class="p-6 space-y-4">
                @forelse ($logbooks as $logbook)
                    {{-- Section Item Logbook Industri (Biru Bersih) --}}
                    <article class="rounded-xl border-2 border-blue-100 bg-blue-50/20 p-5 hover:border-blue-300 transition-all shadow-xs">
                        <div class="grid gap-3 sm:grid-cols-[200px_1fr_auto] sm:items-start">
                            <div>
                                <p class="text-sm font-extrabold text-blue-950">
                                    {{ $logbook->pklSiswa?->siswa?->siswaProfile?->nama_lengkap ?? $logbook->pklSiswa?->siswa?->name }}
                                </p>
                                <p class="mt-0.5 text-xs text-slate-500">📅 {{ $logbook->tanggal->translatedFormat('d M Y') }}</p>
                                <span class="mt-1.5 inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 text-[11px] font-bold text-blue-800 border border-blue-200 capitalize">
                                    Kehadiran: {{ $logbook->status_kehadiran }}
                                </span>
                            </div>

                            <div>
                                <p class="whitespace-pre-line text-sm leading-relaxed text-slate-800 bg-white p-3 rounded-lg border border-blue-100 shadow-xs">{{ $logbook->kegiatan }}</p>
                                @if ($logbook->keterangan)
                                    <p class="mt-1.5 text-xs text-slate-600 italic bg-blue-50/60 p-1.5 rounded border border-blue-100">Catatan: {{ $logbook->keterangan }}</p>
                                @endif
                            </div>

                            <div class="self-start sm:self-auto">
                                @if ($logbook->verifikasi_status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                                        ✓ Disetujui Guru
                                    </span>
                                @elseif ($logbook->verifikasi_status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-800 border border-rose-200">
                                        ✕ Minta Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-200">
                                        ⏱ Menunggu Guru
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-12 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-100 text-blue-600 border border-blue-200">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.5L19 7.5V19a2 2 0 0 1-2 2z" />
                            </svg>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-blue-950">Belum Ada Catatan Logbook</h3>
                        <p class="mt-1 text-xs text-slate-600">Logbook kegiatan siswa yang ditempatkan akan muncul di sini secara berkala.</p>
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
