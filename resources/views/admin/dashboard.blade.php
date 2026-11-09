<x-app-layout>
    <x-slot name="header">
        <div class="rounded-xl border border-blue-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Admin Hubin (Hubungan Industri)
                    </div>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        Monitoring Hub PKL
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Kelola penempatan siswa, pantau status PKL, dan terbitkan sertifikat resmi.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2.5 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 self-start sm:self-auto">
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
            <div role="status" class="flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <span class="font-bold">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-semibold">Periksa kembali data yang diisi:</p>
                <ul class="mt-1.5 list-inside list-disc space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Stat Cards --}}
        <section aria-label="Ringkasan PKL" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa Terdaftar</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 10v-2a4 4 0 0 0-3-3.87m-1-12.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['students'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Akun siswa PKL aktif</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">PKL Berjalan</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $stats['active_placements'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Penempatan aktif di industri</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">PKL Selesai</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $stats['completed_placements'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Penempatan tuntas</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sertifikat Terbit</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['certificates'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Sertifikat resmi terbit</p>
            </div>
        </section>

        {{-- Main Grid: Table + Aside --}}
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
            {{-- Tabel Penempatan PKL --}}
            <section id="penempatan" class="min-w-0 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex flex-col justify-between gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Status Penempatan PKL</h2>
                        <p class="text-xs text-slate-500">Ringkasan siswa dan progres penempatan PKL terkini</p>
                    </div>
                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-100 self-start sm:self-auto">
                        {{ $placements->total() }} Penempatan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[780px] text-left text-sm">
                        <thead class="bg-blue-50/70 border-b border-blue-100 text-xs font-semibold uppercase tracking-wider text-blue-900">
                            <tr>
                                <th scope="col" class="px-5 py-3">Nama Siswa</th>
                                <th scope="col" class="px-5 py-3">Perusahaan</th>
                                <th scope="col" class="px-5 py-3">Pembimbing</th>
                                <th scope="col" class="px-5 py-3">Status</th>
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($placements as $placement)
                                @php
                                    $guruNama = $placement->guru_nama ?: ($placement->guru?->guruProfile?->nama_lengkap ?? $placement->guru?->name);
                                    $perusahaan = $placement->nama_perusahaan ?: $placement->industri?->industriProfile?->nama_perusahaan;
                                    $pembimbing = $placement->nama_pembimbing ?: $placement->industri?->industriProfile?->nama_pembimbing;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-50 text-xs font-bold text-blue-700 border border-blue-100">
                                                {{ mb_strtoupper(mb_substr($placement->siswa?->name ?? 'S', 0, 1)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm text-slate-900 truncate">
                                                    {{ $placement->siswa?->siswaProfile?->nama_lengkap ?? $placement->siswa?->name ?? 'Siswa' }}
                                                </p>
                                                <p class="text-xs text-slate-500 truncate">
                                                    {{ $placement->siswa?->siswaProfile?->jurusan ?? '' }} · {{ $placement->siswa?->siswaProfile?->kelas ?? 'Kelas belum diisi' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">{{ $perusahaan ?: '—' }}</p>
                                        <p class="text-xs text-slate-500">{{ $placement->tanggal_mulai->translatedFormat('d M Y') }} – {{ $placement->tanggal_selesai->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">{{ $guruNama ?: '—' }}</p>
                                        <p class="text-xs text-slate-500">Mitra: {{ $pembimbing ?: '—' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($placement->status === 'aktif')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 border border-blue-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span> Berjalan
                                            </span>
                                        @elseif ($placement->status === 'selesai')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span> Selesai
                                            </span>
                                        @elseif ($placement->status === 'dibatalkan')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 border border-rose-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span> Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 border border-amber-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.placements.edit', $placement) }}"
                                                class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                                                Edit
                                            </a>
                                            @if ($placement->status === 'selesai' && $placement->sertifikat)
                                                <a href="{{ route('sertifikat.show', $placement->sertifikat) }}"
                                                    class="rounded-md bg-blue-600 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                    Sertifikat
                                                </a>
                                            @elseif ($placement->status === 'selesai')
                                                <form method="POST" action="{{ route('admin.placements.sertifikat.generate', $placement) }}">
                                                    @csrf
                                                    <button type="submit" class="rounded-md bg-blue-600 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                        Terbitkan
                                                    </button>
                                                </form>
                                            @elseif ($placement->status === 'aktif')
                                                <form method="POST" action="{{ route('admin.placements.complete', $placement) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                                        Selesaikan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-blue-50 text-blue-600">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.5L19 7.5V19a2 2 0 0 1-2 2z" />
                                            </svg>
                                        </span>
                                        <p class="mt-3 font-semibold text-slate-800">Belum ada data penempatan PKL</p>
                                        <p class="mt-1 text-xs text-slate-500">Mulai buat penempatan siswa ke perusahaan mitra.</p>
                                        <a href="{{ route('admin.placements.create') }}" class="mt-3 inline-flex rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                            Buat Penempatan Baru
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($placements->hasPages())
                    <div class="border-t border-slate-100 px-5 py-3">
                        {{ $placements->links() }}
                    </div>
                @endif
            </section>

            {{-- Aside Aksi & Petunjuk --}}
            <aside class="space-y-4">
                {{-- Aksi Cepat --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Aksi Cepat</h3>
                            <p class="text-xs text-slate-500">Pengelolaan data PKL</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-2.5">
                        <a href="{{ route('admin.students.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Daftarkan Siswa Baru
                        </a>
                        <a href="{{ route('admin.placements.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                            <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Buat Penempatan PKL
                        </a>
                    </div>
                </div>

                {{-- Petunjuk Alur --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-5 text-slate-800">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-700">
                        <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14" />
                        </svg>
                        Alur Penerbitan Sertifikat
                    </div>
                    <ol class="mt-3 list-decimal list-inside space-y-1.5 text-xs leading-5 text-slate-700">
                        <li>Pastikan periode PKL siswa telah selesai.</li>
                        <li>Klik tombol <strong>Selesaikan</strong> pada baris siswa.</li>
                        <li>Klik tombol <strong>Terbitkan</strong> untuk mengenerate sertifikat PDF.</li>
                    </ol>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
