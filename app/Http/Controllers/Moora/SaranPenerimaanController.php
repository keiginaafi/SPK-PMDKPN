<?php

namespace App\Http\Controllers\Moora;

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

class SaranPenerimaanController extends Controller
{
  /*public function __construct(){
    $this->middleware('auth');
  }*/

  public function index(){
    $dataProdi = prodi::select(DB::raw("kode_prodi, nama_prodi"))
    ->orderBy(DB::raw("kode_prodi"))
    ->get();
    $data = array('prodi' => $dataProdi);
    return view('admin.dashboard.saran_penerimaan.SaranPenerimaanView', $data);
  }

  //fungsi untuk mengambil data penerimaan
  public function getDataPenerimaan(){

  }

  //fungsi untuk periksa CI
  public function periksaCI(){

  }

  //fungsi untuk menghasilkan saran penerimaan dengan metode moora
  public function saranPenerimaan(){

  }
}
