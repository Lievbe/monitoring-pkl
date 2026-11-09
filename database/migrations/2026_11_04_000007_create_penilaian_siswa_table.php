<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_siswa_id')->constrained('pkl_siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('hard_skill'); // 0-100
            $table->unsignedTinyInteger('soft_skill'); // 0-100
            $table->unsignedTinyInteger('nilai_akhir')->nullable();
            $table->enum('predikat', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_penilaian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_siswa');
    }
};
