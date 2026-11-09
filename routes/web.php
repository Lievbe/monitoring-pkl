<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\IndustriDashboardController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\ProfileController;
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
        Route::post('/logbook', [SiswaDashboardController::class, 'storeLogbook'])->name('logbook.store');
    });

    Route::middleware('role:guru_pembimbing')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::put('/logbook/{logbook}', [GuruDashboardController::class, 'verifikasi'])->whereNumber('logbook')->name('logbook.verifikasi');
        Route::get('/penilaian/{pklSiswa}', [PenilaianController::class, 'create'])->whereNumber('pklSiswa')->name('penilaian.create');
        Route::post('/penilaian/{pklSiswa}', [PenilaianController::class, 'store'])->whereNumber('pklSiswa')->name('penilaian.store');
    });

    Route::middleware('role:pembimbing_industri')->prefix('industri')->name('industri.')->group(function () {
        Route::get('/dashboard', [IndustriDashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('role:admin_hubin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/siswa/daftar', [AdminDashboardController::class, 'createStudent'])->name('students.create');
        Route::get('/penempatan/buat', [AdminDashboardController::class, 'createPlacement'])->name('placements.create');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
        Route::post('/placements', [AdminDashboardController::class, 'storePlacement'])->name('placements.store');
        Route::get('/placements/{pklSiswa}/edit', [AdminDashboardController::class, 'editPlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.edit');
        Route::put('/placements/{pklSiswa}', [AdminDashboardController::class, 'updatePlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.update');
        Route::patch('/placements/{pklSiswa}/selesai', [AdminDashboardController::class, 'completePlacement'])
            ->whereNumber('pklSiswa')
            ->name('placements.complete');
        Route::post('/placements/{pklSiswa}/sertifikat', [SertifikatController::class, 'generate'])
            ->whereNumber('pklSiswa')
            ->name('placements.sertifikat.generate');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
