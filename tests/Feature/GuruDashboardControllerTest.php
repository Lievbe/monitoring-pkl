<?php

use App\Models\PKLSiswa;
use App\Models\User;

it('bulk approves only matching pending logbooks assigned to the authenticated teacher', function () {
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $otherTeacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $students = User::factory()->count(3)->create(['role' => 'siswa_pkl']);
    $approvalDate = today()->subDay()->toDateString();
    $placementAttributes = [
        'industri_id' => $industry->id,
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->addMonth()->toDateString(),
        'status' => 'aktif',
    ];
    $assignedPlacement = PKLSiswa::query()->create([
        ...$placementAttributes,
        'siswa_id' => $students[0]->id,
        'guru_id' => $teacher->id,
    ]);
    $otherStudentPlacement = PKLSiswa::query()->create([
        ...$placementAttributes,
        'siswa_id' => $students[1]->id,
        'guru_id' => $teacher->id,
    ]);
    $unassignedPlacement = PKLSiswa::query()->create([
        ...$placementAttributes,
        'siswa_id' => $students[2]->id,
        'guru_id' => $otherTeacher->id,
    ]);
    $pendingOnDate = $assignedPlacement->logbooks()->create([
        'tanggal' => $approvalDate,
        'kegiatan' => 'Laporan pending pada tanggal terpilih',
        'status_kehadiran' => 'hadir',
    ]);
    $pendingAnotherDate = $otherStudentPlacement->logbooks()->create([
        'tanggal' => today()->toDateString(),
        'kegiatan' => 'Laporan pending tanggal lain',
        'status_kehadiran' => 'hadir',
    ]);
    $approved = $assignedPlacement->logbooks()->create([
        'tanggal' => $approvalDate,
        'kegiatan' => 'Laporan yang sudah disetujui',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'disetujui',
        'verified_by' => $teacher->id,
        'verified_at' => now()->subHour(),
    ]);
    $rejected = $assignedPlacement->logbooks()->create([
        'tanggal' => $approvalDate,
        'kegiatan' => 'Laporan yang ditolak',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'ditolak',
    ]);
    $unassignedPending = $unassignedPlacement->logbooks()->create([
        'tanggal' => $approvalDate,
        'kegiatan' => 'Laporan siswa guru lain',
        'status_kehadiran' => 'hadir',
    ]);

    $this->actingAs($teacher)
        ->post(route('guru.logbooks.approve-all'), ['tanggal' => $approvalDate, 'status' => 'semua'])
        ->assertRedirect(route('guru.logbooks', ['tanggal' => $approvalDate, 'status' => 'semua']))
        ->assertSessionHas('success', '1 logbook berhasil disetujui.');

    $this->assertDatabaseHas('logbook_harian', [
        'id' => $pendingOnDate->id,
        'verifikasi_status' => 'disetujui',
        'verified_by' => $teacher->id,
    ]);
    expect($pendingOnDate->fresh()->verified_at)->not->toBeNull();
    $this->assertDatabaseHas('logbook_harian', ['id' => $pendingAnotherDate->id, 'verifikasi_status' => 'belum']);
    $this->assertDatabaseHas('logbook_harian', ['id' => $approved->id, 'verifikasi_status' => 'disetujui']);
    $this->assertDatabaseHas('logbook_harian', ['id' => $rejected->id, 'verifikasi_status' => 'ditolak']);
    $this->assertDatabaseHas('logbook_harian', ['id' => $unassignedPending->id, 'verifikasi_status' => 'belum']);
});

it('filters teacher logbooks by student name, date, class, and verification status', function () {
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $student = User::factory()->create(['role' => 'siswa_pkl', 'name' => 'Ayu Pratama']);
    $student->siswaProfile()->create([
        'nama_lengkap' => 'Ayu Pratama',
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII RPL 1',
    ]);
    $otherStudent = User::factory()->create(['role' => 'siswa_pkl', 'name' => 'Bima Saputra']);
    $otherStudent->siswaProfile()->create([
        'nama_lengkap' => 'Bima Saputra',
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII RPL 2',
    ]);
    $placementAttributes = [
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->addMonth()->toDateString(),
        'status' => 'aktif',
    ];
    $placement = PKLSiswa::query()->create([...$placementAttributes, 'siswa_id' => $student->id]);
    $otherPlacement = PKLSiswa::query()->create([...$placementAttributes, 'siswa_id' => $otherStudent->id]);
    $selectedDate = today()->subDay()->toDateString();
    $placement->logbooks()->create([
        'tanggal' => $selectedDate,
        'kegiatan' => 'Membuat komponen laporan',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'belum',
    ]);
    $placement->logbooks()->create([
        'tanggal' => $selectedDate,
        'kegiatan' => 'Laporan yang telah disetujui',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'disetujui',
    ]);
    $otherPlacement->logbooks()->create([
        'tanggal' => $selectedDate,
        'kegiatan' => 'Catatan kelas lain',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'belum',
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.logbooks', [
            'q' => 'Ayu',
            'tanggal' => $selectedDate,
            'kelas' => 'XII RPL 1',
            'status' => 'belum',
        ]))
        ->assertOk()
        ->assertSee('Membuat komponen laporan')
        ->assertSee('1 menunggu persetujuan')
        ->assertDontSee('Laporan yang telah disetujui')
        ->assertDontSee('Catatan kelas lain');
});

it('rejects bulk approval from students and keeps their pending logbooks unchanged', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    $logbook = $placement->logbooks()->create([
        'tanggal' => today()->toDateString(),
        'kegiatan' => 'Laporan menunggu persetujuan',
        'status_kehadiran' => 'hadir',
    ]);

    $this->actingAs($student)
        ->post(route('guru.logbooks.approve-all'))
        ->assertForbidden();

    $this->assertDatabaseHas('logbook_harian', [
        'id' => $logbook->id,
        'verifikasi_status' => 'belum',
        'verified_by' => null,
    ]);
});

it('does not let a teacher reprocess an approved or rejected logbook', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    $approved = $placement->logbooks()->create([
        'tanggal' => today()->subDay()->toDateString(),
        'kegiatan' => 'Catatan yang sudah disetujui',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'disetujui',
        'verified_by' => $teacher->id,
        'verified_at' => now()->subHour(),
    ]);
    $rejected = $placement->logbooks()->create([
        'tanggal' => today()->subDays(2)->toDateString(),
        'kegiatan' => 'Catatan yang sudah ditolak',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'ditolak',
        'verified_by' => $teacher->id,
        'verified_at' => now()->subHour(),
    ]);

    $this->actingAs($teacher)
        ->put(route('guru.logbook.verifikasi', $approved), ['verifikasi_status' => 'ditolak'])
        ->assertNotFound();

    $this->put(route('guru.logbook.verifikasi', $rejected), ['verifikasi_status' => 'disetujui'])
        ->assertNotFound();

    $this->assertDatabaseHas('logbook_harian', ['id' => $approved->id, 'verifikasi_status' => 'disetujui']);
    $this->assertDatabaseHas('logbook_harian', ['id' => $rejected->id, 'verifikasi_status' => 'ditolak']);
});
