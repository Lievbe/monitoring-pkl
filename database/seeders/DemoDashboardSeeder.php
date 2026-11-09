<?php

namespace Database\Seeders;

use App\Models\GuruProfile;
use App\Models\IndustriProfile;
use App\Models\LogbookHarian;
use App\Models\PenilaianSiswa;
use App\Models\PKLSiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDashboardSeeder extends Seeder
{
    private const PASSWORD = 'DemoPKL2026!';

    public function run(): void
    {
        $student = User::query()->updateOrCreate(
            ['email' => 'demo.siswa@example.test'],
            [
                'name' => 'Nadia Putri Demo',
                'password' => Hash::make(self::PASSWORD),
                'role' => 'siswa_pkl',
            ],
        );

        $student->siswaProfile()->updateOrCreate([], [
            'nama_lengkap' => $student->name,
            'jurusan' => 'Rekayasa Perangkat Lunak',
            'kelas' => 'XII RPL 1',
            'nis' => 'DEMO001',
            'nisn' => 'DEMO0001',
        ]);

        $teacher = User::query()->updateOrCreate(
            ['email' => 'demo.guru@example.test'],
            [
                'name' => 'Rifi Ahmad Demo',
                'password' => Hash::make(self::PASSWORD),
                'role' => 'guru_pembimbing',
            ],
        );

        GuruProfile::query()->updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'nama_lengkap' => $teacher->name,
                'nip' => 'DEMO-GURU-001',
                'mapel' => 'Produktif RPL',
            ],
        );

        $industry = User::query()->updateOrCreate(
            ['email' => 'demo.mitra@example.test'],
            [
                'name' => 'Zainul Arif Demo',
                'password' => Hash::make(self::PASSWORD),
                'role' => 'pembimbing_industri',
            ],
        );

        IndustriProfile::query()->updateOrCreate(
            ['user_id' => $industry->id],
            [
                'nama_perusahaan' => 'PT Cyber Jaya Demo',
                'nama_pembimbing' => $industry->name,
                'jabatan' => 'Supervisor IT',
                'alamat' => 'Jakarta',
            ],
        );

        $placement = PKLSiswa::query()->updateOrCreate(
            ['siswa_id' => $student->id],
            [
                'guru_id' => $teacher->id,
                'industri_id' => $industry->id,
                'guru_nama' => $teacher->name,
                'guru_nip' => 'DEMO-GURU-001',
                'nama_perusahaan' => 'PT Cyber Jaya Demo',
                'nama_pembimbing' => $industry->name,
                'tanggal_mulai' => now()->subMonth()->toDateString(),
                'tanggal_selesai' => now()->addMonths(2)->toDateString(),
                'status' => 'aktif',
                'catatan' => 'Data demo dashboard',
            ],
        );

        LogbookHarian::query()->updateOrCreate(
            [
                'pkl_siswa_id' => $placement->id,
                'kegiatan' => 'Membuat tampilan halaman dashboard monitoring',
            ],
            [
                'tanggal' => now()->subDay()->toDateString(),
                'status_kehadiran' => 'hadir',
                'jam_masuk' => '08:00',
                'jam_keluar' => '16:00',
                'verifikasi_status' => 'belum',
            ],
        );

        LogbookHarian::query()->updateOrCreate(
            [
                'pkl_siswa_id' => $placement->id,
                'kegiatan' => 'Mempelajari alur kerja tim pengembangan aplikasi',
            ],
            [
                'tanggal' => now()->subDays(2)->toDateString(),
                'status_kehadiran' => 'hadir',
                'jam_masuk' => '08:00',
                'jam_keluar' => '16:00',
                'verifikasi_status' => 'disetujui',
                'catatan_guru' => 'Bagus, lanjutkan dokumentasi kegiatan.',
                'verified_by' => $teacher->id,
                'verified_at' => now()->subDays(2),
            ],
        );

        PenilaianSiswa::query()->updateOrCreate(
            ['pkl_siswa_id' => $placement->id],
            [
                'guru_id' => $teacher->id,
                'hard_skill' => 85,
                'soft_skill' => 90,
                'nilai_akhir' => 87,
                'predikat' => 'A',
                'catatan' => 'Menunjukkan inisiatif dan kemampuan kerja sama yang baik.',
                'tanggal_penilaian' => now()->toDateString(),
            ],
        );
    }
}
