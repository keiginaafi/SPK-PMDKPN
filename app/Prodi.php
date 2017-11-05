<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
     protected $table = "prodi";
	   public $timestamps = false;
	   protected $primaryKey = 'kode_prodi';
     protected $fillable = [
         'nama_prodi', 'kuota_max', 'kuota_penerimaan', 'kuota_sma', 'kuota_smk', 'kuota_cadangan'
     ];
}
