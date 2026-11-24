<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('industri_profiles', function (Blueprint $table) {
            $table->string('logo_path')->nullable();
        });

        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah')->default('SMKN 43 Jakarta');
            $table->string('logo_path')->nullable();
            $table->text('alamat')->nullable();
            $table->text('informasi')->nullable();
            $table->timestamps();
        });

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('logo_sekolah_path')->nullable();
            $table->string('logo_perusahaan_path')->nullable();
            $table->string('nama_sekolah')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('nama_pembimbing')->nullable();
            $table->timestamp('logos_snapshot_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropColumn(['logo_sekolah_path', 'logo_perusahaan_path', 'nama_sekolah', 'nama_perusahaan', 'nama_pembimbing', 'logos_snapshot_at']);
        });

        Schema::dropIfExists('school_settings');

        Schema::table('industri_profiles', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
