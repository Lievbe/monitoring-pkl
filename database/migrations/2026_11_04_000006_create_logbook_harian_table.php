<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_siswa_id')->constrained('pkl_siswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('kegiatan');
            $table->text('keterangan')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->enum('status_kehadiran', ['hadir', 'sakit', 'izin', 'alfa'])->default('hadir');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->enum('verifikasi_status', ['belum', 'disetujui', 'ditolak'])->default('belum');
            $table->text('catatan_guru')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_harian');
    }
};
