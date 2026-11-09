<?php

use App\Models\IndustriProfile;
use App\Models\PKLSiswa;
use App\Models\Sertifikat;
use App\Models\User;
use Database\Seeders\DemoDashboardSeeder;

it('redirects each authenticated role to a working role-specific dashboard', function (string $role, string $dashboard, string $heading) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route($dashboard));

    $this->get(route($dashboard))
        ->assertOk()
        ->assertSee($heading);
})->with([
    'student' => ['siswa_pkl', 'siswa.dashboard', 'Dashboard PKL'],
    'teacher' => ['guru_pembimbing', 'guru.dashboard', 'Dashboard bimbingan PKL'],
    'industry mentor' => ['pembimbing_industri', 'industri.dashboard', 'Pemantauan siswa PKL'],
    'admin' => ['admin_hubin', 'admin.dashboard', 'Monitoring Hub'],
]);

it('prevents a student from opening the teacher dashboard', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);

    $this->actingAs($student)
        ->get(route('guru.dashboard'))
        ->assertForbidden();
});

it('shows monitoring data and quick actions without inline data entry forms', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Monitoring Hub')
        ->assertSee('Siswa terdaftar')
        ->assertSee('PKL berjalan')
        ->assertSee('PKL selesai')
        ->assertSee('Sertifikat terbit')
        ->assertSee('Aksi Cepat')
        ->assertSee(route('admin.students.create'), false)
        ->assertSee(route('admin.placements.create'), false)
        ->assertDontSee('name="guru_nama"')
        ->assertDontSee('name="nama_perusahaan"')
        ->assertDontSee('name="guru_id"')
        ->assertDontSee('name="industri_id"');
});

it('keeps student registration and placement forms on their dedicated admin pages', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    User::factory()->create(['role' => 'siswa_pkl']);

    $this->actingAs($admin)
        ->get(route('admin.students.create'))
        ->assertOk()
        ->assertSee('Daftarkan Siswa')
        ->assertSee('name="email"', false);

    $this->get(route('admin.placements.create'))
        ->assertOk()
        ->assertSee('Penempatan PKL')
        ->assertSee('name="guru_nama"', false)
        ->assertSee('name="nama_perusahaan"', false);
});

it('prevents students from opening admin quick action pages', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);

    $this->actingAs($student)
        ->get(route('admin.students.create'))
        ->assertForbidden();

    $this->get(route('admin.placements.create'))
        ->assertForbidden();
});

it('prevents a student from creating privileged accounts', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);

    $this->actingAs($student)
        ->post(route('admin.users.store'), [
            'name' => 'Admin Palsu',
            'email' => 'fake-admin@example.com',
            'role' => 'admin_hubin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'fake-admin@example.com']);
});

it('does not let the admin account manager create another admin account', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);

    $this->actingAs($admin)
        ->from(route('admin.dashboard'))
        ->post(route('admin.users.store'), [
            'name' => 'Admin Kedua',
            'email' => 'second-admin@example.com',
            'role' => 'admin_hubin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('users', ['email' => 'second-admin@example.com']);
});

it('lets a student submit a logbook for their active placement', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);

    $student->siswaProfile()->create([
        'nama_lengkap' => $student->name,
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII RPL 1',
    ]);

    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $response = $this->actingAs($student)->post(route('siswa.logbook.store'), [
        'tanggal' => now()->toDateString(),
        'kegiatan' => 'Membuat halaman laporan harian',
        'status_kehadiran' => 'hadir',
        'jam_masuk' => '08:00',
        'jam_keluar' => '16:00',
    ]);

    $response->assertRedirect(route('siswa.dashboard'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('logbook_harian', [
        'pkl_siswa_id' => $placement->id,
        'kegiatan' => 'Membuat halaman laporan harian',
        'verifikasi_status' => 'belum',
    ]);
});

it('does not let a student submit a logbook before receiving an active placement', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);

    $response = $this->actingAs($student)->from(route('siswa.dashboard'))
        ->post(route('siswa.logbook.store'), [
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Kegiatan tanpa penempatan',
            'status_kehadiran' => 'hadir',
        ]);

    $response->assertRedirect(route('siswa.dashboard'))
        ->assertSessionHas('error');
    $this->assertDatabaseCount('logbook_harian', 0);
});

