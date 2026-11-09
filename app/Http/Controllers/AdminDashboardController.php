<?php

namespace App\Http\Controllers;

use App\Models\GuruProfile;
use App\Models\IndustriProfile;
use App\Models\PKLSiswa;
use App\Models\Sertifikat;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'students' => User::query()->where('role', 'siswa_pkl')->count(),
                'active_placements' => PKLSiswa::query()->where('status', 'aktif')->count(),
                'completed_placements' => PKLSiswa::query()->where('status', 'selesai')->count(),
                'certificates' => Sertifikat::query()->count(),
            ],
            'placements' => PKLSiswa::query()
                ->with(['siswa.siswaProfile', 'guru.guruProfile', 'industri.industriProfile', 'sertifikat'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function createStudent(): View
    {
        return view('admin.students.create');
    }

    public function createPlacement(): View
    {
        return view('admin.placements.create', [
            'students' => User::query()
                ->where('role', 'siswa_pkl')
                ->with('siswaProfile')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function editPlacement(PKLSiswa $pklSiswa): View
    {
        $pklSiswa->load('siswa.siswaProfile');

        return view('admin.placements.edit', [
            'placement' => $pklSiswa,
            'students' => User::query()
                ->where('role', 'siswa_pkl')
                ->with('siswaProfile')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['siswa_pkl', 'guru_pembimbing', 'pembimbing_industri'])],
            'jurusan' => ['required_if:role,siswa_pkl', 'nullable', 'string', 'max:255'],
            'kelas' => ['required_if:role,siswa_pkl', 'nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:100', 'unique:guru_profiles,nip'],
            'mapel' => ['nullable', 'string', 'max:255'],
            'nama_perusahaan' => ['required_if:role,pembimbing_industri', 'nullable', 'string', 'max:255'],
            'nama_pembimbing' => ['required_if:role,pembimbing_industri', 'nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            match ($validated['role']) {
                'siswa_pkl' => SiswaProfile::query()->create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $user->name,
                    'jurusan' => $validated['jurusan'],
                    'kelas' => $validated['kelas'],
                ]),
                'guru_pembimbing' => GuruProfile::query()->create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $user->name,
                    'nip' => $validated['nip'] ?? null,
                    'mapel' => $validated['mapel'] ?? null,
                ]),
                'pembimbing_industri' => IndustriProfile::query()->create([
                    'user_id' => $user->id,
                    'nama_perusahaan' => $validated['nama_perusahaan'],
                    'nama_pembimbing' => $validated['nama_pembimbing'],
                ]),
            };
        });

        return redirect()->route('admin.dashboard')
            ->with('success', 'Akun dan profil berhasil dibuat.');
    }

    public function storePlacement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', Rule::exists('users', 'id')->where(fn (QueryBuilder $query) => $query->where('role', 'siswa_pkl'))],
            'guru_nama' => ['required', 'string', 'max:255'],
            'guru_nip' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:pending,aktif,selesai'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated): void {
            User::query()->whereKey($validated['siswa_id'])->lockForUpdate()->firstOrFail();

            $hasCurrentPlacement = PKLSiswa::query()
                ->where('siswa_id', $validated['siswa_id'])
                ->whereIn('status', ['pending', 'aktif'])
                ->exists();

            if ($hasCurrentPlacement) {
                throw ValidationException::withMessages([
                    'siswa_id' => 'Siswa sudah memiliki penempatan yang masih berjalan.',
                ]);
            }

            PKLSiswa::query()->create([
                ...$validated,
                'guru_id' => null,
                'industri_id' => null,
            ]);
        });

        return redirect()->route('admin.dashboard')
            ->with('success', 'Penempatan PKL berhasil disimpan.');
    }

    public function completePlacement(PKLSiswa $pklSiswa): RedirectResponse
    {
        if ($pklSiswa->status !== 'aktif') {
            throw ValidationException::withMessages([
                'status' => 'Hanya penempatan aktif yang dapat ditandai selesai.',
            ]);
        }

        $pklSiswa->update(['status' => 'selesai']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Penempatan berhasil ditandai selesai. Sertifikat sekarang dapat dibuat.');
    }

    public function updatePlacement(Request $request, PKLSiswa $pklSiswa): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', Rule::exists('users', 'id')->where(fn (QueryBuilder $query) => $query->where('role', 'siswa_pkl'))],
            'guru_nama' => ['required', 'string', 'max:255'],
            'guru_nip' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:pending,aktif,selesai,dibatalkan'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated, $pklSiswa): void {
            User::query()->whereKey($validated['siswa_id'])->lockForUpdate()->firstOrFail();

            $hasOtherCurrentPlacement = PKLSiswa::query()
                ->where('siswa_id', $validated['siswa_id'])
                ->whereIn('status', ['pending', 'aktif'])
                ->where('id', '!=', $pklSiswa->id)
                ->exists();

            if ($hasOtherCurrentPlacement && in_array($validated['status'], ['pending', 'aktif'], true)) {
                throw ValidationException::withMessages([
                    'siswa_id' => 'Siswa sudah memiliki penempatan lain yang masih berjalan.',
                ]);
            }

            $pklSiswa->update($validated);
        });

        return redirect()->route('admin.dashboard')
            ->with('success', 'Data penempatan berhasil diperbarui.');
    }
}
