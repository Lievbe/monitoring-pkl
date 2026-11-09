<x-guest-layout>
    <div class="mb-8">
        <div class="mx-auto mb-5 grid h-14 w-14 place-items-center rounded-2xl bg-amber-50 border border-amber-100">
            <svg class="h-7 w-7 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Konfirmasi kata sandi</h1>
        <p class="mt-1.5 text-sm leading-6 text-slate-500">
            Ini adalah area aman. Konfirmasikan kata sandimu untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <div class="relative">
                <x-text-input
                    id="password" name="password" type="password"
                    placeholder="Masukkan kata sandi kamu"
                    required autocomplete="current-password"
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

        <x-primary-button>
            Konfirmasi &amp; lanjutkan
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
