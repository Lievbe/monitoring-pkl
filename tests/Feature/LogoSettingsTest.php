<?php

use App\Models\IndustriProfile;
use App\Models\PKLSiswa;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('lets an industry user update only its own profile and logo', function () {
    Storage::fake('public');
    $company = User::factory()->create(['role' => 'pembimbing_industri']);
    $profile = IndustriProfile::query()->create([
        'user_id' => $company->id,
        'nama_perusahaan' => 'Mitra Lama',
        'nama_pembimbing' => 'Budi',
    ]);

    $this->actingAs($company)->put(route('industri.profile.update'), [
        'nama_perusahaan' => 'Mitra Baru',
        'nama_pembimbing' => 'Siti',
        'alamat' => 'Jakarta',
        'no_hp' => '0812345678',
        'logo' => UploadedFile::fake()->image('company.png'),
    ])->assertSessionHasNoErrors()->assertSessionHas('success');

    expect($profile->fresh()->nama_perusahaan)->toBe('Mitra Baru')
        ->and($profile->fresh()->logo_path)->toStartWith('logos/perusahaan/');
    Storage::disk('public')->assertExists($profile->fresh()->logo_path);
    $firstLogo = $profile->fresh()->logo_path;

    $this->actingAs($company)->put(route('industri.profile.update'), [
        'nama_perusahaan' => 'Mitra Baru',
        'nama_pembimbing' => 'Siti',
        'logo' => UploadedFile::fake()->image('company-updated.webp'),
    ])->assertSessionHasNoErrors();
    expect($profile->fresh()->logo_path)->not->toBe($firstLogo);
    Storage::disk('public')->assertMissing($firstLogo);

    $secondLogo = $profile->fresh()->logo_path;
    $this->actingAs($company)->delete(route('industri.profile.logo.destroy'))
        ->assertSessionHas('success');
    expect($profile->fresh()->logo_path)->toBeNull();
    Storage::disk('public')->assertMissing($secondLogo);

    $other = User::factory()->create(['role' => 'pembimbing_industri']);
    $this->actingAs($other)->put(route('industri.profile.update'), [
        'nama_perusahaan' => 'Mitra Lain', 'nama_pembimbing' => 'Lain',
    ])->assertForbidden();
});

it('restricts school identity changes to admins and validates logo uploads', function () {
    Storage::fake('public');
    $industry = User::factory()->create(['role' => 'pembimbing_industri']);
    $this->actingAs($industry)->get(route('admin.school-identity.edit'))->assertForbidden();

    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $this->actingAs($admin)->put(route('admin.school-identity.update'), [
        'nama_sekolah' => 'SMKN 43 Jakarta',
        'alamat' => 'Jakarta Selatan',
        'informasi' => 'NPSN sekolah',
        'logo' => UploadedFile::fake()->image('school.webp')->size(100),
    ])->assertSessionHasNoErrors()->assertSessionHas('success');

    $settings = SchoolSetting::query()->firstOrFail();
    expect($settings->nama_sekolah)->toBe('SMKN 43 Jakarta')
        ->and($settings->logo_path)->toStartWith('logos/sekolah/');
    Storage::disk('public')->assertExists($settings->logo_path);
    $firstLogo = $settings->logo_path;

    $this->actingAs($admin)->put(route('admin.school-identity.update'), [
        'nama_sekolah' => 'SMKN 43 Jakarta',
        'logo' => UploadedFile::fake()->image('school-updated.jpg'),
    ])->assertSessionHasNoErrors();
    expect($settings->fresh()->logo_path)->not->toBe($firstLogo);
    Storage::disk('public')->assertMissing($firstLogo);
    $secondLogo = $settings->fresh()->logo_path;
    $this->actingAs($admin)->delete(route('admin.school-identity.logo.destroy'))
        ->assertSessionHas('success');
    expect($settings->fresh()->logo_path)->toBeNull();
    Storage::disk('public')->assertMissing($secondLogo);

    $this->actingAs($admin)->put(route('admin.school-identity.update'), [
        'nama_sekolah' => 'SMKN 43 Jakarta',
        'logo' => UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'),
    ])->assertSessionHasErrors('logo');
});

