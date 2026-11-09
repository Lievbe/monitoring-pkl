<nav x-data="{ open: false }" class="sticky top-0 z-30 border-b border-blue-100 bg-white shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm">
                            <x-application-logo class="block h-5 w-5" />
                        </span>
                        <span>
                            <span class="block text-sm font-bold tracking-tight text-slate-900">Monitoring PKL</span>
                            <span class="hidden text-[11px] text-blue-600 sm:block">Portal Praktik Kerja Lapangan</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-2 sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('siswa.*') || request()->routeIs('guru.*') || request()->routeIs('industri.*')">
                        Dashboard
                    </x-nav-link>
                    @if (Auth::user()->role === 'admin_hubin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            Panel Admin
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="grid h-7 w-7 place-items-center rounded-md bg-blue-600 text-xs font-bold uppercase text-white">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="hidden text-left md:block">
                                <span class="block max-w-36 truncate text-xs font-semibold text-slate-900">{{ Auth::user()->name }}</span>
                                <span class="block text-[10px] text-blue-600 font-medium">{{ match (Auth::user()->role) {
                                    'admin_hubin' => 'Admin Hubin',
                                    'siswa_pkl' => 'Siswa PKL',
                                    'guru_pembimbing' => 'Guru Pembimbing',
                                    'pembimbing_industri' => 'Pembimbing Industri',
                                    default => 'Pengguna',
                                } }}</span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil Saya
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Buka menu navigasi">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('siswa.*') || request()->routeIs('guru.*') || request()->routeIs('industri.*')">
                Dashboard
            </x-responsive-nav-link>
            @if (Auth::user()->role === 'admin_hubin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    Panel Admin
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-100">
            <div class="px-4">
                <div class="font-medium text-base text-slate-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                <div class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                    {{ match (Auth::user()->role) {
                        'admin_hubin' => 'Admin Hubin',
                        'siswa_pkl' => 'Siswa PKL',
                        'guru_pembimbing' => 'Guru Pembimbing',
                        'pembimbing_industri' => 'Pembimbing Industri',
                        default => 'Pengguna',
                    } }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil Saya
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
