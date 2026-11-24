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
                ->with(['siswa.siswaProfile', 'guru.guruProfile', 'industri.industriProfile', 'sertifikat', 'penilaian'])
                ->withCount('logbooks')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function createStudent(): View
    {
        return view('admin.students.create');
    }

    public function createTeacher(): View
    {
        return view('admin.teachers.create');
    }

    public function createIndustry(): View
    {
        return view('admin.industries.create');
    }

    public function createPlacement(): View
    {
        return view('admin.placements.create', [
            'students' => User::query()
                ->where('role', 'siswa_pkl')
                ->with('siswaProfile')
                ->orderBy('name')
                ->get(),
            'teachers' => User::query()
                ->where('role', 'guru_pembimbing')
                ->with('guruProfile')
                ->orderBy('name')
                ->get(),
            'industries' => User::query()->where('role', 'pembimbing_industri')->with('industriProfile')->orderBy('name')->get(),
        ]);
    }

    public function editPlacement(PKLSiswa $pklSiswa): View
    {
        $pklSiswa->load(['siswa.siswaProfile', 'sertifikat', 'penilaian'])->loadCount('logbooks');

        return view('admin.placements.edit', [
            'placement' => $pklSiswa,
            'students' => User::query()
                ->where('role', 'siswa_pkl')
                ->with('siswaProfile')
                ->orderBy('name')
                ->get(),
            'teachers' => User::query()
                ->where('role', 'guru_pembimbing')
                ->with('guruProfile')
                ->orderBy('name')
                ->get(),
            'industries' => User::query()->where('role', 'pembimbing_industri')->with('industriProfile')->orderBy('name')->get(),
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
            'alamat_perusahaan' => ['nullable', 'string', 'max:255'],
            'no_hp_perusahaan' => ['nullable', 'string', 'max:50'],
            'jabatan_pembimbing' => ['nullable', 'string', 'max:255'],
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
                    'alamat' => $validated['alamat_perusahaan'] ?? null,
                    'no_hp' => $validated['no_hp_perusahaan'] ?? null,
                    'jabatan' => $validated['jabatan_pembimbing'] ?? null,
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
            'guru_id' => ['required', Rule::exists('users', 'id')->where(fn (QueryBuilder $query) => $query->where('role', 'guru_pembimbing'))],
            'industri_id' => ['nullable', Rule::exists('users', 'id')->where(fn (QueryBuilder $query) => $query->where('role', 'pembimbing_industri'))],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:pending,aktif,selesai'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $teacher = User::query()->with('guruProfile')->findOrFail($validated['guru_id']);
        $industry = isset($validated['industri_id']) ? User::query()->with('industriProfile')->findOrFail($validated['industri_id']) : null;
        if ($industry?->industriProfile) {
            $validated['nama_perusahaan'] = $industry->industriProfile->nama_perusahaan;
            $validated['nama_pembimbing'] = $industry->industriProfile->nama_pembimbing;
        }

        DB::transaction(function () use ($validated, $teacher): void {
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
                'guru_nama' => $teacher->guruProfile?->nama_lengkap ?? $teacher->name,
                'guru_nip' => $teacher->guruProfile?->nip,
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
            'guru_id' => ['required', Rule::exists('users', 'id')->where(fn (QueryBuilder $query) => $query->where('role', 'guru_pembimbing'))],
            'industri_id' => ['nullable', Rule::exists('users', 'id')->where(fn (QueryBuilder $query) => $query->where('role', 'pembimbing_industri'))],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nama_pembimbing' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:pending,aktif,selesai,dibatalkan'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $teacher = User::query()->with('guruProfile')->findOrFail($validated['guru_id']);
        $industry = isset($validated['industri_id']) ? User::query()->with('industriProfile')->findOrFail($validated['industri_id']) : null;
        if ($industry?->industriProfile) {
            $validated['nama_perusahaan'] = $industry->industriProfile->nama_perusahaan;
            $validated['nama_pembimbing'] = $industry->industriProfile->nama_pembimbing;
        }

        DB::transaction(function () use ($validated, $pklSiswa, $teacher): void {
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

            $pklSiswa->update([
                ...$validated,
                'guru_nama' => $teacher->guruProfile?->nama_lengkap ?? $teacher->name,
                'guru_nip' => $teacher->guruProfile?->nip,
            ]);
        });

        return redirect()->route('admin.dashboard')
            ->with('success', 'Data penempatan berhasil diperbarui.');
    }

    public function destroyPlacement(PKLSiswa $pklSiswa): RedirectResponse
    {
        $canDelete = DB::transaction(function () use ($pklSiswa): bool {
            $placement = PKLSiswa::query()->whereKey($pklSiswa->id)->lockForUpdate()->firstOrFail();
            if ($placement->logbooks()->exists() || $placement->penilaian()->exists() || $placement->sertifikat()->exists()) {
                return false;
            }

            $placement->delete();

            return true;
        });

        if (! $canDelete) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Penempatan tidak dapat dihapus karena memiliki logbook, penilaian, atau sertifikat.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Penempatan berhasil dihapus.');
    }
}
