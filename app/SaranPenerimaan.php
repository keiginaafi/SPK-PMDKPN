<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaranPenerimaan extends Model
{
  protected $table = "saran_penerimaan";
  public $timestamps = false;
  protected $primaryKey = 'id_saran';
  protected $fillable = [
    
  ];
}
