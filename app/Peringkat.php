<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peringkat extends Model
{
    protected $table = "peringkat";
    public $timestamps = false;
    protected $primaryKey = 'id_peringkat';
    protected $fillable = [
        'semester', 'peringkat', 'jumlah_siswa'
    ];

    public function mahasiswa(){
        return $this->belongsTo('App\Mahasiswa', 'no_pendaftar');
    }
}
