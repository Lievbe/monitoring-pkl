<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Siswa PKL · Monitoring PKL' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .portal-sidebar { transition: width .2s ease, transform .2s ease; }
        .portal-main { transition: margin-left .2s ease; }
        .sidebar-collapsed #sidebar { width: 76px; }
        .sidebar-collapsed #main-content { margin-left: 76px; }
        .sidebar-collapsed .nav-label, .sidebar-collapsed .brand-copy, .sidebar-collapsed .user-copy, .sidebar-collapsed .nav-section { display: none; }
        .sidebar-collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed .sidebar-brand, .sidebar-collapsed .sidebar-user { justify-content: center; padding-left: 0; padding-right: 0; }
        @media (max-width: 1023px) {
            #sidebar { width: 280px; transform: translateX(-100%); }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-content, .sidebar-collapsed #main-content { margin-left: 0; }
            .sidebar-collapsed #sidebar { width: 280px; }
            .sidebar-collapsed .nav-label, .sidebar-collapsed .brand-copy, .sidebar-collapsed .user-copy, .sidebar-collapsed .nav-section { display: initial; }
            .sidebar-collapsed .nav-link { justify-content: flex-start; padding-left: .875rem; padding-right: .875rem; }
            .sidebar-collapsed .sidebar-brand, .sidebar-collapsed .sidebar-user { justify-content: flex-start; padding-left: 1.25rem; padding-right: 1.25rem; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @php
        $student = auth()->user();
        $studentName = $student->siswaProfile?->nama_lengkap ?: $student->name;
        $studentInitial = mb_strtoupper(mb_substr($studentName, 0, 1));
    @endphp
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/40 lg:hidden" onclick="closeSidebar()"></div>
    <aside id="sidebar" class="portal-sidebar fixed inset-y-0 left-0 z-40 flex w-[260px] flex-col border-r border-slate-200 bg-white">
        <div class="sidebar-brand flex h-[76px] items-center gap-3 border-b border-slate-100 px-5">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-200"><i data-lucide="graduation-cap" class="h-5 w-5"></i></div>
            <div class="brand-copy min-w-0">
                <p class="truncate text-sm font-bold tracking-tight text-slate-900">Monitoring PKL</p>
                <p class="truncate text-[11px] text-slate-500">Portal Praktik Kerja Lapangan</p>
            </div>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-6">
            <p class="nav-section px-3 pb-2 text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">Menu utama</p>
            <a href="{{ route('siswa.dashboard') }}" title="Dashboard" class="nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('siswa.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"><i data-lucide="layout-dashboard" class="h-[18px] w-[18px] shrink-0"></i><span class="nav-label">Dashboard</span></a>
            <a href="{{ route('siswa.logbook') }}" title="Logbook PKL" class="nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('siswa.logbook') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"><i data-lucide="book-open-check" class="h-[18px] w-[18px] shrink-0"></i><span class="nav-label">Logbook PKL</span></a>
            <a href="{{ route('siswa.calendar') }}" title="Kalender" class="nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('siswa.calendar') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"><i data-lucide="calendar-days" class="h-[18px] w-[18px] shrink-0"></i><span class="nav-label">Kalender</span></a>
            <a href="{{ route('siswa.history') }}" title="Riwayat Logbook" class="nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('siswa.history') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"><i data-lucide="history" class="h-[18px] w-[18px] shrink-0"></i><span class="nav-label">Riwayat Logbook</span></a>
            <a href="{{ route('siswa.profile') }}" title="Profil Saya" class="nav-link flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition {{ request()->routeIs('siswa.profile') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"><i data-lucide="user-round" class="h-[18px] w-[18px] shrink-0"></i><span class="nav-label">Profil Saya</span></a>
        </nav>
        <div class="border-t border-slate-100 p-3">
            <a href="{{ route('siswa.profile') }}" class="sidebar-user flex items-center gap-3 rounded-xl px-2 py-3 hover:bg-slate-50">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">{{ $studentInitial }}</span>
                <span class="user-copy min-w-0 flex-1"><span class="block truncate text-xs font-bold text-slate-800">{{ $studentName }}</span><span class="block truncate text-[11px] text-slate-500">{{ $student->siswaProfile?->kelas ?: 'Siswa PKL' }}</span></span>
                <i data-lucide="chevrons-up-down" class="user-copy h-4 w-4 text-slate-400"></i>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-500 hover:bg-rose-50 hover:text-rose-700" type="submit"><i data-lucide="log-out" class="h-[18px] w-[18px] shrink-0"></i><span class="nav-label">Keluar</span></button>
            </form>
        </div>
    </aside>
    <div id="main-content" class="portal-main min-h-screen lg:ml-[260px]">
        <header class="sticky top-0 z-20 flex h-[76px] items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" aria-label="Buka atau tutup menu" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50"><i data-lucide="panel-left" class="h-5 w-5"></i></button>
                <div class="hidden sm:block"><p class="text-[11px] font-medium text-slate-400">Portal Siswa <span class="px-1">/</span> <span class="text-slate-600">{{ request()->routeIs('siswa.dashboard') ? 'Dashboard' : (request()->routeIs('siswa.logbook') ? 'Logbook PKL' : (request()->routeIs('siswa.calendar') ? 'Kalender' : (request()->routeIs('siswa.history') ? 'Riwayat Logbook' : 'Profil Saya'))) }}</span></p><p class="mt-0.5 text-sm font-bold text-slate-800">Selamat datang, {{ $studentName }}</p></div>
            </div>
            <div class="flex items-center gap-2 sm:gap-4">
                <span class="hidden text-xs font-medium text-slate-500 md:block">{{ now()->translatedFormat('l, d F Y') }}</span>
                <a href="{{ route('siswa.history', ['status' => 'belum']) }}" aria-label="Notifikasi logbook menunggu" class="relative grid h-10 w-10 place-items-center rounded-xl text-slate-500 hover:bg-slate-50"><i data-lucide="bell" class="h-[18px] w-[18px]"></i><span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-blue-600 ring-2 ring-white"></span></a>
                <x-account-menu :user="$student" />
            </div>
        </header>
        <main class="mx-auto w-full max-w-[1440px] space-y-6 p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"><i data-lucide="circle-check" class="mt-0.5 h-5 w-5 shrink-0"></i><p>{{ session('success') }}</p></div>
            @endif
            @if (session('error'))
                <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><i data-lucide="circle-alert" class="mt-0.5 h-5 w-5 shrink-0"></i><p>{{ session('error') }}</p></div>
            @endif
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')
    <x-delete-confirmation-modal />
    <script>
        function toggleSidebar() {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                document.body.classList.toggle('sidebar-collapsed');
                return;
            }
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('mobile-open');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
</body>
</html>
