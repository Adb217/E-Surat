<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('pegawai', function (Blueprint $table) {
    $table->id('id_pegawai');
    $table->string('nip')->nullable();
    $table->string('nama_lengkap');
    $table->string('jabatan');
    $table->string('file_ttd_stempel')->nullable();
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
