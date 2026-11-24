<x-siswa-layout>
    @php
        $studentName = auth()->user()->siswaProfile?->nama_lengkap ?: auth()->user()->name;
        $todayStatus = match (true) {
            ! $placement => ['Belum ada penempatan', 'Hubungi admin sekolah untuk mengaktifkan penempatan PKL.', 'slate'],
            ! $todayLogbook => ['Logbook hari ini belum diisi', 'Catat kegiatan dan kehadiranmu sebelum hari berakhir.', 'amber'],
            $todayLogbook->verifikasi_status === 'ditolak' => ['Logbook perlu diperbaiki', $todayLogbook->catatan_guru ?: 'Buka riwayat untuk melihat catatan pembimbing.', 'rose'],
            $todayLogbook->verifikasi_status === 'disetujui' => ['Logbook hari ini disetujui', 'Catatan kegiatanmu sudah diperiksa pembimbing.', 'emerald'],
            default => ['Logbook menunggu verifikasi', 'Laporanmu sudah terkirim dan sedang diperiksa.', 'blue'],
        };
        $statusStyle = [
            'slate' => 'border-slate-200 bg-slate-50 text-slate-700',
            'amber' => 'border-amber-200 bg-amber-50 text-amber-800',
            'rose' => 'border-rose-200 bg-rose-50 text-rose-800',
            'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'blue' => 'border-blue-200 bg-blue-50 text-blue-800',
        ][$todayStatus[2]];
        $attendanceRate = $stats['total'] > 0 ? (int) round(($stats['hadir'] / $stats['total']) * 100) : null;
    @endphp
    <section class="flex flex-col justify-between gap-5 rounded-2xl bg-gradient-to-r from-blue-700 to-blue-600 p-6 text-white shadow-sm sm:flex-row sm:items-center sm:p-8">
        <div class="max-w-2xl"><p class="text-sm font-semibold text-blue-100">Portal Praktik Kerja Lapangan</p><h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Selamat datang, {{ $studentName }}!</h1><p class="mt-2 text-sm leading-6 text-blue-100">Pantau aktivitas dan progres Praktik Kerja Lapangan kamu.</p></div>
        <a href="{{ route('siswa.logbook', ['tanggal' => today()->toDateString()]) }}" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-blue-700 shadow-sm transition hover:bg-blue-50 sm:self-auto"><i data-lucide="plus" class="h-4 w-4"></i>Isi logbook</a>
    </section>

    @if ($editingLogbook)
        <section class="flex flex-col justify-between gap-4 rounded-2xl border border-rose-200 bg-rose-50 p-5 sm:flex-row sm:items-center sm:p-6">
            <div><p class="text-xs font-bold uppercase tracking-wide text-rose-700">Tindak lanjut pembimbing</p><h2 class="mt-1 font-bold text-rose-900">Perbaiki Logbook {{ $editingLogbook->tanggal->translatedFormat('d F Y') }}</h2><p class="mt-1 text-sm text-rose-800">{{ $editingLogbook->catatan_guru ?: 'Periksa kembali uraian kegiatan dan lengkapi laporan.' }}</p><p class="mt-2 text-sm text-slate-700">{{ $editingLogbook->kegiatan }}</p></div>
            <div class="flex shrink-0 flex-wrap gap-2"><a href="{{ route('siswa.calendar', ['tanggal' => $editingLogbook->tanggal->toDateString(), 'bulan' => $editingLogbook->tanggal->month, 'tahun' => $editingLogbook->tanggal->year]) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 text-sm font-bold text-white hover:bg-rose-700"><i data-lucide="pencil" class="h-4 w-4"></i>Perbaiki & kirim ulang</a><form method="POST" action="{{ route('siswa.logbook.destroy', $editingLogbook->id) }}">@csrf @method('DELETE')<button type="button" data-delete-trigger data-delete-name="logbook {{ $editingLogbook->tanggal->translatedFormat('d M Y') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-rose-200 bg-white px-4 text-sm font-bold text-rose-700 hover:bg-rose-50"><i data-lucide="trash-2" class="h-4 w-4"></i>Hapus logbook</button></form></div>
        </section>
    @endif

    @if ($placement)
        <section class="flex flex-col justify-between gap-4 rounded-2xl border p-5 sm:flex-row sm:items-center sm:p-6 {{ $statusStyle }}">
            <div class="flex items-start gap-3"><span class="mt-0.5 grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white/80"><i data-lucide="clipboard-check" class="h-5 w-5"></i></span><div><p class="text-xs font-bold uppercase tracking-wide opacity-70">Status PKL hari ini</p><h2 class="mt-1 text-base font-bold">{{ $todayStatus[0] }}</h2><p class="mt-1 text-sm opacity-80">{{ $todayLogbook ? 'Laporan hari ini sudah dikirim. '.$todayStatus[1] : $todayStatus[1] }}</p></div></div>
            <div class="flex flex-wrap gap-2"><a href="{{ route('siswa.calendar', ['tanggal' => today()->toDateString()]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-current/15 bg-white px-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i data-lucide="calendar-days" class="h-4 w-4"></i>Lihat kalender</a><a href="{{ route('siswa.history') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-slate-900 px-3.5 text-sm font-semibold text-white hover:bg-slate-800"><i data-lucide="history" class="h-4 w-4"></i>Riwayat</a></div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-slate-500">Total logbook</span><span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="notebook-pen" class="h-5 w-5"></i></span></div><p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $stats['total'] }}</p><p class="mt-1 text-xs text-slate-500">Laporan kegiatan tercatat</p></article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-slate-500">Kehadiran</span><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600"><i data-lucide="calendar-check-2" class="h-5 w-5"></i></span></div><p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $attendanceRate !== null ? $attendanceRate.'%' : '—' }}</p><p class="mt-1 text-xs text-slate-500">Hadir {{ $stats['hadir'] }} hari</p></article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-slate-500">Disetujui</span><span class="grid h-10 w-10 place-items-center rounded-xl bg-green-50 text-green-600"><i data-lucide="badge-check" class="h-5 w-5"></i></span></div><p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $stats['disetujui'] }}</p><p class="mt-1 text-xs text-slate-500">{{ $stats['belum'] }} menunggu · {{ $stats['ditolak'] }} perlu perbaikan</p></article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-slate-500">Progres PKL</span><span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-50 text-violet-600"><i data-lucide="chart-no-axes-column-increasing" class="h-5 w-5"></i></span></div><p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $progress['percent'] !== null ? $progress['percent'].'%' : '—' }}</p>@if ($progress['percent'] !== null)<div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-violet-600" style="width: {{ $progress['percent'] }}%"></div></div><p class="mt-1 text-xs text-slate-500">Sisa {{ $progress['days_left'] }} hari</p>@else<p class="mt-1 text-xs text-slate-500">Periode belum tersedia</p>@endif</article>
        </section>

        <section class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(280px,.8fr)]">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><div><h2 class="font-bold text-slate-900">Logbook terbaru</h2><p class="mt-1 text-xs text-slate-500">Aktivitas PKL yang terakhir kamu catat</p></div><a href="{{ route('siswa.history') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">Lihat semua</a></div>
                @if ($logbooks->count())
                    <div class="divide-y divide-slate-100">
                        @foreach ($logbooks->take(5) as $logbook)
                            @php($badge = ['disetujui' => 'bg-emerald-50 text-emerald-700', 'ditolak' => 'bg-rose-50 text-rose-700', 'belum' => 'bg-amber-50 text-amber-700'][$logbook->verifikasi_status] ?? 'bg-slate-100 text-slate-700')
                            @php($statusLabel = ['belum' => 'Menunggu Pemeriksaan Guru', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'][$logbook->verifikasi_status] ?? ucfirst($logbook->verifikasi_status))
                            <article class="flex items-start gap-4 px-5 py-4 sm:px-6"><div class="w-14 shrink-0 rounded-xl bg-slate-50 px-2 py-2 text-center"><span class="block text-lg font-bold text-slate-800">{{ $logbook->tanggal->format('d') }}</span><span class="text-[10px] font-semibold uppercase text-slate-500">{{ $logbook->tanggal->translatedFormat('M') }}</span></div><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><span class="text-sm font-bold capitalize text-slate-800">{{ $logbook->status_kehadiran }}</span><span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $badge }}">{{ $statusLabel }}</span></div><p class="mt-1 line-clamp-2 text-sm leading-5 text-slate-600">{{ $logbook->kegiatan }}</p></div></article>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-12 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-blue-600"><i data-lucide="notebook-text" class="h-6 w-6"></i></span><p class="mt-3 text-sm font-semibold text-slate-800">Belum ada logbook</p><p class="mt-1 text-xs text-slate-500">Aktivitas yang kamu catat akan tampil di sini.</p></div>
                @endif
            </div>
            <aside class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="building-2" class="h-5 w-5"></i></span><div><p class="text-xs font-semibold text-slate-500">Tempat PKL</p><h2 class="mt-0.5 font-bold text-slate-900">{{ $placement->nama_perusahaan ?: ($placement->industri?->industriProfile?->nama_perusahaan ?? 'Belum diatur') }}</h2></div></div><div class="mt-5 space-y-3 border-t border-slate-100 pt-4 text-sm"><div class="flex justify-between gap-3"><span class="text-slate-500">Pembimbing</span><span class="text-right font-semibold text-slate-800">{{ $placement->guru_nama ?: ($placement->guru?->guruProfile?->nama_lengkap ?? $placement->guru?->name ?? 'Belum diatur') }}</span></div><div class="flex justify-between gap-3"><span class="text-slate-500">Periode</span><span class="text-right font-semibold text-slate-800">{{ $placement->tanggal_mulai?->translatedFormat('d M Y') ?? '—' }} – {{ $placement->tanggal_selesai?->translatedFormat('d M Y') ?? '—' }}</span></div><div class="flex justify-between gap-3"><span class="text-slate-500">Status</span><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold capitalize text-emerald-700">{{ $placement->status }}</span></div></div></div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-5"><div class="flex items-center gap-2 text-blue-800"><i data-lucide="lightbulb" class="h-4 w-4"></i><h3 class="text-sm font-bold">Tips logbook</h3></div><ul class="mt-3 space-y-2 text-xs leading-5 text-blue-900/75"><li>Catat kegiatan setiap hari agar progresmu tetap lengkap.</li><li>Jelaskan pekerjaan secara ringkas dan mudah dipahami.</li><li>Jika laporan ditolak, baca catatan pembimbing lalu kirim perbaikan.</li></ul></div>
            </aside>
        </section>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm"><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-50 text-blue-600"><i data-lucide="briefcase-business" class="h-7 w-7"></i></span><h2 class="mt-4 text-xl font-bold text-slate-900">Penempatan PKL belum tersedia</h2><p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">Penempatan, perusahaan, dan pembimbing akan tampil setelah diatur oleh admin sekolah.</p></section>
    @endif
</x-siswa-layout>
