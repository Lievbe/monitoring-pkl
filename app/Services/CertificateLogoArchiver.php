<?php

namespace App\Services;

use App\Models\SchoolSetting;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CertificateLogoArchiver
{
    public function archiveIndustry(User $industry): void
    {
        Sertifikat::query()
            ->whereNull('logos_snapshot_at')
            ->whereHas('pklSiswa', fn ($query) => $query->where('industri_id', $industry->id))
            ->with('pklSiswa.industri.industriProfile')
            ->each(fn (Sertifikat $certificate) => $this->capture(
                $certificate,
                SchoolSetting::current(),
                $certificate->pklSiswa?->industri?->industriProfile?->logo_path,
                $certificate->pklSiswa?->industri?->industriProfile?->nama_perusahaan
                    ?: $certificate->pklSiswa?->nama_perusahaan,
                $certificate->pklSiswa?->industri?->industriProfile?->nama_pembimbing
                    ?: $certificate->pklSiswa?->nama_pembimbing,
            ));
    }

    public function archiveSchool(): void
    {
        Sertifikat::query()
            ->whereNull('logos_snapshot_at')
            ->with('pklSiswa.industri.industriProfile')
            ->each(fn (Sertifikat $certificate) => $this->capture(
                $certificate,
                SchoolSetting::current(),
                $certificate->pklSiswa?->industri?->industriProfile?->logo_path,
                $certificate->pklSiswa?->industri?->industriProfile?->nama_perusahaan
                    ?: $certificate->pklSiswa?->nama_perusahaan,
                $certificate->pklSiswa?->industri?->industriProfile?->nama_pembimbing
                    ?: $certificate->pklSiswa?->nama_pembimbing,
            ));
    }

    public function capture(
        Sertifikat $certificate,
        SchoolSetting $school,
        ?string $companyLogo,
        ?string $companyName,
        ?string $mentorName,
    ): void {
        if ($certificate->logos_snapshot_at) {
            return;
        }

        $disk = Storage::disk('public');
        $copyLogo = function (?string $source, string $name) use ($disk, $certificate): ?string {
            if (! $source || ! $disk->exists($source)) {
                return null;
            }

            $extension = pathinfo($source, PATHINFO_EXTENSION);
            $target = "sertifikat/arsip/{$certificate->id}/{$name}.{$extension}";
            $disk->copy($source, $target);

            return $target;
        };

        $certificate->update([
            'logo_sekolah_path' => $copyLogo($school->logo_path, 'sekolah'),
            'logo_perusahaan_path' => $copyLogo($companyLogo, 'perusahaan'),
            'nama_sekolah' => $school->nama_sekolah,
            'nama_perusahaan' => $companyName,
            'nama_pembimbing' => $mentorName,
            'logos_snapshot_at' => now(),
        ]);
    }
}
