<x-guest-layout>
    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-600">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 shadow-[0_0_0_3px_rgba(59,130,246,0.2)]"></span>
            Reset Kata Sandi
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat kata sandi baru</h1>
        <p class="mt-1.5 text-sm leading-6 text-slate-500">
            Masukkan kata sandi baru untuk akunmu. Pastikan minimal 8 karakter.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input
                id="email" name="email" type="email"
                placeholder="nama@email.com"
                :value="old('email', $request->email)"
                required autofocus autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password Baru --}}
        <div>
            <x-input-label for="password" value="Kata sandi baru" />
            <div class="relative">
                <x-text-input
                    id="password" name="password" type="password"
                    placeholder="Minimal 8 karakter"
                    required autocomplete="new-password"
                    class="pr-11"
                />
                <button type="button"
                    onclick="togglePass('password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan kata sandi">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi baru" />
            <div class="relative">
                <x-text-input
                    id="password_confirmation" name="password_confirmation" type="password"
                    placeholder="Ulangi kata sandi baru"
                    required autocomplete="new-password"
                    class="pr-11"
                />
                <button type="button"
                    onclick="togglePass('password_confirmation', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan konfirmasi kata sandi">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button>
            Simpan kata sandi baru
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
            </svg>
        </x-primary-button>
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
