<?php

namespace App\Http\Controllers\Moora;

ini_set('memory_limit', '256M');

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
use App\SaranPenerimaan as saran_penerimaan;

class SaranPenerimaanController extends Controller
{
  //include fungsi AHP
  protected $ahpService;

  public function __construct(AHP $ahpService){
    $this->middleware('auth');
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
  public function getDataPenerimaan($id){
    $data_prodi = '';
    $data_saran = '';
    try {
      $data_prodi = DB::table('prodi')
      ->select('kuota_sma', 'kuota_smk', 'kuota_cadangan')
      ->where('kode_prodi', $id)
      ->get();

      $data_saran = DB::table('saran_penerimaan')
      ->join('mahasiswa', 'saran_penerimaan.no_pendaftar', '=', 'mahasiswa.no_pendaftar')
      ->select('saran_penerimaan.no_pendaftar', 'mahasiswa.nisn', 'mahasiswa.nama',
      'mahasiswa.jenis_kelamin', 'mahasiswa.tipe_sekolah', 'mahasiswa.jurusan_asal',
      'mahasiswa.nilai_akhir', 'saran_penerimaan.ranking')
      ->where('saran_penerimaan.kode_prodi', $id)
      ->get();

      $data_saran = $data_saran->sortBy('ranking', SORT_NATURAL, true);
    } catch (\Illuminate\Database\QueryException $ex) {
      return Response::json($ex->getMessage());
    }

    $response = array(
      'sma' => $data_prodi[0]->kuota_sma,
      'smk' => $data_prodi[0]->kuota_smk,
      'cadangan' => $data_prodi[0]->kuota_cadangan,
      'saran' => $data_saran,
    );
    return Response::json($response);
    //var_dump($response);
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
      set_time_limit(0);
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
      set_time_limit(0);
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
      set_time_limit(0);
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
      set_time_limit(0);
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
    ->select('no_pendaftar', 'nilai_akademis', 'nilai_non_akademis', 'akreditasi_sekolah')
    ->orderBy('no_pendaftar')
    ->get();

    foreach ($data_mhs as $value) {
      set_time_limit(0);

      if ($value->akreditasi_sekolah == 'A') {
        $akr = 2.0;
      } elseif ($value->akreditasi_sekolah == 'B') {
        $akr = 1.8;
      } elseif ($value->akreditasi_sekolah == 'C') {
        $akr = 1.6;
      } else {
        $akr = 1.4;
      }

      $data_normalisasi[$value->no_pendaftar] = array(
        'Nilai Akademis' => $value->nilai_akademis/$sqrt_nilai_akademis,
        'Nilai Non Akademis' => $value->nilai_non_akademis/$sqrt_nilai_non_akademis,
        'Akreditasi Sekolah' => $akr / $sqrt_akreditasi_sekolah,
      );
    }

    //ambil bobot
    $get_bobot = DB::table('kriteria')
    ->select('nama_kriteria', 'bobot_kriteria')
    ->orderBy('id_kriteria')
    ->get();

    //simpan bobot dalam array
    foreach ($get_bobot as $value) {
      set_time_limit(0);
      $bobot[$value->nama_kriteria] = $value->bobot_kriteria;
    }
    //var_dump($bobot);

    //mulai proses optimisasi
    foreach ($data_normalisasi as $key => $value) {
      set_time_limit(0);
      $optimize_result[$key] = 0;
      foreach ($value as $k => $v) {
        set_time_limit(0);
        $optimize_result[$key] += ($v * $bobot[$k]);
      }
    }

    //save nilai akhir ke database
    foreach ($optimize_result as $key => $value) {
      set_time_limit(0);
      //var_dump($key.' => '.$value);
      try {
        $mhs = mahasiswa::where('no_pendaftar', $key)
        ->update(['nilai_akhir' => $value]);
      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors($ex->getMessage());
      }
    }

    //lakukan perankingan dengan input ke tabel saran penerimaan dg nilai akhir terbesar
    try {
      //ambil data prodi
      $data_prodi = DB::table('prodi')
      ->select('kode_prodi', 'nama_prodi')
      ->get();

      //ambil data mhs dari tiap prodi
      foreach ($data_prodi as $value) {
        set_time_limit(0);
        $data_moora = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->get();

        $rank = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora as $val) {
          set_time_limit(0);
          $rank[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank);
        //inisiasi ranking
        $i = 1;
        //save ke database
        foreach ($rank as $k => $v) {
          set_time_limit(0);
          $saran = new saran_penerimaan();
          $saran->no_pendaftar = $k;
          $saran->kode_prodi = $value->kode_prodi;
          $saran->periode = date('Y');
          $saran->ranking = $i;
          if (!$saran->save()) {
            return Redirect::back()->withErrors('The server encountered an unexpected condition');
          }
          $i += 1;
        }
      }
    } catch (\Illuminate\Database\QueryException $ex) {
      return Redirect::back()->withErrors($ex->getMessage());
    }

    $response = array(
      'fail' => 0,
      'input' => 'Success',
      'message' => 'Saran Penerimaan telah dihasilkan'
    );
    return Response::json($response);
  }
}
