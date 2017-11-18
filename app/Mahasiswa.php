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

    public function peringkat(){
        return $this->hasMany('App\Peringkat', 'no_pendaftar');
    }

    public function pilihan_mhs(){
        return $this->hasMany('App\PilihanMhs', 'no_pendaftar');
    }

    public function nilai_akademis(){
        return $this->hasMany('App\NilaiAkademis', 'no_pendaftar');
    }

    public function nilai_non_akademis(){
        return $this->hasMany('App\NilaiNonAkademis', 'no_pendaftar');
    }
}
