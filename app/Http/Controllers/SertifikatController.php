<?php

namespace App\Http\Controllers;

use App\Models\PKLSiswa;
use App\Models\SchoolSetting;
use App\Models\Sertifikat;
use App\Services\CertificateLogoArchiver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SertifikatController extends Controller
{
    public function generate(PKLSiswa $pklSiswa, CertificateLogoArchiver $archiver): RedirectResponse
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

        $industryProfile = $pklSiswa->industri?->industriProfile;
        $archiver->capture(
            $sertifikat,
            SchoolSetting::current(),
            $industryProfile?->logo_path,
            $industryProfile?->nama_perusahaan ?: $pklSiswa->nama_perusahaan,
            $industryProfile?->nama_pembimbing ?: $pklSiswa->nama_pembimbing,
        );

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

        $schoolSettings = SchoolSetting::current();

        $isAdmin = $user->role === 'admin_hubin';

        return view('sertifikat.cetak', compact('pklSiswa', 'sertifikat', 'schoolSettings', 'isAdmin'));
    }

    public function destroy(PKLSiswa $pklSiswa): RedirectResponse
    {
        $certificate = $pklSiswa->sertifikat()->first();
        if (! $certificate) {
            return redirect()->route('admin.dashboard')->with('error', 'Sertifikat pada penempatan ini tidak ditemukan.');
        }

        $certificateId = $certificate->id;
        $paths = array_filter([$certificate->file_path, $certificate->qr_code_path]);
        DB::transaction(fn () => $certificate->delete());

        $publicDisk = Storage::disk('public');
        foreach ($paths as $path) {
            $isStillUsed = Sertifikat::query()
                ->where(fn ($query) => $query->where('file_path', $path)->orWhere('qr_code_path', $path))
                ->exists();
            if (! $isStillUsed) {
                $publicDisk->delete($path);
            }
        }
        $publicDisk->deleteDirectory("sertifikat/arsip/{$certificateId}");

        return redirect()->route('admin.dashboard')->with('success', 'Sertifikat berhasil dihapus. Periksa logo dan identitas sebelum menerbitkan ulang.');
    }
}
