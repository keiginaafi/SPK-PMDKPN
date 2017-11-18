<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NilaiNonAkademis extends Model
{
    protected $table = "nilai_non_akademis";
    public $timestamps = false;
    protected $primaryKey = 'id_prestasi';
    protected $fillable = [
        'nama_prestasi', 'tahun_prestasi'
    ];

    public function mahasiswa(){
        return $this->belongsTo('App\Mahasiswa', 'no_pendaftar');
    }
}