it('lets the assigned teacher verify a student logbook', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    $logbook = $placement->logbooks()->create([
        'tanggal' => now()->toDateString(),
        'kegiatan' => 'Menyelesaikan modul data',
        'status_kehadiran' => 'hadir',
    ]);

    $response = $this->actingAs($teacher)->put(route('guru.logbook.verifikasi', $logbook), [
        'verifikasi_status' => 'disetujui',
        'catatan_guru' => 'Pekerjaan sudah sesuai.',
    ]);

    $response->assertRedirect(route('guru.dashboard'));
    $this->assertDatabaseHas('logbook_harian', [
        'id' => $logbook->id,
        'verifikasi_status' => 'disetujui',
        'verified_by' => $teacher->id,
        'catatan_guru' => 'Pekerjaan sudah sesuai.',
    ]);
});

it('does not let a teacher verify a logbook assigned to another teacher', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $assignedTeacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $otherTeacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $assignedTeacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    $logbook = $placement->logbooks()->create([
        'tanggal' => now()->toDateString(),
        'kegiatan' => 'Aktivitas privat siswa',
        'status_kehadiran' => 'hadir',
    ]);

    $this->actingAs($otherTeacher)
        ->put(route('guru.logbook.verifikasi', $logbook), [
            'verifikasi_status' => 'disetujui',
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('logbook_harian', [
        'id' => $logbook->id,
        'verifikasi_status' => 'belum',
        'verified_by' => null,
    ]);
});

it('lets the assigned teacher save the calculated assessment', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.penilaian.create', $placement))
        ->assertOk()
        ->assertSee($student->name);

    $response = $this->actingAs($teacher)->post(route('guru.penilaian.store', $placement), [
        'hard_skill' => 80,
        'soft_skill' => 70,
        'tanggal_penilaian' => now()->toDateString(),
        'catatan' => 'Terus tingkatkan ketelitian.',
    ]);

    $response->assertRedirect(route('guru.dashboard'));
    $this->assertDatabaseHas('penilaian_siswa', [
        'pkl_siswa_id' => $placement->id,
        'guru_id' => $teacher->id,
        'nilai_akhir' => 76,
        'predikat' => 'B',
    ]);
});

it('does not let a teacher assess another teacher’s student', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $assignedTeacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $otherTeacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $assignedTeacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $this->actingAs($otherTeacher)
        ->get(route('guru.penilaian.create', $placement))
        ->assertNotFound();

    $this->actingAs($otherTeacher)
        ->post(route('guru.penilaian.store', $placement), [
            'hard_skill' => 100,
            'soft_skill' => 100,
            'tanggal_penilaian' => now()->toDateString(),
        ])
        ->assertNotFound();

    $this->assertDatabaseCount('penilaian_siswa', 0);
});

it('lets an admin create a staff account and matching profile', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Guru Pembimbing',
        'email' => 'guru@example.com',
        'role' => 'guru_pembimbing',
        'nip' => '19870001',
        'mapel' => 'Produktif RPL',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseHas('users', [
        'email' => 'guru@example.com',
        'role' => 'guru_pembimbing',
    ]);
    $this->assertDatabaseHas('guru_profiles', [
        'nama_lengkap' => 'Guru Pembimbing',
        'nip' => '19870001',
    ]);
});

it('lets an admin assign a student using typed teacher and industry details', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);

    $response = $this->actingAs($admin)->post(route('admin.placements.store'), [
        'siswa_id' => $student->id,
        'guru_nama' => 'Ibu Siti Rahmawati',
        'guru_nip' => '19870001',
        'nama_perusahaan' => 'PT Karya Bersama',
        'nama_pembimbing' => 'Bapak Andi',
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseHas('pkl_siswa', [
        'siswa_id' => $student->id,
        'guru_id' => null,
        'industri_id' => null,
        'guru_nama' => 'Ibu Siti Rahmawati',
        'guru_nip' => '19870001',
        'nama_perusahaan' => 'PT Karya Bersama',
        'nama_pembimbing' => 'Bapak Andi',
        'status' => 'aktif',
    ]);

    $this->assertDatabaseMissing('users', ['role' => 'guru_pembimbing']);
    $this->assertDatabaseMissing('users', ['role' => 'pembimbing_industri']);
});

