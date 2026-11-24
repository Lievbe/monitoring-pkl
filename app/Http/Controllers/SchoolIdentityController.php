<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Services\CertificateLogoArchiver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolIdentityController extends Controller
{
    public function edit(): View
    {
        return view('admin.school-identity', ['settings' => SchoolSetting::current()]);
    }

    public function update(Request $request, CertificateLogoArchiver $archiver): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:2000'],
            'informasi' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $settings = SchoolSetting::current();
        $previousLogo = $settings->logo_path;
        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos/sekolah', 'public');
        }

        unset($validated['logo']);
        if (array_key_exists('logo_path', $validated)) {
            $archiver->archiveSchool();
        }
        $settings->update($validated);
        if ($previousLogo && array_key_exists('logo_path', $validated) && $previousLogo !== $settings->logo_path) {
            Storage::disk('public')->delete($previousLogo);
        }

        // Logo paths used by issued certificates are retained as archive assets.
        return back()->with('success', 'Identitas sekolah berhasil disimpan.');
    }

    public function destroyLogo(CertificateLogoArchiver $archiver): RedirectResponse
    {
        $archiver->archiveSchool();
        $settings = SchoolSetting::current();
        $previousLogo = $settings->logo_path;
        $settings->update(['logo_path' => null]);
        if ($previousLogo) {
            Storage::disk('public')->delete($previousLogo);
        }

        return back()->with('success', 'Logo sekolah berhasil dihapus.');
    }
}
