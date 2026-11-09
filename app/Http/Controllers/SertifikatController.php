<?php

namespace App\Http\Controllers;

use App\Models\PKLSiswa;
use App\Models\Sertifikat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SertifikatController extends Controller
{
    public function generate(PKLSiswa $pklSiswa): RedirectResponse
    {
        if ($pklSiswa->status !== 'selesai') {
            throw ValidationException::withMessages([
                'status' => 'Selesaikan penempatan PKL sebelum menerbitkan sertifikat.',
            ]);
        }

        $nomorSertifikat = 'PKL-'.now()->year.'-'.str_pad((string) $pklSiswa->id, 6, '0', STR_PAD_LEFT);
        $sertifikat = Sertifikat::query()->firstOrCreate(
            ['pkl_siswa_id' => $pklSiswa->id],
            [
                'nomor_sertifikat' => $nomorSertifikat,
                'tanggal_terbit' => now()->toDateString(),
                'file_path' => null,
                'verifikasi_url' => '',
                'is_valid' => true,
            ],
        );

        $sertifikat->update([
            'verifikasi_url' => route('sertifikat.show', $sertifikat),
        ]);

        return redirect()->route('sertifikat.show', $sertifikat)
            ->with('success', 'Sertifikat berhasil dibuat. Pilih Cetak / Simpan PDF untuk mengunduhnya.');
    }

    public function show(Request $request, Sertifikat $sertifikat): View
    {
        $pklSiswa = $sertifikat->pklSiswa()
            ->with(['siswa.siswaProfile', 'guru.guruProfile', 'industri.industriProfile', 'penilaian'])
            ->firstOrFail();

        $user = $request->user();
        abort_unless(
            $user->role === 'admin_hubin'
                || ($user->role === 'siswa_pkl' && (int) $pklSiswa->siswa_id === (int) $user->id),
            403,
        );

        return view('sertifikat.cetak', compact('pklSiswa', 'sertifikat'));
    }
}
