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
       Schema::create('surat_keluar', function (Blueprint $table) {
    $table->id('id_surat');
    $table->unsignedInteger('nomor_urut')->nullable();   // baru diisi saat Finalisasi
    $table->unsignedBigInteger('id_klasifikasi');
    $table->unsignedBigInteger('id_pembuat');
    $table->unsignedBigInteger('id_penandatangan');
    $table->string('bulan_romawi', 5);
    $table->unsignedSmallInteger('tahun');
    $table->date('tgl_surat');
    $table->string('perihal');
    $table->string('lampiran')->default('-');
    $table->text('tujuan_surat');
    $table->text('paragraf_pembuka')->nullable();
    $table->text('paragraf_penutup')->nullable();
    $table->string('status_surat', 20)->default('Draft'); // Draft | Final | Dibatalkan

    // konfirmasi ACC Kepsek (offline) + hasil finalisasi
    $table->boolean('acc_kepsek')->default(false);
    $table->string('catatan_acc')->nullable();
    $table->timestamp('tgl_acc')->nullable();
    $table->unsignedBigInteger('acc_dikonfirmasi_oleh')->nullable();
    $table->string('nomor_lengkap')->nullable();
    $table->string('nama_file')->nullable();
    $table->string('path_pdf')->nullable();
    $table->string('kode_verifikasi', 40)->nullable()->unique();
    $table->string('hash_dokumen', 64)->nullable();
    $table->timestamps();

    $table->foreign('id_klasifikasi')->references('id_klasifikasi')->on('klasifikasi_surat');
    $table->foreign('id_pembuat')->references('id_user')->on('pengguna');
    $table->foreign('id_penandatangan')->references('id_pegawai')->on('pegawai');
    $table->foreign('acc_dikonfirmasi_oleh')->references('id_user')->on('pengguna');

    // cegah nomor ganda per tahun (NULL boleh berulang, jadi draft aman)
    $table->unique(['tahun', 'nomor_urut']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
