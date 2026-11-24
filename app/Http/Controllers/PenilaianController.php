<?php

namespace App\Http\Controllers;

use App\Models\PenilaianSiswa;
use App\Models\PKLSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    /**
     * Indikator penilaian sikap (soft skill) - referensi umum rubrik PKL SMK.
     */
    public const SIKAP_INDICATORS = [
        'disiplin' => 'Disiplin & Kehadiran (tepat waktu, menaati aturan, kehadiran)',
        'tanggung_jawab' => 'Tanggung Jawab (menyelesaikan tugas, menjaga alat/lingkungan)',
        'komunikasi' => 'Komunikasi & Kerja Sama (sopan, mampu bekerja dalam tim)',
        'etika' => 'Etika & Sikap Kerja (jujur, profesional, inisiatif, motivasi)',
    ];

    /**
     * Indikator penilaian kompetensi (hard skill/teknis) - referensi umum rubrik PKL SMK.
     */
    public const KOMPETENSI_INDICATORS = [
        'penguasaan' => 'Penguasaan Teknis & Tugas (memahami dan melaksanakan pekerjaan sesuai bidang)',
        'kualitas' => 'Kualitas Hasil Kerja (ketelitian, hasil sesuai standar/tugas)',
        'prosedur' => 'Penerapan Prosedur & K3 (mengikuti SOP, kerapihan, keselamatan kerja)',
        'kemandirian' => 'Kemandirian & Efisiensi (inisiatif, kecepatan, memecahkan masalah)',
    ];

    public function create(Request $request, PKLSiswa $pklSiswa): View
    {
        abort_unless($pklSiswa->guru_id === $request->user()->id, 404);

        $pklSiswa->load(['siswa.siswaProfile', 'penilaian']);

        return view('guru.penilaian.create', [
            'pklSiswa' => $pklSiswa,
            'sikapIndicators' => self::SIKAP_INDICATORS,
            'kompetensiIndicators' => self::KOMPETENSI_INDICATORS,
        ]);
    }

    public function store(Request $request, PKLSiswa $pklSiswa): RedirectResponse
    {
        abort_unless($pklSiswa->guru_id === $request->user()->id, 404);

        $sikapKeys = implode(',', array_keys(self::SIKAP_INDICATORS));
        $kompetensiKeys = implode(',', array_keys(self::KOMPETENSI_INDICATORS));

        $rules = [
            'catatan' => ['nullable', 'string', 'max:2000'],
            'tanggal_penilaian' => ['required', 'date'],
        ];
        foreach (array_keys(self::SIKAP_INDICATORS) as $key) {
            $rules["sikap.{$key}"] = ['required', 'integer', 'between:0,100'];
        }
        foreach (array_keys(self::KOMPETENSI_INDICATORS) as $key) {
            $rules["kompetensi.{$key}"] = ['required', 'integer', 'between:0,100'];
        }

        $validated = $request->validate($rules, [
            'sikap.*.required' => 'Semua indikator sikap wajib diisi.',
            'kompetensi.*.required' => 'Semua indikator kompetensi wajib diisi.',
        ]);

        $sikapValues = array_map('intval', array_values($validated['sikap']));
        $kompetensiValues = array_map('intval', array_values($validated['kompetensi']));

        $nilaiSikap = (int) round(array_sum($sikapValues) / max(1, count($sikapValues)));
        $nilaiKompetensi = (int) round(array_sum($kompetensiValues) / max(1, count($kompetensiValues)));
        $nilaiAkhir = (int) round(($nilaiKompetensi * 0.6) + ($nilaiSikap * 0.4));
        $predikat = $this->hitungPredikat($nilaiAkhir);

        PenilaianSiswa::updateOrCreate(
            ['pkl_siswa_id' => $pklSiswa->id],
            [
                'guru_id' => $request->user()->id,
                'hard_skill' => $nilaiKompetensi,
                'soft_skill' => $nilaiSikap,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
                'catatan' => $validated['catatan'] ?? null,
                'tanggal_penilaian' => $validated['tanggal_penilaian'],
            ]
        );

        return redirect()->route('guru.dashboard')
            ->with('success', 'Nilai siswa berhasil disimpan (Sikap & Kompetensi).');
    }

    public function destroy(Request $request, PKLSiswa $pklSiswa): RedirectResponse
    {
        abort_unless((int) $pklSiswa->guru_id === (int) $request->user()->id, 404);

        if ($pklSiswa->sertifikat()->exists()) {
            return redirect()->route('guru.assessments')
                ->with('error', 'Nilai tidak dapat dihapus karena sertifikat siswa sudah diterbitkan.');
        }

        $deleted = $pklSiswa->penilaian()->where('guru_id', $request->user()->id)->delete();

        return redirect()->route('guru.assessments')
            ->with($deleted ? 'success' : 'error', $deleted ? 'Penilaian berhasil dihapus.' : 'Penilaian tidak ditemukan atau bukan milik Anda.');
    }

    public static function labelPredikat(?string $predikat): string
    {
        return match ($predikat) {
            'A' => 'A (Sangat Baik)',
            'B' => 'B (Baik)',
            'C' => 'C (Cukup)',
            'D' => 'D (Kurang)',
            'E' => 'E (Sangat Kurang)',
            default => $predikat ?? '-',
        };
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
