<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $placement = $user->placementsAsStudent()
            ->with(['guru.guruProfile', 'industri.industriProfile', 'penilaian', 'sertifikat'])
            ->latest()
            ->first();

        $logbooks = $placement
            ? $placement->logbooks()->latest('tanggal')->latest('id')->paginate(10)
            : collect();

        return view('siswa.dashboard', compact('placement', 'logbooks'));
    }

    public function storeLogbook(Request $request): RedirectResponse
    {
        $placement = $request->user()->placementsAsStudent()
            ->where('status', 'aktif')
            ->latest()
            ->first();

        if ($placement === null) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akun ini belum memiliki penempatan PKL aktif. Hubungi admin sekolah.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'kegiatan' => ['required', 'string', 'max:5000'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'status_kehadiran' => ['required', 'in:hadir,sakit,izin,alfa'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i', 'after_or_equal:jam_masuk'],
        ]);

        $placement->logbooks()->create($validated);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Logbook berhasil dikirim dan menunggu verifikasi guru.');
    }
}
