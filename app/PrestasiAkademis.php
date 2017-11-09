<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrestasiAkademis extends Model
{
    protected $table = "nilai_akademis";
    public $timestamps = false;
    protected $primaryKey = 'id_nilai_akademis';
    public $incrementing = false;
    protected $fillable = [
        'semester', 'mapel'
    ];
}
