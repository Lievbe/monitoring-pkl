<?php

use App\Models\PKLSiswa;
use App\Models\User;

it('renders all student portal pages for an authenticated student', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);

    $this->actingAs($student)
        ->get(route('siswa.dashboard'))
        ->assertOk()
        ->assertSee('Portal Siswa PKL');

    $this->get(route('siswa.logbook'))
        ->assertOk()
        ->assertSee('Logbook PKL');

    $this->get(route('siswa.calendar'))
        ->assertOk()
        ->assertSee('Kalender');

    $this->get(route('siswa.history'))
        ->assertOk()
        ->assertSee('Riwayat logbook');

    $this->get(route('siswa.profile'))
        ->assertOk()
        ->assertSee('Data siswa');
});

it('filters a student history to matching logbooks from their own placement', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $otherStudent = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placementAttributes = [
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subMonth()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ];
    $placement = PKLSiswa::query()->create([
        ...$placementAttributes,
        'siswa_id' => $student->id,
    ]);
    $otherPlacement = PKLSiswa::query()->create([
        ...$placementAttributes,
        'siswa_id' => $otherStudent->id,
    ]);

    $placement->logbooks()->create([
        'tanggal' => today()->toDateString(),
        'kegiatan' => 'Membuat komponen dashboard',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'belum',
    ]);
    $placement->logbooks()->create([
        'tanggal' => today()->subDay()->toDateString(),
        'kegiatan' => 'Menyusun dokumen proyek',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'disetujui',
    ]);
    $otherPlacement->logbooks()->create([
        'tanggal' => today()->toDateString(),
        'kegiatan' => 'Data pribadi siswa lain',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'belum',
    ]);

    $this->actingAs($student)
        ->get(route('siswa.history', ['status' => 'belum', 'q' => 'komponen']))
        ->assertOk()
        ->assertSee('Membuat komponen dashboard')
        ->assertDontSee('Menyusun dokumen proyek')
        ->assertDontSee('Data pribadi siswa lain');
});
