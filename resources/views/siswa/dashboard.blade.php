<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl border-2 border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Portal Siswa PKL &amp; Pemantauan Orang Tua
                    </div>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-blue-950 sm:text-3xl">
                        Halo, {{ auth()->user()->name }}!
                    </h1>
                    <p class="mt-1 text-sm text-slate-600 max-w-2xl">
                        Catat kegiatan harianmu dengan disiplin. Catatan ini dapat dilihat oleh guru pembimbing dan orang tua/wali murid sebagai bukti pelaksanaan Praktik Kerja Lapangan.
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
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-xl border-2 border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
                <span class="grid h-6 w-6 place-items-center rounded-full bg-emerald-600 text-xs font-bold text-white">✓</span>
                <div>
                    <strong class="font-bold">Berhasil:</strong>
                    <span class="ml-1">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-start gap-3 rounded-xl border-2 border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
                <span class="grid h-6 w-6 place-items-center rounded-full bg-rose-600 text-xs font-bold text-white">!</span>
                <div>
                    <strong class="font-bold">Perhatian:</strong>
                    <span class="ml-1">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($placement)
            {{-- Section Items: Stat Cards Biru Bersih --}}
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-100">Status Magang</span>
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white shadow-sm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-2xl font-extrabold capitalize text-blue-950">{{ $placement->status }}</p>
                    <p class="mt-1 text-xs text-slate-500">Kondisi penempatan siswa saat ini</p>
                </div>

                <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-100">Logbook Dikirim</span>
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white shadow-sm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10M7 8h10M7 12h6m-9 8h16a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1Z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-2xl font-extrabold text-blue-950">{{ $logbooks->total() }} Laporan</p>
                    <p class="mt-1 text-xs text-slate-500">Catatan aktivitas harian siswa</p>
                </div>

                <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-100">Tempat PKL</span>
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white shadow-sm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 truncate text-base font-extrabold text-blue-950" title="{{ $placement->nama_perusahaan ?: ($placement->industri?->industriProfile?->nama_perusahaan ?? 'Belum diatur') }}">
                        {{ $placement->nama_perusahaan ?: ($placement->industri?->industriProfile?->nama_perusahaan ?? 'Belum diatur') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Perusahaan mitra industri tempat siswa bekerja</p>
                </div>

                <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm hover:border-blue-300 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-100">Guru Pembimbing</span>
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white shadow-sm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 truncate text-base font-extrabold text-blue-950">
                        {{ $placement->guru_nama ?: ($placement->guru?->guruProfile?->nama_lengkap ?? $placement->guru?->name ?? 'Belum diatur') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Guru pendamping dari pihak sekolah</p>
                </div>
            </section>

            {{-- Sertifikat Banner --}}
            @if ($placement->sertifikat)
                <div class="flex flex-col justify-between gap-4 rounded-2xl border-2 border-blue-200 bg-blue-50/70 p-5 sm:flex-row sm:items-center shadow-sm">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-blue-800">
                            <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Sertifikat Resmi PKL Telah Terbit
                        </div>
                        <h2 class="mt-1 text-base font-extrabold text-blue-950">Nomor Dokumen: {{ $placement->sertifikat->nomor_sertifikat }}</h2>
                        <p class="text-xs text-slate-600">Sertifikat resmi kelulusan PKL dapat dicetak atau disimpan oleh siswa dan orang tua.</p>
                    </div>
                    <a href="{{ route('sertifikat.show', $placement->sertifikat) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                        Lihat Sertifikat
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            @endif

            {{-- Main Grid: Form Catat Logbook + Aside --}}
            <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(290px,0.9fr)]">
                {{-- Form Catat Logbook (Section Item Biru Bersih) --}}
                @if ($placement->status === 'aktif')
                    <section class="rounded-2xl border-2 border-blue-100 bg-white shadow-sm overflow-hidden">
                        <div class="border-b-2 border-blue-100 bg-blue-50/70 px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="grid h-7 w-7 place-items-center rounded-lg bg-blue-600 text-white text-xs font-bold">✍</span>
                                <h2 class="text-base font-extrabold text-blue-950">Catat Kegiatan PKL Hari Ini</h2>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">Tuliskan pekerjaan yang kamu lakukan agar orang tua dan guru pembimbing dapat memantau dengan jelas.</p>
                        </div>

                        <form method="POST" action="{{ route('siswa.logbook.store') }}" class="grid gap-4 p-6 sm:grid-cols-2">
                            @csrf
                            <div>
                                <x-input-label for="tanggal" value="Tanggal Kegiatan" class="text-slate-800 font-bold text-xs" />
                                <x-text-input id="tanggal" name="tanggal" type="date"
                                    class="border-blue-200 focus:border-blue-500"
                                    :value="old('tanggal', now()->toDateString())"
                                    :max="now()->toDateString()" required />
                                <x-input-error :messages="$errors->get('tanggal')" class="mt-1.5" />
                            </div>

                            <div>
                                <x-input-label for="status_kehadiran" value="Status Kehadiran Hari Ini" class="text-slate-800 font-bold text-xs" />
                                <select id="status_kehadiran" name="status_kehadiran" required
                                    class="block w-full rounded-xl border-2 border-blue-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                                    @foreach (['hadir' => 'Hadir di Tempat PKL', 'sakit' => 'Sakit (Dengan Keterangan)', 'izin' => 'Izin (Dengan Alasan)', 'alfa' => 'Tidak Hadir Tanpa Keterangan'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('status_kehadiran', 'hadir') === $val)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status_kehadiran')" class="mt-1.5" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="kegiatan" value="Uraian Kegiatan / Pekerjaan yang Dilakukan" class="text-slate-800 font-bold text-xs" />
                                <textarea id="kegiatan" name="kegiatan" rows="4" required maxlength="5000"
                                    placeholder="Jelaskan secara rinci tugas atau pekerjaan yang kamu lakukan hari ini di tempat PKL..."
                                    class="block w-full rounded-xl border-2 border-blue-200 bg-white p-3.5 text-sm text-slate-900 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none leading-relaxed">{{ old('kegiatan') }}</textarea>
                                <x-input-error :messages="$errors->get('kegiatan')" class="mt-1.5" />
                            </div>

                            <div>
                                <x-input-label for="jam_masuk" value="Jam Masuk Kerja (Opsional)" class="text-slate-800 font-bold text-xs" />
                                <x-text-input id="jam_masuk" name="jam_masuk" type="time" class="border-blue-200" :value="old('jam_masuk')" />
                                <x-input-error :messages="$errors->get('jam_masuk')" class="mt-1.5" />
                            </div>

                            <div>
                                <x-input-label for="jam_keluar" value="Jam Pulang Kerja (Opsional)" class="text-slate-800 font-bold text-xs" />
                                <x-text-input id="jam_keluar" name="jam_keluar" type="time" class="border-blue-200" :value="old('jam_keluar')" />
                                <x-input-error :messages="$errors->get('jam_keluar')" class="mt-1.5" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="keterangan" value="Catatan / Alat / Pembelajaran Tambahan (Opsional)" class="text-slate-800 font-bold text-xs" />
                                <textarea id="keterangan" name="keterangan" rows="2" maxlength="2000"
                                    placeholder="Kendala yang dialami, bimbingan yang didapat dari rekan kerja, atau catatan lain..."
                                    class="block w-full rounded-xl border border-blue-200 bg-white p-3 text-sm text-slate-900 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">{{ old('keterangan') }}</textarea>
                                <x-input-error :messages="$errors->get('keterangan')" class="mt-1.5" />
                            </div>

                            <div class="sm:col-span-2 pt-2">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kirim Laporan Logbook Hari Ini
                                </button>
                            </div>
                        </form>
                    </section>
                @endif

                {{-- Aside Info --}}
                <aside class="space-y-4">
                    {{-- Periode PKL (Section Item Biru) --}}
                    <div class="rounded-2xl border-2 border-blue-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-extrabold text-blue-950">Jadwal Periode PKL</h3>
                                <p class="text-xs text-slate-500">Rentang waktu magang siswa</p>
                            </div>
                        </div>
                        <div class="mt-4 border-t border-blue-100 pt-3 text-xs space-y-1.5">
                            <div class="flex justify-between items-center text-slate-700">
                                <span>Mulai:</span>
                                <strong class="text-blue-950 font-bold">{{ $placement->tanggal_mulai ? $placement->tanggal_mulai->translatedFormat('d F Y') : '-' }}</strong>
                            </div>
                            <div class="flex justify-between items-center text-slate-700">
                                <span>Selesai:</span>
                                <strong class="text-blue-950 font-bold">{{ $placement->tanggal_selesai ? $placement->tanggal_selesai->translatedFormat('d F Y') : '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Panduan Orang Tua & Siswa (Section Item Biru Jelas) --}}
                    <div class="rounded-2xl border-2 border-blue-200 bg-blue-50/80 p-5 text-blue-950 shadow-xs">
                        <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-blue-800">
                            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            Catatan untuk Orang Tua / Wali
                        </div>
                        <p class="mt-2 text-xs leading-relaxed text-slate-700">
                            Bapak/Ibu orang tua dapat memeriksa riwayat kehadiran dan catatan tugas anak di bawah ini secara berkala. Pastikan ananda mengisi logbook setiap hari selesai jam kerja PKL.
                        </p>
                    </div>

                    {{-- Hasil Penilaian (Jika Ada) --}}
                    @if ($placement->penilaian)
                        <div class="rounded-2xl border-2 border-blue-200 bg-white p-5 shadow-sm">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-800 block">Hasil Penilaian PKL Resmi</span>
                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold text-blue-700">{{ $placement->penilaian->nilai_akhir }}</span>
                                <span class="text-sm font-semibold text-slate-500">/ 100</span>
                                <span class="ml-auto rounded-lg bg-blue-100 px-3 py-1 text-xs font-extrabold text-blue-800 border border-blue-200">
                                    Predikat: {{ $placement->penilaian->predikat }}
                                </span>
                            </div>
                            @if ($placement->penilaian->catatan)
                                <div class="mt-3 border-t border-blue-100 pt-3">
                                    <p class="text-xs font-bold text-slate-700">Catatan dari Guru Pembimbing:</p>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-600 bg-blue-50/60 p-2.5 rounded-lg border border-blue-100">{{ $placement->penilaian->catatan }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </aside>
            </div>

            {{-- Riwayat Logbook (Section Item Dibuat Serba Biru) --}}
            <section class="rounded-2xl border-2 border-blue-100 bg-white shadow-sm overflow-hidden">
                <div class="flex flex-col justify-between gap-2 border-b-2 border-blue-100 bg-blue-50/70 px-6 py-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-base font-extrabold text-blue-950">Riwayat Catatan Harian (Logbook)</h2>
                        <p class="text-xs text-slate-600">Daftar lengkap aktivitas kerja yang telah dikirimkan oleh siswa.</p>
                    </div>
                    <span class="inline-flex items-center rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800 border border-blue-200 self-start sm:self-auto">
                        Total: {{ $logbooks->total() }} Catatan Harian
                    </span>
                </div>

                <div class="p-6 space-y-4">
                    @forelse ($logbooks as $logbook)
                        {{-- Section Item Logbook (Biru Rapi & Transparan) --}}
                        <article class="rounded-xl border-2 border-blue-100 bg-blue-50/20 p-5 hover:border-blue-300 hover:bg-blue-50/40 transition-all shadow-xs">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between pb-3 border-b border-blue-100">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800 border border-blue-200">
                                        📅 {{ $logbook->tanggal->translatedFormat('l, d F Y') }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 border border-blue-200 capitalize">
                                        Kehadiran: <strong class="ml-1 text-blue-900">{{ $logbook->status_kehadiran }}</strong>
                                    </span>
                                    @if ($logbook->jam_masuk || $logbook->jam_keluar)
                                        <span class="text-xs text-slate-500 font-medium">
                                            ({{ $logbook->jam_masuk ?? '-' }} - {{ $logbook->jam_keluar ?? '-' }} WIB)
                                        </span>
                                    @endif
                                </div>

                                {{-- Status Verifikasi Badge (Jelas bagi orang tua) --}}
                                <div>
                                    @if ($logbook->verifikasi_status === 'disetujui')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                                            ✓ Telah Disetujui Guru (Sah)
                                        </span>
                                    @elseif ($logbook->verifikasi_status === 'ditolak')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-800 border border-rose-200">
                                            ✕ Perlu Perbaikan Catatan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-200">
                                            ⏱ Menunggu Pemeriksaan Guru
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block mb-1">Kegiatan yang Dikerjakan:</span>
                                <p class="whitespace-pre-line text-sm leading-relaxed text-slate-800 bg-white p-3.5 rounded-xl border border-blue-100 shadow-xs">{{ $logbook->kegiatan }}</p>
                            </div>

                            @if ($logbook->keterangan)
                                <p class="mt-2 text-xs text-slate-600 italic bg-blue-50/50 p-2 rounded-lg border border-blue-100">
                                    Catatan Tambahan Siswa: {{ $logbook->keterangan }}
                                </p>
                            @endif

                            @if ($logbook->catatan_guru)
                                <div class="mt-3 rounded-xl border-2 border-blue-200 bg-blue-50/80 p-3.5 text-xs text-blue-950">
                                    <strong class="font-bold text-blue-900 block mb-0.5">💬 Masukan / Catatan dari Guru Pembimbing:</strong>
                                    <p class="text-slate-800 leading-relaxed">{{ $logbook->catatan_guru }}</p>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-12 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-100 text-blue-600 border border-blue-200">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.5L19 7.5V19a2 2 0 0 1-2 2z" />
                                </svg>
                            </span>
                            <h3 class="mt-4 text-base font-bold text-blue-950">Belum Ada Catatan Logbook</h3>
                            <p class="mt-1 text-xs text-slate-600 max-w-sm mx-auto">
                                Catat kegiatan harianmu menggunakan formulir di atas setelah selesai bertugas di tempat PKL.
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
        @else
            {{-- State: Penempatan belum diatur --}}
            <div class="rounded-2xl border-2 border-blue-200 bg-white p-10 text-center shadow-sm">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-blue-50 text-blue-600 border-2 border-blue-200">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </span>
                <h2 class="mt-4 text-xl font-extrabold text-blue-950">Penempatan PKL Sedang Disiapkan</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-600 leading-relaxed">
                    Akun siswa Anda sudah aktif di sistem. Admin Hubin sekolah sedang mengatur penempatan mitra industri dan guru pembimbing. Silakan hubungi bagian Hubin sekolah jika penempatan belum muncul.
                </p>
            </div>
        @endif
    </div>
</x-app-layout>
