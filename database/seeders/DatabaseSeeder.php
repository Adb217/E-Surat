<?php

namespace Database\Seeders;

use App\Models\{User, Pegawai, KlasifikasiSurat, PengaturanSekolah};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun tunggal (Pak Yazid). GANTI password sebelum dipakai di hosting.
        User::updateOrCreate(
            ['username' => 'yazid'],
            ['nama_lengkap' => 'Yazid', 'role' => 'admin', 'password' => 'ganti-password-ini']
        );

        // Penandatangan (sesuai surat asli)
        Pegawai::updateOrCreate(
            ['nama_lengkap' => 'Deden Hendradi, S.Kom, M.Pd.'],
            ['jabatan' => 'Kepala Sekolah', 'aktif' => true]
        );

        // Klasifikasi: baru 'H' yang gua tau, tambah sisanya di sini
        KlasifikasiSurat::updateOrCreate(
            ['kode_indeks' => 'H'],
            ['keterangan' => 'Humas & Undangan']
        );

        // Data kop surat (dari surat asli, tolong dicek lagi)
        $kop = [
            'nomor_awal' => '571',   // nomor pertama yang dipakai kalau belum ada surat bernomor sama sekali
            'nama_yayasan'  => 'YAYASAN DHARMA SETIA KOSGORO',
            'nama_program'  => 'PROGRAM SMK PUSAT KEUNGGULAN',
            'nama_sekolah'  => 'SMK KOSGORO KOTA BOGOR',
            'akreditasi'    => "TERAKREDITASI 'A' \"AMAT BAIK\"",
            'nss'           => '342026101012',
            'npsn'          => '20276384',
            'alamat'        => 'JALAN PAJAJARAN 217A, BANTARJATI, BOGOR UTARA, KOTA BOGOR 16153',
            'telepon'       => '0251-7545041',
            'website'       => 'smk-kosgoro.sch.id',
            'email'         => 'smkkosgorobogor.sch.id', // di surat asli tanpa "@", cek yang benar
            'kota'          => 'Bogor',
        ];
        foreach ($kop as $k => $v) {
            PengaturanSekolah::updateOrCreate(['kunci' => $k], ['nilai' => $v]);
        }
    }
}