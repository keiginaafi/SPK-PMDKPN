<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilihanMhs extends Model
{
    protected $table = "pilihan_mhs";
    public $timestamps = false;
    protected $primaryKey = 'id_pilihan';
    protected $fillable = [
        'pilihan_ke', 'pilihan_poltek', 'pilihan_prodi'
    ];

    public function mahasiswa(){
        return $this->belongsTo('App\Mahasiswa', 'no_pendaftar');
    }
}
