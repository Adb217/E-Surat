<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $table = 'surat_keluar';
    protected $primaryKey = 'id_surat';
    protected $guarded = [];

    protected $casts = [
        'tgl_surat'  => 'date',
        'tgl_acc'    => 'datetime',
        'acc_kepsek' => 'boolean',
    ];

    
    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiSurat::class, 'id_klasifikasi', 'id_klasifikasi');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_pembuat', 'id_user');
    }

    public function penandatangan()
    {
        return $this->belongsTo(Pegawai::class, 'id_penandatangan', 'id_pegawai');
    }

    public function detail()
    {
        return $this->hasOne(DetailKegiatan::class, 'id_surat', 'id_surat');
    }

    public function isFinal(): bool
    {
        return $this->status_surat === 'Final';
    }

    public function getNomorTampilAttribute(): string
    {
        return $this->nomor_lengkap ?? 'Draft (belum bernomor)';
    }
}