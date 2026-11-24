<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isAdminPortal = $portal === 'admin';
        $account = auth()->user();
        $profile = $isAdminPortal ? null : $account->industriProfile;
        $accountName = $isAdminPortal ? $account->name : ($profile?->nama_pembimbing ?: $account->name);
        $accountInitial = mb_strtoupper(mb_substr($accountName, 0, 1));
        $portalTitle = $isAdminPortal ? 'Admin Hubin' : 'Mitra Industri';
        $pageTitle = $isAdminPortal
            ? (request()->routeIs('admin.dashboard') ? 'Dashboard' : (request()->routeIs('admin.school-identity.*') ? 'Identitas Sekolah' : (request()->routeIs('admin.students.*') ? 'Daftarkan Siswa' : (request()->routeIs('admin.teachers.*') ? 'Guru Pembimbing' : (request()->routeIs('admin.industries.*') ? 'Mitra Perusahaan' : 'Penempatan PKL')))))
            : (request()->routeIs('industri.profile.*') ? 'Profil Perusahaan' : 'Pemantauan Siswa');
        $navigation = $isAdminPortal
            ? [
                ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
                ['label' => 'Daftarkan Siswa', 'icon' => 'user-round-plus', 'href' => route('admin.students.create'), 'active' => request()->routeIs('admin.students.*')],
                ['label' => 'Guru Pembimbing', 'icon' => 'users-round', 'href' => route('admin.teachers.create'), 'active' => request()->routeIs('admin.teachers.*')],
                ['label' => 'Mitra Perusahaan', 'icon' => 'building-2', 'href' => route('admin.industries.create'), 'active' => request()->routeIs('admin.industries.*')],
                ['label' => 'Penempatan PKL', 'icon' => 'briefcase-business', 'href' => route('admin.placements.create'), 'active' => request()->routeIs('admin.placements.*')],
                ['label' => 'Identitas Sekolah', 'icon' => 'school', 'href' => route('admin.school-identity.edit'), 'active' => request()->routeIs('admin.school-identity.*')],
            ]
            : [
                ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'href' => route('industri.dashboard'), 'active' => true],
                ['label' => 'Siswa PKL', 'icon' => 'users-round', 'href' => route('industri.dashboard').'#siswa', 'active' => false],
                ['label' => 'Aktivitas Logbook', 'icon' => 'notebook-tabs', 'href' => route('industri.dashboard').'#logbook', 'active' => false],
                ['label' => 'Profil Perusahaan', 'icon' => 'building-2', 'href' => route('industri.profile.edit'), 'active' => request()->routeIs('industri.profile.*')],
            ];
    @endphp
    <title>{{ $title ?? $portalTitle.' · Monitoring PKL' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .management-sidebar, .management-main { transition: width .2s ease, margin-left .2s ease, transform .2s ease; }
        .management-collapsed #management-sidebar { width: 76px; }
        .management-collapsed #management-main { margin-left: 76px; }
        .management-collapsed .management-label, .management-collapsed .management-brand-copy, .management-collapsed .management-user-copy, .management-collapsed .management-section-label { display: none; }
        .management-collapsed .management-nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        .management-collapsed .management-brand, .management-collapsed .management-user { justify-content: center; padding-left: 0; padding-right: 0; }
        @media (max-width: 1023px) {
            #management-sidebar { width: 280px; transform: translateX(-100%); }
            #management-sidebar.mobile-open { transform: translateX(0); }
            #management-main, .management-collapsed #management-main { margin-left: 0; }
            .management-collapsed #management-sidebar { width: 280px; }
            .management-collapsed .management-label, .management-collapsed .management-brand-copy, .management-collapsed .management-user-copy, .management-collapsed .management-section-label { display: initial; }
            .management-collapsed .management-nav-link { justify-content: flex-start; padding-left: .875rem; padding-right: .875rem; }
            .management-collapsed .management-brand, .management-collapsed .management-user { justify-content: flex-start; padding-left: 1.25rem; padding-right: 1.25rem; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div id="management-sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden" onclick="closeManagementSidebar()"></div>
    <aside id="management-sidebar" class="management-sidebar fixed inset-y-0 left-0 z-40 flex w-[260px] flex-col bg-[#1E293B] text-white shadow-xl shadow-slate-900/10">
        <div class="management-brand flex h-[76px] items-center gap-3 border-b border-white/10 px-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-950/30"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
            <span class="management-brand-copy min-w-0"><span class="block truncate text-sm font-bold">Monitoring PKL</span><span class="mt-0.5 block truncate text-[11px] text-slate-300">{{ $isAdminPortal ? 'Hubungan Industri Sekolah' : ($profile?->nama_perusahaan ?: 'Portal Mitra Industri') }}</span></span>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-6">
            <p class="management-section-label px-3 pb-2 text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">{{ $isAdminPortal ? 'Pengelolaan' : 'Pemantauan' }}</p>
            @foreach ($navigation as $item)
                <a href="{{ $item['href'] }}" title="{{ $item['label'] }}" class="management-nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ $item['active'] ? 'bg-blue-600 text-white shadow-sm shadow-blue-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><i data-lucide="{{ $item['icon'] }}" class="h-[18px] w-[18px] shrink-0"></i><span class="management-label">{{ $item['label'] }}</span></a>
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-3">
            <a href="{{ route('profile.edit') }}" class="management-user flex items-center gap-3 rounded-xl px-2 py-3 hover:bg-white/10"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-blue-500 text-sm font-bold text-white">{{ $accountInitial }}</span><span class="management-user-copy min-w-0 flex-1"><span class="block truncate text-xs font-bold">{{ $accountName }}</span><span class="block truncate text-[11px] text-slate-300">{{ $portalTitle }}</span></span><i data-lucide="chevrons-up-down" class="management-user-copy h-4 w-4 text-slate-400"></i></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="management-nav-link flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:bg-rose-500/15 hover:text-rose-200"><i data-lucide="log-out" class="h-[18px] w-[18px] shrink-0"></i><span class="management-label">Keluar</span></button></form>
        </div>
    </aside>
    <div id="management-main" class="management-main min-h-screen lg:ml-[260px]">
        <header class="sticky top-0 z-20 flex h-[76px] items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center gap-3"><button type="button" onclick="toggleManagementSidebar()" aria-label="Buka atau ciutkan menu" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50"><i data-lucide="panel-left" class="h-5 w-5"></i></button><div class="hidden sm:block"><p class="text-[11px] font-medium text-slate-400">{{ $portalTitle }} <span class="px-1">/</span> <span class="text-slate-600">{{ $pageTitle }}</span></p><p class="mt-0.5 text-sm font-bold text-slate-800">{{ $isAdminPortal ? 'Kelola data PKL sekolah' : ($profile?->nama_perusahaan ?: 'Pantau kegiatan siswa di mitra') }}</p></div></div>
            <div class="flex items-center gap-2 sm:gap-4"><span class="hidden text-xs font-medium text-slate-500 md:block">{{ now()->translatedFormat('l, d F Y') }}</span><a href="{{ $isAdminPortal ? route('admin.dashboard').'#penempatan' : route('industri.dashboard').'#logbook' }}" aria-label="Lihat aktivitas terbaru" class="grid h-10 w-10 place-items-center rounded-xl text-slate-500 hover:bg-slate-50"><i data-lucide="bell" class="h-[18px] w-[18px]"></i></a><x-account-menu :user="$account" /></div>
        </header>
        <main class="mx-auto w-full max-w-[1440px] space-y-6 p-4 sm:p-6 lg:p-8">
            @if (session('success'))<div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"><i data-lucide="circle-check" class="mt-0.5 h-5 w-5 shrink-0"></i><p>{{ session('success') }}</p></div>@endif
            @if (session('error'))<div role="alert" class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><i data-lucide="circle-alert" class="mt-0.5 h-5 w-5 shrink-0"></i><p>{{ session('error') }}</p></div>@endif
            @isset($header){{ $header }}@endisset
            {{ $slot }}
        </main>
    </div>
    <script>
        function toggleManagementSidebar() {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                document.body.classList.toggle('management-collapsed');
                return;
            }
            document.getElementById('management-sidebar').classList.toggle('mobile-open');
            document.getElementById('management-sidebar-overlay').classList.toggle('hidden');
        }
        function closeManagementSidebar() {
            document.getElementById('management-sidebar').classList.remove('mobile-open');
            document.getElementById('management-sidebar-overlay').classList.add('hidden');
        }
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
    <x-delete-confirmation-modal />
</body>
</html>
