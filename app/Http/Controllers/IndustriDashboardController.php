<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndustriDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $placements = $request->user()->placementsAsIndustry()
            ->with(['siswa.siswaProfile', 'guru.guruProfile'])
            ->withCount([
                'logbooks',
                'logbooks as pending_logbooks_count' => fn ($query) => $query
                    ->where('verifikasi_status', 'belum'),
            ])
            ->latest()
            ->get();

        $logbooks = LogbookHarian::query()
            ->whereHas('pklSiswa', fn (Builder $query) => $query->where('industri_id', $request->user()->id))
            ->with(['pklSiswa.siswa.siswaProfile'])
            ->latest('tanggal')
            ->paginate(12);

        return view('industri.dashboard', [
            'placements' => $placements,
            'logbooks' => $logbooks,
            'companyName' => $request->user()->industriProfile?->nama_perusahaan,
            'stats' => [
                'students' => $placements->whereIn('status', ['aktif', 'selesai'])->count(),
                'active' => $placements->where('status', 'aktif')->count(),
                'pending_logbooks' => $placements->sum('pending_logbooks_count'),
            ],
        ]);
    }
}
