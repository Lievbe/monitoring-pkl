<x-guest-layout>
    <div class="mb-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-600">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 shadow-[0_0_0_3px_rgba(59,130,246,0.2)]"></span>
            Lupa Kata Sandi
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Reset kata sandi</h1>
        <p class="mt-1.5 text-sm leading-6 text-slate-500">
            Masukkan alamat email akunmu. Kami akan mengirim tautan untuk membuat kata sandi baru.
        </p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input
                id="email" name="email" type="email"
                placeholder="nama@email.com"
                :value="old('email')"
                required autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button>
            Kirim tautan reset
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7z"/>
            </svg>
        </x-primary-button>

        <p class="text-center text-sm text-slate-500">
            Ingat kata sandimu?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                Kembali ke login
            </a>
        </p>
    </form>
</x-guest-layout>
