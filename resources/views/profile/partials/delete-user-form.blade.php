<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-700">
            Hapus akun
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Tindakan ini permanen. Akun admin dan akun yang masih terhubung dengan riwayat PKL akan dilindungi.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    ><i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>Hapus Akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

        <h2 class="text-lg font-bold text-slate-900">
            Hapus Akun?
            </h2>

        <p class="mt-2 text-sm leading-6 text-slate-600">
            Akun <strong>{{ $user->name }}</strong> akan dihapus. Riwayat PKL dan akun admin yang masih digunakan tidak dapat dihapus.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-slate-300"
                    placeholder="Kata sandi saat ini"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" class="min-h-11 justify-center rounded-xl">
                    Batal
                </x-secondary-button>

                <x-danger-button class="min-h-11 justify-center rounded-xl">
                    <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>Ya, Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
