<?php

namespace App\Http\Controllers;

use App\Models\PenilaianSiswa;
use App\Models\PKLSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    public function create(Request $request, PKLSiswa $pklSiswa): View
    {
        abort_unless($pklSiswa->guru_id === $request->user()->id, 404);

        $pklSiswa->load(['siswa.siswaProfile', 'penilaian']);

        return view('guru.penilaian.create', compact('pklSiswa'));
    }

    public function store(Request $request, PKLSiswa $pklSiswa): RedirectResponse
    {
        abort_unless($pklSiswa->guru_id === $request->user()->id, 404);

        $validated = $request->validate([
            'hard_skill' => ['required', 'integer', 'between:0,100'],
            'soft_skill' => ['required', 'integer', 'between:0,100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'tanggal_penilaian' => ['required', 'date'],
        ]);

        $hard = (int) $validated['hard_skill'];
        $soft = (int) $validated['soft_skill'];
        $nilaiAkhir = round(($hard * 0.6) + ($soft * 0.4));
        $predikat = $this->hitungPredikat($nilaiAkhir);

        PenilaianSiswa::updateOrCreate(
            ['pkl_siswa_id' => $pklSiswa->id],
            [
                'guru_id' => $request->user()->id,
                'hard_skill' => $hard,
                'soft_skill' => $soft,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
                'catatan' => $validated['catatan'] ?? null,
                'tanggal_penilaian' => $validated['tanggal_penilaian'],
            ]
        );

        return redirect()->route('guru.dashboard')
            ->with('success', 'Nilai siswa berhasil disimpan.');
    }

    private function hitungPredikat(int $nilai): string
    {
        if ($nilai >= 85) {
            return 'A';
        }

        if ($nilai >= 75) {
            return 'B';
        }

        if ($nilai >= 65) {
            return 'C';
        }

        if ($nilai >= 55) {
            return 'D';
        }

        return 'E';
    }
}
