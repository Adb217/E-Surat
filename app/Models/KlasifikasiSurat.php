<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiSurat extends Model
{
    protected $table = 'klasifikasi_surat';
    protected $primaryKey = 'id_klasifikasi';
    protected $guarded = [];

    public function surat()
    {
        return $this->hasMany(SuratKeluar::class, 'id_klasifikasi', 'id_klasifikasi');
    }
}