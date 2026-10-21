<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';
    protected $primaryKey = 'id_masuk';
    protected $guarded = [];

    protected $casts = [
        'tgl_diterima' => 'date',
        'tgl_surat'    => 'date',
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiSurat::class, 'id_klasifikasi', 'id_klasifikasi');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'id_pencatat', 'id_user');
    }
}