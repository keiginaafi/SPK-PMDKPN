<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrestasiNonAkademis extends Model
{
    protected $table = "nilai_non_akademis";
    public $timestamps = false;
    protected $primaryKey = 'id_prestasi';
    public $incrementing = false;
    protected $fillable = [
        'nama_prestasi', 'tahun_prestasi'
    ];
}
