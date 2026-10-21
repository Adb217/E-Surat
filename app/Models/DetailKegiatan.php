<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    protected $table = 'detail_kegiatan';
    protected $primaryKey = 'id_detail';
    protected $guarded = [];
}