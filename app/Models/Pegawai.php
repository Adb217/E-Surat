<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    protected $guarded = [];

    protected $casts = ['aktif' => 'boolean'];

    public function surat()
    {
        return $this->hasMany(SuratKeluar::class, 'id_penandatangan', 'id_pegawai');
    }
}