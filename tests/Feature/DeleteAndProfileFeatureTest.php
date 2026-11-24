<?php

use App\Models\IndustriProfile;
use App\Models\PKLSiswa;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makePlacement(User $student, User $teacher, ?User $industry = null, string $status = 'aktif'): PKLSiswa
{
    return PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $industry?->id,
        'nama_perusahaan' => 'Mitra Uji',
        'nama_pembimbing' => 'Pembimbing Uji',
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->addMonth()->toDateString(),
        'status' => $status,
    ]);
}

it('lets admins delete only placements without logbooks, grades, or certificates', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = makePlacement($student, $teacher);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('data-delete-trigger', false)
        ->assertSee('delete-confirmation-modal', false)
        ->assertSee('Hapus Data?');

    $this->actingAs($admin)->delete(route('admin.placements.destroy', $placement))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('success');
    $this->assertDatabaseMissing('pkl_siswa', ['id' => $placement->id]);

    $protectedPlacement = makePlacement($student, $teacher, null, 'aktif');
    $protectedPlacement->logbooks()->create([
        'tanggal' => today()->subDay()->toDateString(),
        'kegiatan' => 'Riwayat penting',
        'status_kehadiran' => 'hadir',
    ]);
    $this->actingAs($admin)->delete(route('admin.placements.destroy', $protectedPlacement))
        ->assertSessionHas('error');
    $this->assertDatabaseHas('pkl_siswa', ['id' => $protectedPlacement->id]);

    $this->actingAs($teacher)->delete(route('admin.placements.destroy', $protectedPlacement))
        ->assertForbidden();
});

it('allows students to remove only their own unapproved logbooks', function () {
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $placement = makePlacement($student, $teacher);
    $pending = $placement->logbooks()->create([
        'tanggal' => today()->subDay()->toDateString(),
        'kegiatan' => 'Catatan pending',
        'status_kehadiran' => 'hadir',
    ]);

    $this->actingAs($student)->delete(route('siswa.logbook.destroy', $pending->id))
        ->assertRedirect(route('siswa.history'))
        ->assertSessionHas('success');
    $this->assertDatabaseMissing('logbook_harian', ['id' => $pending->id]);

    $approved = $placement->logbooks()->create([
        'tanggal' => today()->subDays(2)->toDateString(),
        'kegiatan' => 'Catatan disetujui',
        'status_kehadiran' => 'hadir',
        'verifikasi_status' => 'disetujui',
    ]);
    $this->actingAs($student)->delete(route('siswa.logbook.destroy', $approved->id))->assertNotFound();

    $otherStudent = User::factory()->create(['role' => 'siswa_pkl']);
    $this->actingAs($otherStudent)->delete(route('siswa.logbook.destroy', $approved->id))->assertNotFound();
});

it('lets only the assigned teacher delete grades before certificate issue', function () {
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $otherTeacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $placement = makePlacement($student, $teacher);
    $placement->penilaian()->create([
        'guru_id' => $teacher->id,
        'hard_skill' => 80,
        'soft_skill' => 90,
        'nilai_akhir' => 84,
        'predikat' => 'B',
        'tanggal_penilaian' => today()->toDateString(),
    ]);

    $this->actingAs($otherTeacher)->delete(route('guru.penilaian.destroy', $placement))->assertNotFound();
    $this->actingAs($teacher)->delete(route('guru.penilaian.destroy', $placement))
        ->assertRedirect(route('guru.assessments'))
        ->assertSessionHas('success');
    $this->assertDatabaseMissing('penilaian_siswa', ['pkl_siswa_id' => $placement->id]);

    $placement->penilaian()->create([
        'guru_id' => $teacher->id,
        'hard_skill' => 80,
        'soft_skill' => 90,
        'nilai_akhir' => 84,
        'predikat' => 'B',
        'tanggal_penilaian' => today()->toDateString(),
    ]);
    Sertifikat::query()->create([
        'pkl_siswa_id' => $placement->id,
        'nomor_sertifikat' => 'PKL-TEST-1',
        'tanggal_terbit' => today()->toDateString(),
        'file_path' => null,
        'verifikasi_url' => '',
        'is_valid' => true,
    ]);
    $this->actingAs($teacher)->delete(route('guru.penilaian.destroy', $placement))
        ->assertSessionHas('error');
    $this->assertDatabaseHas('penilaian_siswa', ['pkl_siswa_id' => $placement->id]);
});

it('shows role information and lets a user replace only their own profile photo', function () {
    Storage::fake('public');
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    IndustriProfile::query()->create([
        'user_id' => $industry->id,
        'nama_perusahaan' => 'Perusahaan Uji',
        'nama_pembimbing' => 'Nama Pembimbing',
    ]);

    $this->actingAs($industry)->get(route('profile.edit'))
        ->assertOk()
        ->assertSee($industry->email)
        ->assertSee('Pembimbing Industri')
        ->assertSee('Perusahaan Uji')
        ->assertSee('Keamanan akun')
        ->assertSee('data-account-menu', false)
        ->assertSee('delete-confirmation-modal', false);

    $this->actingAs($industry)->patch(route('profile.update'), [
        'name' => 'Nama Akun Baru',
        'email' => $industry->email,
        'avatar' => UploadedFile::fake()->image('avatar.webp'),
    ])->assertSessionHasNoErrors()->assertSessionHas('status', 'profile-updated');

    expect($industry->fresh()->avatar_path)->toStartWith('profile-photos/');
    Storage::disk('public')->assertExists($industry->fresh()->avatar_path);

    $this->actingAs($industry)->put(route('password.update'), [
        'current_password' => 'password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertSessionHasNoErrors()->assertSessionHas('status', 'password-updated');

    $this->actingAs($industry)->post(route('logout'))->assertRedirect(route('home'));
});

it('blocks deletion of admin accounts and accounts tied to placement history', function () {
    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $this->actingAs($admin)->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrorsIn('userDeletion', 'password');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);

    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    makePlacement($student, $teacher);
    $this->actingAs($student)->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertSessionHasErrorsIn('userDeletion', 'password');
    $this->assertDatabaseHas('users', ['id' => $student->id]);
});
