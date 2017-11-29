<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = "kriteria";
	  public $timestamps = false;
	  protected $primaryKey = 'id_kriteria';
    protected $fillable = [
        'nama_kriteria', 'bobot_kriteria',
    ];

    public function tabel_perbandingan(){
        return $this->hasMany('App\TabelPerbandingan', 'id_kriteria_1');
    }
}
