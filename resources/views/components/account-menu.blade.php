@props(['user'])

@php
    $displayName = match ($user->role) {
        'siswa_pkl' => $user->siswaProfile?->nama_lengkap ?: $user->name,
        'guru_pembimbing' => $user->guruProfile?->nama_lengkap ?: $user->name,
        'pembimbing_industri' => $user->industriProfile?->nama_pembimbing ?: $user->name,
        default => $user->name,
    };
    $roleLabel = match ($user->role) {
        'admin_hubin' => 'Admin Hubin',
        'siswa_pkl' => 'Siswa PKL',
        'guru_pembimbing' => 'Guru Pembimbing',
        'pembimbing_industri' => 'Pembimbing Industri',
        default => 'Pengguna',
    };
@endphp

<div class="relative shrink-0" data-account-menu>
    <button type="button" data-account-toggle aria-expanded="false" aria-haspopup="true" aria-label="Buka menu akun"
        class="flex min-h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-2.5 text-left shadow-sm transition hover:border-blue-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/30 sm:gap-3 sm:pr-3">
        @if ($user->avatar_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="" class="h-9 w-9 rounded-full object-cover ring-2 ring-blue-50">
        @else
            <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-100 text-sm font-bold text-blue-700 ring-2 ring-blue-50">{{ mb_strtoupper(mb_substr($displayName, 0, 1)) }}</span>
        @endif
        <span class="hidden min-w-0 text-left sm:block"><span class="block max-w-36 truncate text-xs font-bold text-slate-800">{{ $displayName }}</span><span class="block text-[11px] text-slate-500">{{ $roleLabel }}</span></span>
        <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 text-slate-400"></i>
    </button>

    <div data-account-panel hidden class="account-menu-panel absolute right-0 z-50 mt-3 w-[288px] max-w-[calc(100vw-2rem)] origin-top-right overflow-hidden rounded-[14px] border border-slate-200 bg-white shadow-xl shadow-slate-900/10">
        <div class="flex items-center gap-3 bg-slate-50 p-4">
            @if ($user->avatar_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="" class="h-12 w-12 rounded-full object-cover ring-2 ring-white shadow-sm">
            @else
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-blue-100 text-lg font-bold text-blue-700 ring-2 ring-white">{{ mb_strtoupper(mb_substr($displayName, 0, 1)) }}</span>
            @endif
            <span class="min-w-0"><span class="block truncate text-sm font-bold text-slate-900">{{ $displayName }}</span><span class="mt-0.5 block truncate text-xs text-slate-500">{{ $user->email }}</span><span class="mt-1 inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">{{ $roleLabel }}</span></span>
        </div>
        <div class="border-t border-slate-100 p-2">
            <a href="{{ route('profile.edit') }}" class="flex min-h-10 items-center gap-3 rounded-lg px-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"><i data-lucide="user-round" class="h-4 w-4"></i>Profil Saya</a>
            <a href="{{ route('profile.edit') }}#account-settings" class="flex min-h-10 items-center gap-3 rounded-lg px-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"><i data-lucide="settings-2" class="h-4 w-4"></i>Pengaturan Akun</a>
            <a href="{{ route('profile.edit') }}#security" class="flex min-h-10 items-center gap-3 rounded-lg px-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700"><i data-lucide="shield-check" class="h-4 w-4"></i>Keamanan &amp; kata sandi</a>
        </div>
        <div class="border-t border-slate-100 p-2">
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="flex min-h-10 w-full items-center gap-3 rounded-lg px-3 text-sm font-semibold text-red-600 transition hover:bg-red-50"><i data-lucide="log-out" class="h-4 w-4"></i>Keluar</button></form>
        </div>
    </div>
</div>
