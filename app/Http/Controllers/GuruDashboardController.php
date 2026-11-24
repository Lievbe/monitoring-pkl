<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use App\Models\PKLSiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $guru = $request->user();
        $placements = $this->teacherPlacements($guru);
        $recentLogbooks = $this->teacherLogbooks($guru)
            ->with(['pklSiswa.siswa.siswaProfile'])
            ->latest('tanggal')
            ->latest('id')
            ->limit(6)
            ->get();

        $stats = [
            'students' => $placements->pluck('siswa_id')->unique()->count(),
            'placements' => $placements->whereIn('status', ['aktif', 'selesai'])->count(),
            'pending' => $this->teacherLogbooks($guru)->where('verifikasi_status', 'belum')->count(),
            'approved' => $this->teacherLogbooks($guru)->where('verifikasi_status', 'disetujui')->count(),
            'rejected' => $this->teacherLogbooks($guru)->where('verifikasi_status', 'ditolak')->count(),
        ];

        return view('guru.dashboard', compact('placements', 'recentLogbooks', 'stats'));
    }

    public function students(Request $request): View
    {
        $placements = $this->teacherPlacements($request->user());

        return view('guru.students', compact('placements'));
    }

    public function assessments(Request $request): View
    {
        $placements = $this->teacherPlacements($request->user());

        return view('guru.assessments', compact('placements'));
    }

    public function logbooks(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['semua', 'belum', 'disetujui', 'ditolak'])],
        ]);

        $filters['status'] = $filters['status'] ?? 'semua';
        $query = $this->filteredLogbooks($request->user(), $filters);
        $pendingCount = (clone $query)->where('verifikasi_status', 'belum')->count();
        $logbooks = $query->with(['pklSiswa.siswa.siswaProfile'])->latest('tanggal')->latest('id')->paginate(10)->withQueryString();
        $classes = $this->teacherPlacements($request->user())
            ->pluck('siswa.siswaProfile.kelas')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('guru.logbooks', compact('filters', 'logbooks', 'pendingCount', 'classes'));
    }

    public function approveAll(Request $request): RedirectResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['semua', 'belum', 'disetujui', 'ditolak'])],
        ]);
        $filters['status'] = $filters['status'] ?? 'semua';
        $teacher = $request->user();
        $approvalTime = now();

        $approvedCount = DB::transaction(fn (): int => $this->filteredLogbooks($teacher, $filters)
            ->where('verifikasi_status', 'belum')
            ->update([
                'verifikasi_status' => 'disetujui',
                'verified_by' => $teacher->id,
                'verified_at' => $approvalTime,
            ]));

        $message = $approvedCount > 0
            ? "{$approvedCount} logbook berhasil disetujui."
            : 'Tidak ada logbook pending yang sesuai dengan filter aktif.';

        return redirect()->route('guru.logbooks', $filters)->with('success', $message);
    }

    public function verifikasi(Request $request, int $logbook): RedirectResponse
    {
        $record = LogbookHarian::query()
            ->whereHas('pklSiswa', fn (Builder $query) => $query->where('guru_id', $request->user()->id))
            ->where('verifikasi_status', 'belum')
            ->findOrFail($logbook);

        $validated = $request->validate([
            'verifikasi_status' => ['required', 'in:disetujui,ditolak'],
            'catatan_guru' => ['nullable', 'string', 'max:2000'],
        ]);

        $updatedCount = DB::transaction(fn (): int => $this->teacherLogbooks($request->user())
            ->whereKey($record->id)
            ->where('verifikasi_status', 'belum')
            ->update([
                'verifikasi_status' => $validated['verifikasi_status'],
                'catatan_guru' => $validated['catatan_guru'] ?? null,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]));

        if ($updatedCount === 0) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Logbook tersebut sudah diproses sebelumnya.');
        }

        return redirect()->route('guru.dashboard')
            ->with('success', 'Logbook berhasil diverifikasi.');
    }

    /** @return Collection<int, PKLSiswa> */
    private function teacherPlacements(User $teacher): Collection
    {
        return $teacher->placementsAsTeacher()
            ->with(['siswa.siswaProfile', 'industri.industriProfile', 'penilaian', 'sertifikat'])
            ->withCount([
                'logbooks',
                'logbooks as pending_logbooks_count' => fn (Builder $query) => $query->where('verifikasi_status', 'belum'),
            ])
            ->latest()
            ->get();
    }

    /** @param array{q?: string|null, tanggal?: string|null, kelas?: string|null, status?: string|null} $filters */
    private function filteredLogbooks(User $teacher, array $filters): Builder
    {
        return $this->teacherLogbooks($teacher)
            ->when(filled($filters['q'] ?? null), function (Builder $query) use ($filters): void {
                $search = $filters['q'];
                $query->whereHas('pklSiswa.siswa', fn (Builder $studentQuery) => $studentQuery
                    ->where(function (Builder $nameQuery) use ($search): void {
                        $nameQuery->where('name', 'like', "%{$search}%")
                            ->orWhereHas('siswaProfile', fn (Builder $profileQuery) => $profileQuery->where('nama_lengkap', 'like', "%{$search}%"));
                    }));
            })
            ->when(filled($filters['tanggal'] ?? null), fn (Builder $query) => $query->whereDate('tanggal', $filters['tanggal']))
            ->when(filled($filters['kelas'] ?? null), fn (Builder $query) => $query->whereHas('pklSiswa.siswa.siswaProfile', fn (Builder $profileQuery) => $profileQuery->where('kelas', $filters['kelas'])))
            ->when(($filters['status'] ?? 'semua') !== 'semua', fn (Builder $query) => $query->where('verifikasi_status', $filters['status']));
    }

    private function teacherLogbooks(User $teacher): Builder
    {
        return LogbookHarian::query()
            ->whereHas('pklSiswa', fn (Builder $query) => $query->where('guru_id', $teacher->id));
    }
}
