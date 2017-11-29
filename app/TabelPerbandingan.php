<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TabelPerbandingan extends Model
{
  protected $table = "tabel_perbandingan";
  public $timestamps = false;
  protected $primaryKey = 'id_perbandingan';
  protected $fillable = [
    'id_kriteria_1', 'id_kriteria_2', 'nilai_banding', 'normalisasi'
  ];

  public function kriteria(){
      return $this->belongsTo('App\Kriteria', 'id_kriteria');
  }
}
