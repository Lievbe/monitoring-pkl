<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industri_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_perusahaan');
            $table->string('alamat')->nullable();
            $table->string('nama_pembimbing');
            $table->string('jabatan')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email_perusahaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industri_profiles');
    }
};
