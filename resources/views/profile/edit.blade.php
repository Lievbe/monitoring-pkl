@php
    $roleLabel = match ($user->role) {
        'admin_hubin' => 'Admin Hubin',
        'siswa_pkl' => 'Siswa PKL',
        'guru_pembimbing' => 'Guru Pembimbing',
        'pembimbing_industri' => 'Pembimbing Industri',
        default => 'Pengguna',
    };
    $displayName = match ($user->role) {
        'siswa_pkl' => $user->siswaProfile?->nama_lengkap ?: $user->name,
        'guru_pembimbing' => $user->guruProfile?->nama_lengkap ?: $user->name,
        'pembimbing_industri' => $user->industriProfile?->nama_pembimbing ?: $user->name,
        default => $user->name,
    };
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="mx-auto max-w-7xl"><p class="text-sm font-semibold text-blue-700">Akun dan preferensi</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Profil Saya</h1><p class="mt-2 text-sm text-slate-500">Kelola informasi akun dan keamanan di satu tempat.</p></div>
    </x-slot>

    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-7 sm:px-6 lg:grid-cols-[280px_minmax(0,1fr)] lg:px-8">
        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            @if ($user->avatar_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="Foto profil {{ $displayName }}" class="mx-auto h-24 w-24 rounded-full object-cover ring-4 ring-blue-50">
            @else
                <span class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-blue-100 text-3xl font-bold text-blue-700 ring-4 ring-blue-50">{{ mb_strtoupper(mb_substr($displayName, 0, 1)) }}</span>
            @endif
            <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $displayName }}</h2><p class="mt-1 break-all text-sm text-slate-500">{{ $user->email }}</p><span class="mt-3 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $roleLabel }}</span>
            <dl class="mt-6 space-y-3 border-t border-slate-100 pt-5 text-left text-sm">
                @if ($user->siswaProfile)<div class="flex justify-between gap-3"><dt class="text-slate-500">NIS / NISN</dt><dd class="text-right font-semibold text-slate-800">{{ $user->siswaProfile->nis ?: '—' }} / {{ $user->siswaProfile->nisn ?: '—' }}</dd></div><div class="flex justify-between gap-3"><dt class="text-slate-500">Kelas</dt><dd class="text-right font-semibold text-slate-800">{{ $user->siswaProfile->kelas ?: '—' }}</dd></div><div><dt class="text-slate-500">Program keahlian</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->siswaProfile->jurusan ?: '—' }}</dd></div>
                @elseif ($user->guruProfile)<div class="flex justify-between gap-3"><dt class="text-slate-500">NIP</dt><dd class="text-right font-semibold text-slate-800">{{ $user->guruProfile->nip ?: '—' }}</dd></div><div><dt class="text-slate-500">Mata pelajaran</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->guruProfile->mapel ?: '—' }}</dd></div>
                @elseif ($user->industriProfile)<div><dt class="text-slate-500">Perusahaan</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->industriProfile->nama_perusahaan }}</dd></div><div><dt class="text-slate-500">Jabatan</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->industriProfile->jabatan ?: '—' }}</dd></div><div><dt class="text-slate-500">Telepon</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->industriProfile->no_hp ?: '—' }}</dd></div>
                @else<div><dt class="text-slate-500">Akses</dt><dd class="mt-1 font-semibold text-slate-800">Pengelolaan Hubungan Industri</dd></div>@endif
            </dl>
        </aside>

        <div class="min-w-0 space-y-6">
            @if (session('status') === 'profile-updated')<div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">Perubahan profil berhasil disimpan.</div>@endif
            <section id="account-settings" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7"><div class="mb-5"><h2 class="font-bold text-slate-900">Informasi profil</h2><p class="mt-1 text-sm text-slate-500">Nama dan email akun yang digunakan untuk masuk.</p></div>@include('profile.partials.update-profile-information-form')</section>
            <section id="security" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7"><div class="mb-5"><h2 class="font-bold text-slate-900">Keamanan akun</h2><p class="mt-1 text-sm text-slate-500">Ganti kata sandi dan pastikan akun tetap aman.</p></div>@include('profile.partials.update-password-form')</section>
            <section class="rounded-2xl border border-red-100 bg-white p-5 shadow-sm sm:p-7">@include('profile.partials.delete-user-form')</section>
        </div>
    </div>
</x-app-layout>
