<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load(['siswaProfile', 'guruProfile', 'industriProfile']),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        unset($validated['avatar']);
        $previousAvatar = $user->avatar_path;
        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('profile-photos', 'public');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $user->siswaProfile?->update(['nama_lengkap' => $user->name]);
        $user->guruProfile?->update(['nama_lengkap' => $user->name]);
        $user->industriProfile?->update(['nama_pembimbing' => $user->name]);
        if ($previousAvatar && isset($validated['avatar_path'])) {
            Storage::disk('public')->delete($previousAvatar);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->role === 'admin_hubin'
            || $user->placementsAsStudent()->exists()
            || $user->placementsAsTeacher()->exists()
            || $user->placementsAsIndustry()->exists()
            || $user->penilaianAsTeacher()->exists()
            || $user->verifiedLogbooks()->exists()) {
            return Redirect::route('profile.edit')
                ->withErrors(['password' => 'Akun tidak dapat dihapus karena memiliki riwayat penting atau merupakan akun admin.'], 'userDeletion');
        }

        Auth::logout();

        $user->delete();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
