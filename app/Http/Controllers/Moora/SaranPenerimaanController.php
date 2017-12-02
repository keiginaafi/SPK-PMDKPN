<?php

namespace App\Http\Controllers\Moora;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Http\AHP;
use App\Prodi as prodi;
use App\Mahasiswa as mahasiswa;
use App\Peringkat as peringkat;
use App\PilihanMhs as pilihan_mhs;
//use App\NilaiAkademis as nilai_akademis;
//use App\NilaiNonAkademis as nilai_non_akademis;
use App\Kriteria as kriteria;

class SaranPenerimaanController extends Controller
{
  //include fungsi AHP
  protected $ahpService;

  public function __construct(AHP $ahpService){
    //$this->middleware('auth');
    $this->ahpService = $ahpService;
  }

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

  //fungsi untuk menghasilkan saran penerimaan dengan metode moora
  public function saranPenerimaan(){
    //periksa CR
    $nilai_cr = $this->ahpService->hitungConsistency();
    if ($nilai_cr == -1) {
      $response = array(
        'fail' => 1,
        'input' => 'Tabel perbandingan belum diisi',
        'message' => 'Tidak bisa periksa consistency bila tabel ada yang kosong',
      );
      return Response::json($response);
    } elseif ($nilai_cr > 0.1) {
      $response = array(
        'fail' => 1,
        'input' => 'Nilai consistency ratio lebih dari 10%',
        'message' => 'Tabel penilaian perlu diubah'
      );
      return Response::json($response);
    }

    //cek apakah data sudah diolah
    $cek_data = DB::table('mahasiswa')->count();
    if ($cek_data == 0) {
      $response = array(
        'fail' => 1,
        'input' => 'Data Mahasiswa belum diisi',
        'message' => 'Silahkan input data mahasiswa dahulu',
      );
      return Response::json($response);
    }
    $cek_nilai = DB::table('mahasiswa')
    ->select('nilai_akademis')
    ->get();
    foreach ($cek_nilai as $value) {
      if ($value->nilai_akademis == 0) {
        $response = array(
          'fail' => 1,
          'input' => 'Data Mahasiswa belum diolah',
          'message' => 'Silahkan olah data mahasiswa dahulu',
        );
        return Response::json($response);
      }
    }
    unset($cek_data);
    unset($cek_nilai);

    //jika CR lolos, mulai penghitungan metode moora
    //ambil sum nilai kuadrat dari tiap kriteria lalu di akar
    $get_nilai_akademis = DB::table('mahasiswa')
    ->select('nilai_akademis')
    ->get();
    //initiate value sum of square
    $sum_sqr_nilai_akademis = 0;
    foreach ($get_nilai_akademis as $value) {
      $sum_sqr_nilai_akademis = $sum_sqr_nilai_akademis + ($value->nilai_akademis ** 2);
    }
    //define denominator for nilai_akademis
    $sqrt_nilai_akademis = sqrt($sum_sqr_nilai_akademis);

    $get_nilai_non_akademis = DB::table('mahasiswa')
    ->select('nilai_non_akademis')
    ->get();
    //initiate value sum of square
    $sum_sqr_nilai_non_akademis = 0;
    foreach ($get_nilai_non_akademis as $value) {
      $sum_sqr_nilai_non_akademis = $sum_sqr_nilai_non_akademis + ($value->nilai_non_akademis ** 2);
    }
    //define denominator of nilai_non_akademis
    $sqrt_nilai_non_akademis = sqrt($sum_sqr_nilai_non_akademis);

    //convert akreditasi ke nilai numerik
    $get_akreditasi = DB::table('mahasiswa')
    ->select('akreditasi_sekolah')
    ->get();
    //initiate value sum of square
    $sum_sqr_akreditasi_sekolah = 0;
    foreach ($get_akreditasi as $value) {
      switch ($value->akreditasi_sekolah) {
        case 'A':
          $sum_sqr_akreditasi_sekolah = $sum_sqr_akreditasi_sekolah + (2.0 ** 2);
          break;

        case 'B':
          $sum_sqr_akreditasi_sekolah = $sum_sqr_akreditasi_sekolah + (1.8 ** 2);
          break;

        case 'C':
          $sum_sqr_akreditasi_sekolah = $sum_sqr_akreditasi_sekolah + (1.6 ** 2);
          break;

        default:
          $sum_sqr_akreditasi_sekolah = $sum_sqr_akreditasi_sekolah + (1.4 ** 2);
          break;
      }
    }
    //define denominator of akreditasi_sekolah
    $sqrt_akreditasi_sekolah = sqrt($sum_sqr_akreditasi_sekolah);

    //normalisasi tiap nilai kriteria tiap mahasiswa dengan denominator masing-masing
    $data_mhs = DB::table('mahasiswa')
    ->select('nilai_akademis', 'nilai_non_akademis', 'akreditasi_sekolah')
    ->orderBy('no_pendaftar')
    ->get();

    //ambil bobot
    //$bobot = DB::table('kriteria');
  }
}
