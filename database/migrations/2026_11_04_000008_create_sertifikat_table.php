<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_siswa_id')->constrained('pkl_siswa')->onDelete('cascade');
            $table->string('nomor_sertifikat')->unique();
            $table->date('tanggal_terbit');
            $table->string('file_path');
            $table->string('qr_code_path')->nullable();
            $table->string('verifikasi_url');
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
