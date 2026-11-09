<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto mb-5 grid h-16 w-16 place-items-center rounded-2xl bg-blue-50 border border-blue-100">
            <svg class="h-8 w-8 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Verifikasi email kamu</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Kami sudah mengirim tautan verifikasi ke email kamu. Klik tautan tersebut untuk mengaktifkan akun.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>
            </svg>
            <span>Tautan verifikasi baru telah dikirim ke alamat email kamu.</span>
        </div>
    @endif

    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3.5 text-sm text-blue-700 mb-6">
        <p class="font-semibold text-blue-800 mb-1">Tidak menerima email?</p>
        <p class="text-xs leading-5">Periksa folder <strong>Spam</strong> atau <strong>Junk</strong> kamu. Jika tetap tidak ada, klik tombol di bawah untuk mengirim ulang.</p>
    </div>

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Kirim ulang email verifikasi
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 2 11 13M22 2 15 22l-4-9-9-4 20-7z"/>
                </svg>
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/>
                </svg>
                Keluar dari akun
            </button>
        </form>
    </div>
</x-guest-layout>
