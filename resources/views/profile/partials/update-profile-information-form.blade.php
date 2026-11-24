<form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">@csrf</form>
<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf @method('patch')
    <div class="grid gap-5 sm:grid-cols-[120px_minmax(0,1fr)] sm:items-center">
        <div class="flex flex-col items-center gap-2"><img id="avatar-preview" src="{{ $user->avatar_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) : '' }}" alt="Preview foto profil" class="{{ $user->avatar_path ? '' : 'hidden' }} h-20 w-20 rounded-full object-cover ring-4 ring-blue-50"><span id="avatar-fallback" class="{{ $user->avatar_path ? 'hidden' : '' }} grid h-20 w-20 place-items-center rounded-full bg-blue-100 text-2xl font-bold text-blue-700 ring-4 ring-blue-50">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span></div>
        <div><label for="avatar" class="inline-flex min-h-10 cursor-pointer items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 text-sm font-bold text-blue-700 hover:bg-blue-100"><i data-lucide="camera" class="h-4 w-4"></i>Ganti foto profil</label><input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp" class="sr-only"><p class="mt-2 text-xs text-slate-500">PNG, JPG, JPEG, atau WebP. Maksimal 2 MB.</p><p id="avatar-filename" class="mt-1 text-xs text-slate-600"></p><x-input-error class="mt-1" :messages="$errors->get('avatar')" /></div>
    </div>
    <div><x-input-label for="name" value="Nama lengkap" /><x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-slate-300" :value="old('name', $user->name)" required autocomplete="name" /><x-input-error class="mt-1" :messages="$errors->get('name')" /></div>
    <div><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-slate-300" :value="old('email', $user->email)" required autocomplete="username" /><x-input-error class="mt-1" :messages="$errors->get('email')" />
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())<p class="mt-2 text-xs text-amber-700">Email belum diverifikasi. <button form="send-verification" class="font-semibold underline">Kirim ulang tautan verifikasi</button></p>@endif
    </div>
    <div class="flex flex-wrap items-center gap-3"><button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-700"><i data-lucide="save" class="h-4 w-4"></i>Simpan perubahan</button><span class="text-xs text-slate-500">Role akun: {{ str_replace('_', ' ', ucfirst($user->role)) }}</span></div>
</form>
<script>
    document.getElementById('avatar')?.addEventListener('change', (event) => {
        const file = event.target.files[0]; if (!file) return;
        const preview = document.getElementById('avatar-preview'); preview.src = URL.createObjectURL(file); preview.classList.remove('hidden');
        document.getElementById('avatar-fallback').classList.add('hidden'); document.getElementById('avatar-filename').textContent = file.name;
    });
</script>
