<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\IndustriDashboardController;
use App\Http\Controllers\IndustriProfileController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolIdentityController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\SiswaDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/sertifikat/{sertifikat}', [SertifikatController::class, 'show'])->name('sertifikat.show');

    Route::middleware('role:siswa_pkl')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/logbook', [SiswaDashboardController::class, 'logbook'])->name('logbook');
        Route::get('/kalender', [SiswaDashboardController::class, 'logbook'])->name('calendar');
        Route::get('/riwayat', [SiswaDashboardController::class, 'history'])->name('history');
        Route::get('/profil', [SiswaDashboardController::class, 'profile'])->name('profile');
        Route::post('/logbook', [SiswaDashboardController::class, 'storeLogbook'])->name('logbook.store');
        Route::put('/logbook/{logbook}', [SiswaDashboardController::class, 'updateLogbook'])
            ->whereNumber('logbook')
            ->name('logbook.update');
        Route::delete('/logbook/{logbook}', [SiswaDashboardController::class, 'destroyLogbook'])
            ->whereNumber('logbook')
            ->name('logbook.destroy');
    });

    Route::middleware('role:guru_pembimbing')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/siswa', [GuruDashboardController::class, 'students'])->name('students');
        Route::get('/penilaian', [GuruDashboardController::class, 'assessments'])->name('assessments');
        Route::get('/logbook', [GuruDashboardController::class, 'logbooks'])->name('logbooks');
        Route::post('/logbook/setujui-semua', [GuruDashboardController::class, 'approveAll'])->name('logbooks.approve-all');
        Route::put('/logbook/{logbook}', [GuruDashboardController::class, 'verifikasi'])->whereNumber('logbook')->name('logbook.verifikasi');
        Route::get('/penilaian/{pklSiswa}', [PenilaianController::class, 'create'])->whereNumber('pklSiswa')->name('penilaian.create');
        Route::post('/penilaian/{pklSiswa}', [PenilaianController::class, 'store'])->whereNumber('pklSiswa')->name('penilaian.store');
        Route::delete('/penilaian/{pklSiswa}', [PenilaianController::class, 'destroy'])->whereNumber('pklSiswa')->name('penilaian.destroy');
    });

    Route::middleware('role:pembimbing_industri')->prefix('industri')->name('industri.')->group(function () {
        Route::get('/dashboard', [IndustriDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profil', [IndustriProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [IndustriProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profil/logo', [IndustriProfileController::class, 'destroyLogo'])->name('profile.logo.destroy');
    });

    Route::middleware('role:admin_hubin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/identitas-sekolah', [SchoolIdentityController::class, 'edit'])->name('school-identity.edit');
        Route::put('/identitas-sekolah', [SchoolIdentityController::class, 'update'])->name('school-identity.update');
        Route::delete('/identitas-sekolah/logo', [SchoolIdentityController::class, 'destroyLogo'])->name('school-identity.logo.destroy');
        Route::get('/siswa/daftar', [AdminDashboardController::class, 'createStudent'])->name('students.create');
        Route::get('/guru/daftar', [AdminDashboardController::class, 'createTeacher'])->name('teachers.create');
        Route::get('/mitra/daftar', [AdminDashboardController::class, 'createIndustry'])->name('industries.create');
        Route::get('/penempatan/buat', [AdminDashboardController::class, 'createPlacement'])->name('placements.create');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
        Route::post('/placements', [AdminDashboardController::class, 'storePlacement'])->name('placements.store');
        Route::get('/placements/{pklSiswa}/edit', [AdminDashboardController::class, 'editPlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.edit');
        Route::put('/placements/{pklSiswa}', [AdminDashboardController::class, 'updatePlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.update');
        Route::delete('/placements/{pklSiswa}', [AdminDashboardController::class, 'destroyPlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.destroy');
        Route::patch('/placements/{pklSiswa}/selesai', [AdminDashboardController::class, 'completePlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.complete');
        Route::post('/placements/{pklSiswa}/sertifikat', [SertifikatController::class, 'generate'])
            ->whereNumber('pklSiswa')
            ->name('placements.sertifikat.generate');
        Route::delete('/placements/{pklSiswa}/sertifikat', [SertifikatController::class, 'destroy'])
            ->whereNumber('pklSiswa')
            ->name('placements.sertifikat.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
