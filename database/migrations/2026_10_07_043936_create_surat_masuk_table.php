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
        Schema::create('surat_masuk', function (Blueprint $table) {
    $table->id('id_masuk');
    $table->string('kode_unik', 12)->unique();
    $table->date('tgl_diterima');
    $table->date('tgl_surat')->nullable();
    $table->string('nomor_surat_asal')->nullable();
    $table->string('pengirim');
    $table->string('perihal');
    $table->unsignedBigInteger('id_klasifikasi')->nullable();
    $table->text('catatan')->nullable();
    $table->string('file_scan')->nullable();
    $table->unsignedBigInteger('id_pencatat');
    $table->timestamps();

    $table->foreign('id_klasifikasi')->references('id_klasifikasi')->on('klasifikasi_surat')->nullOnDelete();
    $table->foreign('id_pencatat')->references('id_user')->on('pengguna');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
