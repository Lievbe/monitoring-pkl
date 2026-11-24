<x-admin-layout>
    <x-slot name="header">
        <div class="mx-auto max-w-4xl">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Monitoring Hub</a>
            <p class="mt-5 text-xs font-bold uppercase tracking-[0.14em] text-blue-600">Kelola guru</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Daftarkan Guru Pembimbing</h1>
            <p class="mt-2 text-sm text-slate-500">Akun ini digunakan guru untuk login, melihat siswa bimbingan, dan memeriksa logbook harian.</p>
        </div>
    </x-slot>

    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-semibold">Periksa kembali data yang diisi.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-xl border border-slate-200/80 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                <h2 class="font-semibold text-slate-900">Informasi akun guru</h2>
                <p class="mt-1 text-sm text-slate-500">Berikan email dan password sementara kepada guru agar dapat masuk ke dashboard.</p>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                @csrf
                <input type="hidden" name="role" value="guru_pembimbing">
                <div class="sm:col-span-2">
                    <x-input-label for="name" value="Nama lengkap guru" class="text-slate-800" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="email" value="Email untuk login" class="text-slate-800" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nip" value="NIP (opsional)" class="text-slate-800" />
                    <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('nip')" />
                    <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="mapel" value="Mata pelajaran (opsional)" class="text-slate-800" />
                    <x-text-input id="mapel" name="mapel" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" :value="old('mapel')" />
                </div>
                <div>
                    <x-input-label for="password" value="Password sementara" class="text-slate-800" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Ulangi password" class="text-slate-800" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border-slate-300 text-slate-900 focus:border-blue-500 focus:ring-blue-500" required autocomplete="new-password" />
                </div>
                <div class="flex flex-wrap gap-3 pt-2 sm:col-span-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Simpan akun guru</button>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
                </div>
            </form>
        </section>
    </main>
</x-admin-layout>
