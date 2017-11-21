<?php

namespace App\Http\Controllers\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Prodi as prodi;
use App\Mahasiswa as mahasiswa;
use App\Peringkat as peringkat;
use App\PilihanMhs as pilihan_mhs;
use App\NilaiAkademis as nilai_akademis;
use App\NilaiNonAkademis as nilai_non_akademis;

class PengolahDataController extends Controller
{
  /*public function __construct(){
    $this->middleware('auth');
  }*/

  public function index(){
    $dataProdi = prodi::select(DB::raw("kode_prodi, nama_prodi"))
    ->orderBy(DB::raw("kode_prodi"))
    ->get();
    $data = array('prodi' => $dataProdi);
    return view('admin.dashboard.mahasiswa.dataPendaftarView', $data);
  }

  public function getDataMhs($id){
    //$id_prodi = $id;
    $data_pendaftar = "";
    $data_prodi = "";
    try {
      //get nama prodi based on id
      $data_prodi = DB::table('prodi')
      ->select('nama_prodi')
      ->where('kode_prodi', '=', $id)
      ->get();

      //get data mhs
      $data_pendaftar = DB::table('mahasiswa')
      ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
      ->select('mahasiswa.no_pendaftar', 'mahasiswa.nisn', 'mahasiswa.nama',
      'mahasiswa.jenis_kelamin', 'mahasiswa.agama', 'mahasiswa.tgl_lahir',
      'mahasiswa.kota', 'mahasiswa.tipe_sekolah', 'mahasiswa.jenis_sekolah',
      'mahasiswa.akreditasi_sekolah', 'mahasiswa.jurusan_asal', 'pilihan_mhs.pilihan_ke')
      ->where('pilihan_mhs.pilihan_prodi', '=', $data_prodi[0]->nama_prodi)
      ->get();
    } catch(\Illuminate\Database\QueryException $ex){
      dd($ex->getMessage());
      // Note any method of class PDOException can be called on $ex.
    }

    //var_dump($data_pendaftar);
    return Response::json($data_pendaftar);
  }

  public function detailMhs($id){
    //get data akademis and prestasi based on id
    $data_akademis = nilai_akademis::where('no_pendaftar', '=', $id)
    ->with(['mahasiswa:no_pendaftar,nama', 'mahasiswa.nilai_non_akademis'])
    ->orderBy('semester')
    ->get();

    /*foreach ($data_akademis as $akademis) {
      var_dump($akademis->mahasiswa->nilai_non_akademis[0]->nama_prestasi);
    }*/


    $data = array('data_mhs' => $data_akademis);
    return view('admin.dashboard.mahasiswa.detailView', $data);
  }

  public function olahDataMhs(){
    //get nilai akademis
    $nilai = nilai_akademis::all();
    foreach ($nilai as $akademis) {
      //jenis nilai 4
      if($akademis->jenis_nilai == 4){
        //cek nilai antara 1 - 4
        if($akademis->nilai_mapel >= 1 && $akademis->nilai_mapel <= 4){
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel * 25;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        //cek nilai antara 1 - 10
        }elseif($akademis->nilai_mapel >= 1 && $akademis->nilai_mapel <= 10){
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel * 10;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        //cek nilai antara 1 - 100
        }else{
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        }
      //jenis nilai = 10
      }elseif($akademis->jenis_nilai == 10){
        //cek nilai antara 1 - 4
        if($akademis->nilai_mapel >= 1 && $akademis->nilai_mapel <= 4){
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel * 25;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        //cek nilai antara 1 - 10
        }elseif($akademis->nilai_mapel >= 1 && $akademis->nilai_mapel <= 10){
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel * 10;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        //cek nilai antara 1 - 100
        }else{
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        }
      //jenis nilai = 100
      }else{
        //cek nilai antara 1 - 4
        if($akademis->nilai_mapel >= 1 && $akademis->nilai_mapel <= 4){
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel * 25;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        //cek nilai antara 1 - 10
        }elseif($akademis->nilai_mapel >= 1 && $akademis->nilai_mapel <= 10){
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel * 10;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        //cek nilai antara 1 - 100
        }else{
          $akademis->nilai_mapel_koreksi = $akademis->nilai_mapel;
          if(!$akademis->save()){
      			return Redirect::back()->withErrors('The server encountered an unexpected condition');
      		}
        }
      }
    }

    //get prestasi
    $lomba = nilai_non_akademis::all();
    foreach ($lomba as $prestasi) {
      $nilai_prestasi = 0;

      //cek skala prestasi
      if ($prestasi->skala_prestasi == "KOTA") {
        $nilai_prestasi = 1;
      }elseif ($prestasi->skala_prestasi == "PROVINSI") {
        $nilai_prestasi = 5;
      }elseif ($prestasi->skala_prestasi == "NASIONAL") {
        $nilai_prestasi = 15;
      }elseif ($prestasi->skala_prestasi == "INTERNASIONAL") {
        $nilai_prestasi = 50;
      }

      //cek jenis prestasi
      if ($prestasi->jenis_prestasi == "Kelompok") {
        $nilai_prestasi = $nilai_prestasi * 1;
      }else {
        $nilai_prestasi = $nilai_prestasi * 2;
      }

      //cek juara
      if ($prestasi->juara_prestasi == 1) {
        $nilai_prestasi = $nilai_prestasi * 6;
      }elseif ($prestasi->juara_prestasi == 2) {
        $nilai_prestasi = $nilai_prestasi * 5;
      }elseif ($prestasi->juara_prestasi == 3) {
        $nilai_prestasi = $nilai_prestasi * 4;
      }elseif ($prestasi->juara_prestasi == 4) {
        $nilai_prestasi = $nilai_prestasi * 3;
      }elseif ($prestasi->juara_prestasi == 5) {
        $nilai_prestasi = $nilai_prestasi * 2;
      }else {
        $nilai_prestasi = $nilai_prestasi * 1;
      }

      //simpan ke Mahasiswa
      $mahasiswa = mahasiswa::where('no_pendaftar', '=', $prestasi->no_pendaftar)
      ->update(['nilai_non_akademis' => $nilai_prestasi]);
      /*$mahasiswa->nilai_non_akademis = $nilai_prestasi;
      $mahasiswa->save();*/
      //return success
      return Response::json([
        'input' => 'success',
        'message' => 'Data Pendaftar telah dinormalisasi'
      ])
    }
  }
}