it('lets an admin edit placement details without creating teacher or industry accounts', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_nama' => 'Guru Lama',
        'nama_perusahaan' => 'Perusahaan Lama',
        'nama_pembimbing' => 'Pembimbing Lama',
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.placements.edit', $placement))
        ->assertOk()
        ->assertSee('Edit data penempatan PKL')
        ->assertSee('Guru Lama');

    $this->put(route('admin.placements.update', $placement), [
        'siswa_id' => $student->id,
        'guru_nama' => 'Guru Baru',
        'guru_nip' => '19870002',
        'nama_perusahaan' => 'Perusahaan Baru',
        'nama_pembimbing' => 'Pembimbing Baru',
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addMonths(2)->toDateString(),
        'status' => 'aktif',
        'catatan' => 'Catatan yang diperbarui',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseHas('pkl_siswa', [
        'id' => $placement->id,
        'guru_nama' => 'Guru Baru',
        'guru_nip' => '19870002',
        'nama_perusahaan' => 'Perusahaan Baru',
        'nama_pembimbing' => 'Pembimbing Baru',
        'catatan' => 'Catatan yang diperbarui',
    ]);

    $this->assertDatabaseMissing('users', ['role' => 'guru_pembimbing']);
    $this->assertDatabaseMissing('users', ['role' => 'pembimbing_industri']);
});

it('does not let an admin edit a student into a second active placement', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_nama' => 'Guru Satu',
        'nama_perusahaan' => 'Perusahaan Satu',
        'nama_pembimbing' => 'Pembimbing Satu',
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_nama' => 'Guru Dua',
        'nama_perusahaan' => 'Perusahaan Dua',
        'nama_pembimbing' => 'Pembimbing Dua',
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.placements.edit', $placement))
        ->put(route('admin.placements.update', $placement), [
            'siswa_id' => $student->id,
            'guru_nama' => 'Guru Satu Diubah',
            'nama_perusahaan' => 'Perusahaan Satu',
            'nama_pembimbing' => 'Pembimbing Satu',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status' => 'aktif',
        ])
        ->assertRedirect(route('admin.placements.edit', $placement))
        ->assertSessionHasErrors('siswa_id');

    $this->assertDatabaseHas('pkl_siswa', [
        'id' => $placement->id,
        'guru_nama' => 'Guru Satu',
    ]);
});

it('seeds repeatable sample data for student teacher and industry dashboards', function () {
    app(DemoDashboardSeeder::class)->run();
    app(DemoDashboardSeeder::class)->run();

    $this->assertDatabaseCount('users', 3);
    $this->assertDatabaseCount('pkl_siswa', 1);
    $this->assertDatabaseCount('logbook_harian', 2);

    $student = User::query()->where('email', 'demo.siswa@example.test')->firstOrFail();
    $teacher = User::query()->where('email', 'demo.guru@example.test')->firstOrFail();
    $industry = User::query()->where('email', 'demo.mitra@example.test')->firstOrFail();

    $this->actingAs($student)
        ->get(route('siswa.dashboard'))
        ->assertOk()
        ->assertSee('PT Cyber Jaya Demo')
        ->assertSee('Rifi Ahmad Demo')
        ->assertSee('Membuat tampilan halaman dashboard monitoring');

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertOk()
        ->assertSee('PT Cyber Jaya Demo')
        ->assertSee('Nadia Putri Demo')
        ->assertSee('Membuat tampilan halaman dashboard monitoring');

    $this->actingAs($industry)
        ->get(route('industri.dashboard'))
        ->assertOk()
        ->assertSee('PT Cyber Jaya Demo')
        ->assertSee('Nadia Putri Demo')
        ->assertSee('Mempelajari alur kerja tim pengembangan aplikasi');
});