it('snapshots the selected industry logo and school logo on each certificate', function () {
    Storage::fake('public');
    $schoolLogo = UploadedFile::fake()->image('school.png')->store('logos/sekolah', 'public');
    $companyLogo = UploadedFile::fake()->image('company.png')->store('logos/perusahaan', 'public');
    SchoolSetting::current()->update(['logo_path' => $schoolLogo, 'nama_sekolah' => 'SMKN 43 Jakarta']);

    $admin = User::factory()->create(['role' => 'admin_hubin']);
    $student = User::factory()->create(['role' => 'siswa_pkl']);
    $teacher = User::factory()->create(['role' => 'guru_pembimbing']);
    $company = User::factory()->create(['role' => 'pembimbing_industri']);
    $otherCompany = User::factory()->create(['role' => 'pembimbing_industri']);
    IndustriProfile::query()->create([
        'user_id' => $company->id,
        'nama_perusahaan' => 'Perusahaan Dinamis',
        'nama_pembimbing' => 'Pembimbing Industri',
        'logo_path' => $companyLogo,
    ]);
    $otherCompanyLogo = UploadedFile::fake()->image('other-company.png')->store('logos/perusahaan', 'public');
    IndustriProfile::query()->create([
        'user_id' => $otherCompany->id,
        'nama_perusahaan' => 'Mitra Kedua',
        'nama_pembimbing' => 'Pembimbing Kedua',
        'logo_path' => $otherCompanyLogo,
    ]);
    $placement = PKLSiswa::query()->create([
        'siswa_id' => $student->id,
        'guru_id' => $teacher->id,
        'industri_id' => $company->id,
        'nama_perusahaan' => 'Perusahaan Dinamis',
        'nama_pembimbing' => 'Pembimbing Industri',
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->subDay()->toDateString(),
        'status' => 'selesai',
    ]);

    $this->actingAs($admin)->post(route('admin.placements.sertifikat.generate', $placement))
        ->assertRedirect();

    $certificate = $placement->sertifikat()->firstOrFail();
    expect($certificate->logos_snapshot_at)->not->toBeNull()
        ->and($certificate->nama_sekolah)->toBe('SMKN 43 Jakarta')
        ->and($certificate->logo_sekolah_path)->toContain("arsip/{$certificate->id}/sekolah")
        ->and($certificate->logo_perusahaan_path)->toContain("arsip/{$certificate->id}/perusahaan");
    Storage::disk('public')->assertExists($certificate->logo_sekolah_path);
    Storage::disk('public')->assertExists($certificate->logo_perusahaan_path);

    $secondStudent = User::factory()->create(['role' => 'siswa_pkl']);
    $secondPlacement = PKLSiswa::query()->create([
        'siswa_id' => $secondStudent->id,
        'guru_id' => $teacher->id,
        'industri_id' => $otherCompany->id,
        'nama_perusahaan' => 'Mitra Kedua',
        'nama_pembimbing' => 'Pembimbing Kedua',
        'tanggal_mulai' => today()->subMonth()->toDateString(),
        'tanggal_selesai' => today()->subDay()->toDateString(),
        'status' => 'selesai',
    ]);
    $this->actingAs($admin)->post(route('admin.placements.sertifikat.generate', $secondPlacement))
        ->assertRedirect();
    $secondCertificate = $secondPlacement->sertifikat()->firstOrFail();
    expect($secondCertificate->logo_perusahaan_path)->not->toBe($certificate->logo_perusahaan_path);
    Storage::disk('public')->assertExists($secondCertificate->logo_perusahaan_path);

    $this->actingAs($company)->put(route('industri.profile.update'), [
        'nama_perusahaan' => 'Nama Profil Diperbarui',
        'nama_pembimbing' => 'Pembimbing Diperbarui',
        'logo' => UploadedFile::fake()->image('company-replacement.png'),
    ])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($companyLogo);
    Storage::disk('public')->assertExists($certificate->logo_perusahaan_path);
    $this->actingAs($student)->get(route('sertifikat.show', $certificate))
        ->assertOk()
        ->assertSee('Perusahaan Dinamis')
        ->assertSee(Storage::disk('public')->url($certificate->logo_perusahaan_path), false);

    $this->actingAs($student)->get(route('sertifikat.show', $certificate))
        ->assertOk()
        ->assertSee('Perusahaan Dinamis')
        ->assertSee(Storage::disk('public')->url($certificate->logo_sekolah_path), false)
        ->assertSee(Storage::disk('public')->url($certificate->logo_perusahaan_path), false);
});
