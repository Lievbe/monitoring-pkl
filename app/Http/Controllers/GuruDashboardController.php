<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $guru = $request->user();
        $placements = $guru->placementsAsTeacher()
            ->with(['siswa.siswaProfile', 'industri.industriProfile', 'penilaian'])
            ->withCount('logbooks')
            ->latest()
            ->get();

        $logbooks = LogbookHarian::query()
            ->whereHas('pklSiswa', fn (Builder $query) => $query->where('guru_id', $guru->id))
            ->with(['pklSiswa.siswa.siswaProfile'])
            ->where('verifikasi_status', 'belum')
            ->latest('tanggal')
            ->paginate(10);

        $stats = [
            'placements' => $placements->whereIn('status', ['aktif', 'selesai'])->count(),
            'pending' => LogbookHarian::query()
                ->whereHas('pklSiswa', fn (Builder $query) => $query->where('guru_id', $guru->id))
                ->where('verifikasi_status', 'belum')
                ->count(),
            'approved' => LogbookHarian::query()
                ->whereHas('pklSiswa', fn (Builder $query) => $query->where('guru_id', $guru->id))
                ->where('verifikasi_status', 'disetujui')
                ->count(),
        ];

        return view('guru.dashboard', compact('placements', 'logbooks', 'stats'));
    }

    public function verifikasi(Request $request, int $logbook): RedirectResponse
    {
        $record = LogbookHarian::query()
            ->whereHas('pklSiswa', fn (Builder $query) => $query->where('guru_id', $request->user()->id))
            ->findOrFail($logbook);

        $validated = $request->validate([
            'verifikasi_status' => ['required', 'in:disetujui,ditolak'],
            'catatan_guru' => ['nullable', 'string', 'max:2000'],
        ]);

        $record->update([
            'verifikasi_status' => $validated['verifikasi_status'],
            'catatan_guru' => $validated['catatan_guru'] ?? null,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()->route('guru.dashboard')
            ->with('success', 'Logbook berhasil diverifikasi.');
    }
}
