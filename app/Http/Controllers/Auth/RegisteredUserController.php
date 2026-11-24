<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the student registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming student registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'jurusan' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa_pkl',
        ]);

        $user->siswaProfile()->create([
            'nama_lengkap' => $user->name,
            'jurusan' => $validated['jurusan'],
            'kelas' => $validated['kelas'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Display the teacher registration view.
     */
    public function createTeacher(): View
    {
        return view('auth.register-guru');
    }

    /**
     * Handle an incoming teacher registration request.
     *
     * @throws ValidationException
     */
    public function storeTeacher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'jenis_guru' => ['required', Rule::in(['guru_pembimbing', 'guru_biasa'])],
            'nip' => ['nullable', 'string', 'max:100', 'unique:guru_profiles,nip'],
            'mapel' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru_pembimbing',
        ]);

        $user->guruProfile()->create([
            'nama_lengkap' => $user->name,
            'nip' => $validated['nip'] ?? null,
            'mapel' => $validated['mapel'] ?? null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
