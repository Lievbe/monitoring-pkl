<x-industri-layout>
    <section class="rounded-2xl bg-gradient-to-r from-blue-800 to-blue-600 p-6 text-white sm:p-8">
        <p class="text-sm font-semibold text-blue-100">Profil Mitra PKL</p>
        <h1 class="mt-2 text-2xl font-bold">Profil Perusahaan</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100">Informasi dan logo ini akan digunakan pada sertifikat siswa yang ditempatkan di perusahaan Anda.</p>
    </section>

    @if ($errors->any())<div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif

    <form method="POST" action="{{ route('industri.profile.update') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        @csrf @method('PUT')
        <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <h2 class="text-base font-bold text-slate-900">Informasi perusahaan</h2>
            <label class="block text-sm font-semibold text-slate-700">Nama perusahaan<input name="nama_perusahaan" required maxlength="255" value="{{ old('nama_perusahaan', $profile->nama_perusahaan) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
            <label class="block text-sm font-semibold text-slate-700">Alamat<textarea name="alamat" rows="3" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat', $profile->alamat) }}</textarea></label>
            <div class="grid gap-5 sm:grid-cols-2"><label class="block text-sm font-semibold text-slate-700">Nomor telepon<input name="no_hp" maxlength="50" value="{{ old('no_hp', $profile->no_hp) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label><label class="block text-sm font-semibold text-slate-700">Email perusahaan<input type="email" name="email_perusahaan" maxlength="255" value="{{ old('email_perusahaan', $profile->email_perusahaan) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label></div>
            <div class="grid gap-5 sm:grid-cols-2"><label class="block text-sm font-semibold text-slate-700">Nama pimpinan / pembimbing<input name="nama_pembimbing" required maxlength="255" value="{{ old('nama_pembimbing', $profile->nama_pembimbing) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label><label class="block text-sm font-semibold text-slate-700">Jabatan<input name="jabatan" maxlength="255" value="{{ old('jabatan', $profile->jabatan) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label></div>
            <button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-700"><i data-lucide="save" class="h-4 w-4"></i>Simpan Profil</button>
        </section>

        <section class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-bold text-slate-900">Logo Perusahaan</h2><p class="mt-1 text-xs leading-5 text-slate-500">PNG, JPG, JPEG, atau WebP. Maksimal 2 MB.</p>
            <div class="mt-5 grid min-h-44 place-items-center rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4"><img id="logo-preview" src="{{ $profile->logo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($profile->logo_path) : '' }}" alt="Preview logo perusahaan" class="{{ $profile->logo_path ? '' : 'hidden' }} max-h-36 max-w-full object-contain"><span id="logo-empty" class="{{ $profile->logo_path ? 'hidden' : '' }} text-center text-sm font-semibold text-slate-400">Logo belum diunggah</span></div>
            <label for="logo-file" class="mt-4 inline-flex min-h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 text-sm font-bold text-blue-700 hover:bg-blue-100"><i data-lucide="upload" class="h-4 w-4"></i>{{ $profile->logo_path ? 'Ganti Logo' : 'Upload Logo Perusahaan' }}</label><input id="logo-file" type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="sr-only"><p id="logo-filename" class="mt-2 text-center text-xs text-slate-500"></p>
            @if ($profile->logo_path)<button form="delete-company-logo" type="button" data-delete-trigger data-delete-name="logo perusahaan" class="mt-3 min-h-10 w-full rounded-xl border border-rose-200 px-4 text-sm font-semibold text-rose-700 hover:bg-rose-50"><i data-lucide="trash-2" class="mr-1 inline h-4 w-4"></i>Hapus Logo</button>@endif
        </section>
    </form>
    @if ($profile->logo_path)<form id="delete-company-logo" method="POST" action="{{ route('industri.profile.logo.destroy') }}" class="hidden">@csrf @method('DELETE')</form>@endif
    <script>
        document.getElementById('logo-file').addEventListener('change', (event) => {
            const file = event.target.files[0]; if (!file) return;
            document.getElementById('logo-preview').src = URL.createObjectURL(file);
            document.getElementById('logo-preview').classList.remove('hidden');
            document.getElementById('logo-empty').classList.add('hidden');
            document.getElementById('logo-filename').textContent = file.name;
        });
    </script>
</x-industri-layout>
