<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pkl_siswa', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->change();
            $table->foreignId('industri_id')->nullable()->change();
            $table->string('guru_nama')->nullable();
            $table->string('guru_nip', 100)->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('nama_pembimbing')->nullable();
        });

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('pkl_siswa')->whereNull('guru_id')->orWhereNull('industri_id')->exists()) {
            throw new RuntimeException('Typed placement contacts must be migrated before this migration can be rolled back.');
        }

        if (DB::table('sertifikat')->whereNull('file_path')->exists()) {
            throw new RuntimeException('Printed certificates must be migrated before this migration can be rolled back.');
        }

        Schema::table('pkl_siswa', function (Blueprint $table) {
            $table->dropColumn(['guru_nama', 'guru_nip', 'nama_perusahaan', 'nama_pembimbing']);
            $table->foreignId('guru_id')->nullable(false)->change();
            $table->foreignId('industri_id')->nullable(false)->change();
        });

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
