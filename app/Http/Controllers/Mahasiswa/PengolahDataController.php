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
}
