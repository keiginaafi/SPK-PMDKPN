<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NilaiAkademis extends Model
{
    protected $table = "nilai_akademis";
    public $timestamps = false;
    protected $primaryKey = 'id_nilai_akademis';    
    protected $fillable = [
        'semester', 'mapel'
    ];

    public function mahasiswa(){
        return $this->belongsTo('App\Mahasiswa', 'foreign_key', 'no_pendaftar');
    }
}
