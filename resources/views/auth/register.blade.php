<x-guest-layout>
    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-600">
            <span class="h-1.5 w-1.5 rounded-full bg-green-500 shadow-[0_0_0_3px_rgba(34,197,94,0.2)]"></span>
            Pendaftaran Siswa
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat akun siswa PKL</h1>
        <p class="mt-1.5 text-sm leading-6 text-slate-500">
            Khusus pendaftaran siswa PKL. Akun pembimbing industri dibuat oleh admin sekolah.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input
                id="name" name="name" type="text"
                placeholder="Contoh: Ahmad Fauzi"
                :value="old('name')"
                required autofocus autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input
                id="email" name="email" type="email"
                placeholder="nama@email.com"
                :value="old('email')"
                required autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Jurusan + Kelas (2 kolom) --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-input-label for="jurusan" value="Jurusan" />
                <x-text-input
                    id="jurusan" name="jurusan" type="text"
                    placeholder="Contoh: RPL"
                    :value="old('jurusan')"
                    required autocomplete="organization-title"
                />
                <x-input-error :messages="$errors->get('jurusan')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="kelas" value="Kelas" />
                <x-text-input
                    id="kelas" name="kelas" type="text"
                    placeholder="Contoh: XI RPL 1"
                    :value="old('kelas')"
                    required
                />
                <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
            </div>
        </div>

        {{-- Password --}}
        <div>
            <x-input-label for="password" value="Kata sandi" />
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
                    aria-label="Tampilkan / sembunyikan kata sandi">
                    <svg id="eye-password" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" />
            <div class="relative">
                <x-text-input
                    id="password_confirmation" name="password_confirmation" type="password"
                    placeholder="Ulangi kata sandi"
                    required autocomplete="new-password"
                    class="pr-11"
                />
                <button type="button"
                    onclick="togglePass('password_confirmation', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan / sembunyikan konfirmasi kata sandi">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Info box --}}
        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs leading-5 text-blue-700">
            <p class="font-semibold text-blue-800">Informasi akun</p>
            <p class="mt-0.5">Setelah mendaftar, akun kamu perlu dikaitkan dengan data PKL oleh admin sebelum bisa mengakses dashboard siswa.</p>
        </div>

        {{-- Submit --}}
        <x-primary-button class="mt-2">
            Buat akun siswa
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
            </svg>
        </x-primary-button>

        <p class="text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                Masuk sekarang
            </a>
        </p>
        <p class="text-center text-sm text-slate-500">
            Anda guru?
            <a href="{{ route('register.guru') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                Daftar sebagai guru
            </a>
        </p>
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
