<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";
    public $timestamps = false;
    protected $primaryKey = 'no_pendaftar';
    public $incrementing = false;
    protected $fillable = [
        'nama', 'jenis_kelamin', 'agama', 'tgl_lahir', 'kecamatan', 'kota', 'provinsi',
        'npsn', 'tipe_sekolah', 'jenis_sekolah', 'jurusan_asal', 'periode'
    ];
}