it('lets an admin issue a printable certificate for a completed placement', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $student->siswaProfile()->create([
        'nama_lengkap' => $student->name,
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII RPL 1',
    ]);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_nama' => 'Ibu Siti Rahmawati',
        'guru_nip' => '19870001',
        'nama_perusahaan' => 'PT Karya Bersama',
        'nama_pembimbing' => 'Bapak Andi',
        'tanggal_mulai' => now()->subMonths(3)->toDateString(),
        'tanggal_selesai' => now()->subWeek()->toDateString(),
        'status' => 'selesai',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.placements.sertifikat.generate', $placement));
    $sertifikat = Sertifikat::query()->where('pkl_siswa_id', $placement->id)->firstOrFail();

    $response->assertRedirect(route('sertifikat.show', $sertifikat));
    $this->assertDatabaseHas('sertifikat', [
        'pkl_siswa_id' => $placement->id,
        'nomor_sertifikat' => 'PKL-'.now()->year.'-'.str_pad((string) $placement->id, 6, '0', STR_PAD_LEFT),
        'file_path' => null,
        'is_valid' => true,
    ]);

    $this->get(route('sertifikat.show', $sertifikat))
        ->assertOk()
        ->assertSee('SERTIFIKAT')
        ->assertSee('PT Karya Bersama')
        ->assertSee('Ibu Siti Rahmawati')
        ->assertSee('Cetak / Simpan PDF');

    $this->actingAs($student)
        ->get(route('sertifikat.show', $sertifikat))
        ->assertOk()
        ->assertSee($student->name);

    $this->actingAs($admin)->post(route('admin.placements.sertifikat.generate', $placement));
    $this->assertDatabaseCount('sertifikat', 1);
});

it('requires a completed placement before issuing a certificate', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_nama' => 'Ibu Siti Rahmawati',
        'nama_perusahaan' => 'PT Karya Bersama',
        'nama_pembimbing' => 'Bapak Andi',
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.dashboard'))
        ->post(route('admin.placements.sertifikat.generate', $placement))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHasErrors('status');

    $this->assertDatabaseCount('sertifikat', 0);
});

it('lets an admin finish an active placement and make it eligible for certification', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_nama' => 'Ibu Siti Rahmawati',
        'nama_perusahaan' => 'PT Karya Bersama',
        'nama_pembimbing' => 'Bapak Andi',
        'tanggal_mulai' => now()->subMonths(3)->toDateString(),
        'tanggal_selesai' => now()->toDateString(),
        'status' => 'aktif',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.placements.complete', $placement))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('pkl_siswa', [
        'id' => $placement->id,
        'status' => 'selesai',
    ]);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Terbitkan');
});

it('does not let a student view another students certificate', function () {
    $owner = User::factory()->create(['role' => 'siswa_pkl']);
    $otherStudent = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $owner->id,
        'guru_nama' => 'Ibu Siti Rahmawati',
        'nama_perusahaan' => 'PT Karya Bersama',
        'nama_pembimbing' => 'Bapak Andi',
        'tanggal_mulai' => now()->subMonths(3)->toDateString(),
        'tanggal_selesai' => now()->subWeek()->toDateString(),
        'status' => 'selesai',
    ]);
    $sertifikat = Sertifikat::query()->create([
        'pkl_siswa_id' => $placement->id,
        'nomor_sertifikat' => 'PKL-'.now()->year.'-'.str_pad((string) $placement->id, 6, '0', STR_PAD_LEFT),
        'tanggal_terbit' => now()->toDateString(),
        'verifikasi_url' => 'https://example.test/sertifikat',
        'is_valid' => true,
    ]);

    $this->actingAs($otherStudent)
        ->get(route('sertifikat.show', $sertifikat))
        ->assertForbidden();
});

it('limits an industry dashboard to logbooks from its own placements', function () {
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $otherIndustry = User::factory()->create(['role' => 'pembimbing_industri']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $otherStudent = User::factory()->create(['role' => 'siswa_pkl']);

    IndustriProfile::query()->create([
        'user_id' => $industry->id,
        'nama_perusahaan' => 'Bengkel Utama',
        'nama_pembimbing' => 'Pembimbing Satu',
    ]);

    $ownPlacement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    $otherPlacement = PKLSiswa::query()->create([
        'siswa_id' => $otherStudent->id,
        'guru_id' => $teacher->id,
        'industri_id' => $otherIndustry->id,
        'tanggal_mulai' => now()->subWeek()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'status' => 'aktif',
    ]);
    $ownPlacement->logbooks()->create([
        'tanggal' => now()->toDateString(),
        'kegiatan' => 'Aktivitas milik perusahaan ini',
        'status_kehadiran' => 'hadir',
    ]);
    $otherPlacement->logbooks()->create([
        'tanggal' => now()->toDateString(),
        'kegiatan' => 'Aktivitas perusahaan lain',
        'status_kehadiran' => 'hadir',
    ]);

    $this->actingAs($industry)
        ->get(route('industri.dashboard'))
        ->assertOk()
        ->assertSee('Aktivitas milik perusahaan ini')
        ->assertDontSee('Aktivitas perusahaan lain');
});
