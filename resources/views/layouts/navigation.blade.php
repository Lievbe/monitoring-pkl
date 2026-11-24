<nav x-data="{ open: false }" class="sticky top-0 z-30 border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-6">
            <a href="{{ route('dashboard') }}" class="inline-flex shrink-0 items-center gap-2.5"><span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-600 text-white"><x-application-logo class="h-4 w-4" /></span><span><span class="block text-sm font-bold tracking-tight text-slate-800">Monitoring PKL</span><span class="hidden text-[11px] text-slate-500 sm:block">Portal Praktik Kerja Lapangan</span></span></a>
            <div class="hidden items-center gap-1 sm:flex"><x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('siswa.*') || request()->routeIs('guru.*') || request()->routeIs('industri.*')">Dashboard</x-nav-link>@if (Auth::user()->role === 'admin_hubin')<x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Panel Admin</x-nav-link>@endif</div>
        </div>
        <div class="flex shrink-0 items-center gap-2"><x-account-menu :user="Auth::user()" /><button @click="open = ! open" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 sm:hidden" aria-label="Buka navigasi"><i data-lucide="menu" class="h-5 w-5"></i></button></div>
    </div>
    <div x-show="open" x-transition class="border-t border-slate-200 bg-white p-3 sm:hidden"><x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('siswa.*') || request()->routeIs('guru.*') || request()->routeIs('industri.*')">Dashboard</x-responsive-nav-link>@if (Auth::user()->role === 'admin_hubin')<x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Panel Admin</x-responsive-nav-link>@endif</div>
</nav>
