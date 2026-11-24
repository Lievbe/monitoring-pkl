<?php

namespace App\Http\Controllers;

use App\Services\CertificateLogoArchiver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IndustriProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('industri.profile', ['profile' => $request->user()->industriProfile]);
    }

    public function update(Request $request, CertificateLogoArchiver $archiver): RedirectResponse
    {
        $profile = $request->user()->industriProfile;
        abort_unless($profile, 403);

        $validated = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'email_perusahaan' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos/perusahaan', 'public');
        }

        unset($validated['logo']);
        $previousLogo = $profile->logo_path;
        if (array_key_exists('logo_path', $validated)) {
            $archiver->archiveIndustry($request->user());
        }
        $profile->update($validated);
        if ($previousLogo && array_key_exists('logo_path', $validated) && $previousLogo !== $profile->logo_path) {
            Storage::disk('public')->delete($previousLogo);
        }

        // Logo paths used by issued certificates are retained as archive assets.
        return back()->with('success', 'Profil perusahaan berhasil disimpan.');
    }

    public function destroyLogo(Request $request, CertificateLogoArchiver $archiver): RedirectResponse
    {
        $profile = $request->user()->industriProfile;
        abort_unless($profile, 403);
        $archiver->archiveIndustry($request->user());
        $previousLogo = $profile->logo_path;
        $profile->update(['logo_path' => null]);
        if ($previousLogo) {
            Storage::disk('public')->delete($previousLogo);
        }

        return back()->with('success', 'Logo perusahaan berhasil dihapus.');
    }
}
