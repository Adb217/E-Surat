<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSekolah extends Model
{
    protected $table = 'pengaturan_sekolah';
    protected $guarded = [];

    // PengaturanSekolah::ambil('npsn') atau ::ambil() untuk semua sebagai array
    public static function ambil(?string $kunci = null, $default = null)
    {
        $semua = static::pluck('nilai', 'kunci')->all();
        return $kunci ? ($semua[$kunci] ?? $default) : $semua;
    }

    public static function kop(): array
    {
        $k = static::ambil();
        $k['logo_url'] = !empty($k['logo']) ? asset('storage/' . $k['logo']) : null;
        return $k;
    }
}