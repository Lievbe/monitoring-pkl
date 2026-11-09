<x-app-layout>
    <x-slot name="header">
        <div class="rounded-xl border border-blue-100 bg-white p-6 shadow-sm">
            <div class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                Pengaturan Akun
            </div>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                Profil Pengguna
            </h1>
            <p class="mt-1 text-sm text-slate-600">
                Perbarui informasi akun, alamat email, dan kata sandi Anda.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
