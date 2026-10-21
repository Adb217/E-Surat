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
       Schema::create('detail_kegiatan', function (Blueprint $table) {
    $table->id('id_detail');
    $table->unsignedBigInteger('id_surat')->unique(); // unique = relasi 1-to-1
    $table->string('hari_tgl_kegiatan');
    $table->string('waktu_kegiatan');
    $table->string('tempat_kegiatan');
    $table->timestamps();

    $table->foreign('id_surat')->references('id_surat')->on('surat_keluar')->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatan');
    }
};
