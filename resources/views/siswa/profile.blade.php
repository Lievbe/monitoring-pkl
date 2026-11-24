<x-siswa-layout>
    @php
        $profile = $user->siswaProfile;
        $companyName = $placement?->nama_perusahaan ?: ($placement?->industri?->industriProfile?->nama_perusahaan ?? 'Belum diatur');
        $advisorName = $placement?->nama_pembimbing ?: ($placement?->guru_nama ?: ($placement?->guru?->guruProfile?->nama_lengkap ?? $placement?->guru?->name ?? 'Belum diatur'));
        $placementPeriod = $placement?->tanggal_mulai && $placement?->tanggal_selesai
            ? $placement->tanggal_mulai->translatedFormat('d F Y').' – '.$placement->tanggal_selesai->translatedFormat('d F Y')
            : 'Belum diatur';
    @endphp
    <section><p class="text-sm font-semibold text-blue-700">Akun siswa</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Profil saya</h1><p class="mt-2 text-sm text-slate-500">Informasi siswa dan penempatan Praktik Kerja Lapangan.</p></section>
    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,.75fr)_minmax(0,1.25fr)]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm"><span class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-blue-100 text-2xl font-bold text-blue-700">{{ mb_strtoupper(mb_substr($profile?->nama_lengkap ?: $user->name, 0, 1)) }}</span><h2 class="mt-4 text-lg font-bold text-slate-900">{{ $profile?->nama_lengkap ?: $user->name }}</h2><p class="mt-1 text-sm text-slate-500">{{ $profile?->kelas ?: 'Siswa' }}{{ $profile?->jurusan ? ' · '.$profile->jurusan : '' }}</p><a href="{{ route('profile.edit') }}" class="mt-5 inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i data-lucide="settings-2" class="h-4 w-4"></i>Pengaturan akun</a></section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><h2 class="font-bold text-slate-900">Data siswa</h2><p class="mt-1 text-sm text-slate-500">Data diambil dari profil dan penempatan yang tersimpan.</p><dl class="mt-5 grid gap-x-6 sm:grid-cols-2">@foreach ([['Nama lengkap', $profile?->nama_lengkap ?: $user->name], ['NIS', $profile?->nis ?: 'Belum tersedia'], ['NISN', $profile?->nisn ?: 'Belum tersedia'], ['Kelas', $profile?->kelas ?: 'Belum tersedia'], ['Jurusan', $profile?->jurusan ?: 'Belum tersedia'], ['Email', $user->email], ['Perusahaan PKL', $companyName], ['Pembimbing', $advisorName], ['Periode PKL', $placementPeriod]] as [$label, $value])<div class="border-b border-slate-100 py-4"><dt class="text-xs font-semibold text-slate-500">{{ $label }}</dt><dd class="mt-1 text-sm font-semibold text-slate-800">{{ $value }}</dd></div>@endforeach</dl></section>
    </div>
</x-siswa-layout>
