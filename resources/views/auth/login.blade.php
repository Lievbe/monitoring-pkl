<x-guest-layout>
    <x-auth-session-status class="mb-5 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800" :status="session('status')" />

    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-600">
            <span class="h-1.5 w-1.5 rounded-full bg-green-500 shadow-[0_0_0_3px_rgba(34,197,94,0.2)]"></span>
            Selamat datang kembali
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke akunmu</h1>
        <p class="mt-1.5 text-sm leading-6 text-slate-500">
            Lanjutkan memantau perjalanan Praktik Kerja Lapanganmu.
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input
                id="email" name="email" type="email"
                placeholder="nama@email.com"
                :value="old('email')"
                required autofocus autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <x-input-label for="password" value="Kata sandi" class="mb-0" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <x-text-input
                    id="password" name="password" type="password"
                    placeholder="Masukkan kata sandi"
                    required autocomplete="current-password"
                    class="pr-11"
                />
                <button type="button"
                    onclick="togglePass('password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan / sembunyikan kata sandi">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <label for="remember_me" class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600 select-none">
            <input id="remember_me" type="checkbox"
                class="h-4 w-4 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-2 transition"
                name="remember">
            <span>Ingat saya di perangkat ini</span>
        </label>

        {{-- Submit --}}
        <x-primary-button class="mt-2">
            Masuk ke portal
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
            </svg>
        </x-primary-button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    Daftar sebagai siswa
                </a>
            </p>
        @endif
    </form>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.querySelector('svg').innerHTML = isPass
                ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    </script>
</x-guest-layout>
