<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Guru · Monitoring PKL' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .guru-sidebar, .guru-main { transition: width .2s ease, margin-left .2s ease, transform .2s ease; }
        .guru-collapsed #guru-sidebar { width: 76px; }
        .guru-collapsed #guru-main { margin-left: 76px; }
        .guru-collapsed .guru-label, .guru-collapsed .guru-brand-copy, .guru-collapsed .guru-user-copy, .guru-collapsed .guru-section-label { display: none; }
        .guru-collapsed .guru-nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        .guru-collapsed .guru-brand, .guru-collapsed .guru-user { justify-content: center; padding-left: 0; padding-right: 0; }
        @media (max-width: 1023px) {
            #guru-sidebar { width: 280px; transform: translateX(-100%); }
            #guru-sidebar.mobile-open { transform: translateX(0); }
            #guru-main, .guru-collapsed #guru-main { margin-left: 0; }
            .guru-collapsed #guru-sidebar { width: 280px; }
            .guru-collapsed .guru-label, .guru-collapsed .guru-brand-copy, .guru-collapsed .guru-user-copy, .guru-collapsed .guru-section-label { display: initial; }
            .guru-collapsed .guru-nav-link { justify-content: flex-start; padding-left: .875rem; padding-right: .875rem; }
            .guru-collapsed .guru-brand, .guru-collapsed .guru-user { justify-content: flex-start; padding-left: 1.25rem; padding-right: 1.25rem; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @php
        $teacher = auth()->user();
        $teacherName = $teacher->guruProfile?->nama_lengkap ?: $teacher->name;
        $teacherInitial = mb_strtoupper(mb_substr($teacherName, 0, 1));
        $pageName = request()->routeIs('guru.dashboard') ? 'Dashboard' : (request()->routeIs('guru.students') ? 'Siswa Bimbingan' : (request()->routeIs('guru.assessments', 'guru.penilaian.*') ? 'Penilaian PKL' : (request()->query('status') === 'belum' ? 'Validasi Logbook' : 'Logbook Harian')));
    @endphp
    <div id="guru-sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden" onclick="closeGuruSidebar()"></div>
    <aside id="guru-sidebar" class="guru-sidebar fixed inset-y-0 left-0 z-40 flex w-[260px] flex-col bg-[#1E293B] text-white shadow-xl shadow-slate-900/10">
        <div class="guru-brand flex h-[76px] items-center gap-3 border-b border-white/10 px-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-950/30"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
            <span class="guru-brand-copy min-w-0"><span class="block truncate text-sm font-bold">Monitoring PKL</span><span class="mt-0.5 block truncate text-[11px] text-slate-300">Portal Guru Pembimbing</span></span>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-6">
            <p class="guru-section-label px-3 pb-2 text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">Menu utama</p>
            <a href="{{ route('guru.dashboard') }}" title="Dashboard" class="guru-nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><i data-lucide="layout-dashboard" class="h-[18px] w-[18px] shrink-0"></i><span class="guru-label">Dashboard</span></a>
            <a href="{{ route('guru.students') }}" title="Siswa Bimbingan" class="guru-nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.students') ? 'bg-blue-600 text-white shadow-sm shadow-blue-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><i data-lucide="users-round" class="h-[18px] w-[18px] shrink-0"></i><span class="guru-label">Siswa Bimbingan</span></a>
            <a href="{{ route('guru.logbooks') }}" title="Logbook Harian" class="guru-nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.logbooks') && request()->query('status') !== 'belum' ? 'bg-blue-600 text-white shadow-sm shadow-blue-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><i data-lucide="notebook-tabs" class="h-[18px] w-[18px] shrink-0"></i><span class="guru-label">Logbook Harian</span></a>
            <a href="{{ route('guru.logbooks', ['status' => 'belum']) }}" title="Validasi Logbook" class="guru-nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.logbooks') && request()->query('status') === 'belum' ? 'bg-blue-600 text-white shadow-sm shadow-blue-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><i data-lucide="clipboard-check" class="h-[18px] w-[18px] shrink-0"></i><span class="guru-label">Validasi Logbook</span></a>
            <a href="{{ route('guru.assessments') }}" title="Penilaian PKL" class="guru-nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.assessments', 'guru.penilaian.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><i data-lucide="chart-no-axes-column-increasing" class="h-[18px] w-[18px] shrink-0"></i><span class="guru-label">Penilaian PKL</span></a>
        </nav>
        <div class="border-t border-white/10 p-3">
            <a href="{{ route('profile.edit') }}" class="guru-user flex items-center gap-3 rounded-xl px-2 py-3 hover:bg-white/10"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-blue-500 text-sm font-bold text-white">{{ $teacherInitial }}</span><span class="guru-user-copy min-w-0 flex-1"><span class="block truncate text-xs font-bold">{{ $teacherName }}</span><span class="block truncate text-[11px] text-slate-300">Guru Pembimbing</span></span><i data-lucide="chevrons-up-down" class="guru-user-copy h-4 w-4 text-slate-400"></i></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="guru-nav-link flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:bg-rose-500/15 hover:text-rose-200"><i data-lucide="log-out" class="h-[18px] w-[18px] shrink-0"></i><span class="guru-label">Keluar</span></button></form>
        </div>
    </aside>
    <div id="guru-main" class="guru-main min-h-screen lg:ml-[260px]">
        <header class="sticky top-0 z-20 flex h-[76px] items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center gap-3"><button type="button" onclick="toggleGuruSidebar()" aria-label="Buka atau ciutkan menu" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50"><i data-lucide="panel-left" class="h-5 w-5"></i></button><div class="hidden sm:block"><p class="text-[11px] font-medium text-slate-400">Portal Guru <span class="px-1">/</span> <span class="text-slate-600">{{ $pageName }}</span></p><p class="mt-0.5 text-sm font-bold text-slate-800">Selamat datang, {{ $teacherName }}</p></div></div>
            <div class="flex items-center gap-2 sm:gap-4"><span class="hidden text-xs font-medium text-slate-500 md:block">{{ now()->translatedFormat('l, d F Y') }}</span><a href="{{ route('guru.logbooks', ['status' => 'belum']) }}" aria-label="Logbook menunggu validasi" class="relative grid h-10 w-10 place-items-center rounded-xl text-slate-500 hover:bg-slate-50"><i data-lucide="bell" class="h-[18px] w-[18px]"></i><span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-amber-500 ring-2 ring-white"></span></a><x-account-menu :user="$teacher" /></div>
        </header>
        <main class="mx-auto w-full max-w-[1440px] space-y-6 p-4 sm:p-6 lg:p-8">
            @if (session('success'))<div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"><i data-lucide="circle-check" class="mt-0.5 h-5 w-5 shrink-0"></i><p>{{ session('success') }}</p></div>@endif
            @if (session('error'))<div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><i data-lucide="circle-alert" class="mt-0.5 h-5 w-5 shrink-0"></i><p>{{ session('error') }}</p></div>@endif
            @isset($header){{ $header }}@endisset
            {{ $slot }}
        </main>
    </div>
    <script>
        function toggleGuruSidebar() {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                document.body.classList.toggle('guru-collapsed');
                return;
            }
            document.getElementById('guru-sidebar').classList.toggle('mobile-open');
            document.getElementById('guru-sidebar-overlay').classList.toggle('hidden');
        }
        function closeGuruSidebar() {
            document.getElementById('guru-sidebar').classList.remove('mobile-open');
            document.getElementById('guru-sidebar-overlay').classList.add('hidden');
        }
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
    <x-delete-confirmation-modal />
</body>
</html>
