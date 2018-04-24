<?php

namespace App\Http\Controllers\Mahasiswa;

ini_set('max_execution_time', '1800');
ini_set('memory_limit', '512M');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
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
  public function __construct(){
    $this->middleware('auth');
  }

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
    try {
      //get nama prodi based on id
      /*$data_prodi = DB::table('prodi')
      ->select('nama_prodi')
      ->where('kode_prodi', '=', $id)
      ->get();*/

      //get data mhs
      $data_pendaftar = DB::table('mahasiswa')
      ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
      ->select('mahasiswa.no_pendaftar', 'mahasiswa.nama', 'mahasiswa.jenis_kelamin',
      'mahasiswa.kota', 'mahasiswa.tipe_sekolah', 'mahasiswa.akreditasi_sekolah',
      'mahasiswa.jurusan_asal', 'mahasiswa.pekerjaan_ayah', 'mahasiswa.pendapatan_ayah',
      'mahasiswa.pekerjaan_ibu', 'mahasiswa.pendapatan_ibu', 'mahasiswa.bidik_misi',
      'pilihan_mhs.pilihan_ke')
      //->where('mahasiswa.periode', date('Y'))
      ->where('mahasiswa.periode', '2017')
      ->where('pilihan_mhs.pilihan_prodi', '=', $id)
      ->get();
    } catch(\Illuminate\Database\QueryException $ex){
      dd($ex->getMessage());
      // Note any method of class PDOException can be called on $ex.
    }

    //return Response::json($data_pendaftar);
    $data_pendaftar->transform(function ($data){
      return array_dot($data);
    });
    /*foreach ($data_pendaftar as $key => $value) {
      var_dump($value['no_pendaftar']);
    }*/
    return Datatables::of($data_pendaftar)
    ->addColumn('action', function($data_pendaftar){
      return '<a href="data_pendaftar/'.$data_pendaftar['no_pendaftar'].'/details" class="btn btn-primary btn-flat btn-sm">
      <i class="fa fa-list"></i> Details</a>';
    })
    ->make(true);
  }

  public function detailMhs($id){
    //get data akademis and prestasi based on id
    $data_akademis = nilai_akademis::where('no_pendaftar', '=', $id)
    ->with(['mahasiswa:no_pendaftar,nama', 'mahasiswa.nilai_non_akademis', 'mahasiswa.peringkat'])
    ->orderBy('semester')
    ->get();

    /*foreach ($data_akademis as $akademis) {
      var_dump($akademis->mahasiswa->nilai_non_akademis[0]->nama_prestasi);
    }*/


    $data = array('data_mhs' => $data_akademis);
    return view('admin.dashboard.mahasiswa.detailView', $data);
  }

  public function olahDataMhs(){
    //cek data
    if (DB::table('nilai_akademis')->count() <= 0) {
      return Response::json([
        'fail' => 1,
        'input' => 'Tidak ada data.',
        'message' => 'Data nilai akademis tidak ditemukan.'
      ]);
    }

    //get nilai akademis
    nilai_akademis::chunk(1000, function($nilai){
      foreach ($nilai as $akademis) {
        set_time_limit(0);
        if ($akademis->nilai_mapel_koreksi <= 0) {
          $list_nilai = DB::table('nilai_akademis')
          ->where('no_pendaftar', $akademis->no_pendaftar)
          ->where('semester', $akademis->semester)
          ->get();

          //cek nilai pada variabel list nilai untuk menentukan multiplier
          foreach ($list_nilai as $value) {
            if ($value->nilai_mapel > 10) {
              $multiplier = 1;
              break;
            } elseif ($value->nilai_mapel > 4) {
              $multiplier = 10;
              break;
            } else {
              $multiplier = 25;
            }
          }
          unset($value);

          //isi nilai_mapel_koreksi berdasarkan multiplier
          foreach ($list_nilai as $isi_nilai) {
            try {
              DB::table('nilai_akademis')
              ->where('no_pendaftar', $isi_nilai->no_pendaftar)
              ->where('semester', $isi_nilai->semester)
              ->where('mapel', $isi_nilai->mapel)
              ->update(['nilai_mapel_koreksi' => ($isi_nilai->nilai_mapel * $multiplier)]);
            } catch(\Illuminate\Database\QueryException $ex){
              return Redirect::back()->withErrors('Gagal melakukan normalisasi data nilai akademis.');
              //$ex->getMessage();
            }
          }
          unset($isi_nilai);
        }        
      }
      unset($akademis);
    });
    $memory1 = (memory_get_usage()) / (1024 ** 2);
    /*foreach ($nilai as $value) {
      var_dump($value->no_pendaftar, $value->semester, $value->mapel, $value->nilai_mapel);
    }*/

    //sum nilai avg mapel koreksi tiap semester, lalu save ke mahasiswa
    DB::table('mahasiswa')->select('no_pendaftar')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->orderBy('no_pendaftar', 'asc')
    ->chunk(1000, function($pendaftar){
      foreach ($pendaftar as $id) {
        set_time_limit(0);
        //rata-rata smt 1
        $avg_smt_1 = DB::table('nilai_akademis')
        ->where('no_pendaftar', '=', $id->no_pendaftar)
        ->where('semester', '=', 1)
        ->avg('nilai_mapel_koreksi');

        //rata-rata smt 2
        $avg_smt_2 = DB::table('nilai_akademis')
        ->where('no_pendaftar', '=', $id->no_pendaftar)
        ->where('semester', '=', 2)
        ->avg('nilai_mapel_koreksi');

        //rata-rata smt 3
        $avg_smt_3 = DB::table('nilai_akademis')
        ->where('no_pendaftar', '=', $id->no_pendaftar)
        ->where('semester', '=', 3)
        ->avg('nilai_mapel_koreksi');

        //rata-rata smt 4
        $avg_smt_4 = DB::table('nilai_akademis')
        ->where('no_pendaftar', '=', $id->no_pendaftar)
        ->where('semester', '=', 4)
        ->avg('nilai_mapel_koreksi');

        //rata-rata smt 5
        $avg_smt_5 = DB::table('nilai_akademis')
        ->where('no_pendaftar', '=', $id->no_pendaftar)
        ->where('semester', '=', 5)
        ->avg('nilai_mapel_koreksi');

        //jumlah rata-rata
        $sum = $avg_smt_1 + $avg_smt_2 + $avg_smt_3 + $avg_smt_4 + $avg_smt_5;

        try {
          $mhs = mahasiswa::where('no_pendaftar', '=', $id->no_pendaftar)
          ->update(['nilai_akademis' => $sum]);
        } catch(\Illuminate\Database\QueryException $ex){
          return Redirect::back()->withErrors('Gagal menyimpan nilai akademis mahasiswa.');
          //$ex->getMessage();
          // Note any method of class PDOException can be called on $ex.
        }
        //var_dump($avg_smt_1);
      }
      unset($id);
    });
    $memory2 = (memory_get_usage()) / (1024 ** 2);

    //cek data peringkat
    if(DB::table('peringkat')->count() > 0){
      DB::table('mahasiswa')->select('no_pendaftar')
      //->where('periode', date('Y'))
      ->where('periode', '2017')
      ->orderBy('no_pendaftar', 'asc')
      ->chunk(1000, function($noPendaftar){
        foreach ($noPendaftar as $value) {
          set_time_limit(0);
          //initiate pembagi peringkat
          $pembagi = 1;
          //ambil peringkat dan jumlah siswa
          $data_peringkat_smt_1 = DB::table('peringkat')
          ->select('peringkat', 'jumlah_siswa')
          ->where('no_pendaftar', $value->no_pendaftar)
          ->where('semester', 1)
          ->get();

          //var_dump(count($data_peringkat_smt_1));
          //bagi peringkat dengan jumlah siswa
          if (count($data_peringkat_smt_1) > 0) {
            $nilai_peringkat_smt_1 = $data_peringkat_smt_1[0]->peringkat / $data_peringkat_smt_1[0]->jumlah_siswa;
          } else {
            $nilai_peringkat_smt_1 = 0;
          }

          //cek nilai untuk menambah pembagi
          if($nilai_peringkat_smt_1 > 0){
            $pembagi += 1;
          }

          //ambil peringkat dan jumlah siswa
          $data_peringkat_smt_2 = DB::table('peringkat')
          ->select('peringkat', 'jumlah_siswa')
          ->where('no_pendaftar', $value->no_pendaftar)
          ->where('semester', 2)
          ->get();

          //bagi peringkat dengan jumlah siswa
          if (count($data_peringkat_smt_2) > 0) {
            $nilai_peringkat_smt_2 = $data_peringkat_smt_2[0]->peringkat / $data_peringkat_smt_2[0]->jumlah_siswa;
          } else {
            $nilai_peringkat_smt_2 = 0;
          }

          //cek nilai untuk menambah pembagi
          if($nilai_peringkat_smt_2 > 0){
            $pembagi += 1;
          }

          //ambil peringkat dan jumlah siswa
          $data_peringkat_smt_3 = DB::table('peringkat')
          ->select('peringkat', 'jumlah_siswa')
          ->where('no_pendaftar', $value->no_pendaftar)
          ->where('semester', 3)
          ->get();

          //bagi peringkat dengan jumlah siswa
          if (count($data_peringkat_smt_3) > 0) {
            $nilai_peringkat_smt_3 = $data_peringkat_smt_3[0]->peringkat / $data_peringkat_smt_3[0]->jumlah_siswa;
          } else {
            $nilai_peringkat_smt_3 = 0;
          }

          //cek nilai untuk menambah pembagi
          if($nilai_peringkat_smt_3 > 0){
            $pembagi += 1;
          }

          //ambil peringkat dan jumlah siswa
          $data_peringkat_smt_4 = DB::table('peringkat')
          ->select('peringkat', 'jumlah_siswa')
          ->where('no_pendaftar', $value->no_pendaftar)
          ->where('semester', 4)
          ->get();

          //bagi peringkat dengan jumlah siswa
          if (count($data_peringkat_smt_4) > 0) {
            $nilai_peringkat_smt_4 = $data_peringkat_smt_4[0]->peringkat / $data_peringkat_smt_4[0]->jumlah_siswa;
          } else {
            $nilai_peringkat_smt_4 = 0;
          }

          //cek nilai untuk menambah pembagi
          if($nilai_peringkat_smt_4 > 0){
            $pembagi += 1;
          }

          //ambil peringkat dan jumlah siswa
          $data_peringkat_smt_5 = DB::table('peringkat')
          ->select('peringkat', 'jumlah_siswa')
          ->where('no_pendaftar', $value->no_pendaftar)
          ->where('semester', 5)
          ->get();

          //bagi peringkat dengan jumlah siswa
          if (count($data_peringkat_smt_5) > 0) {
            $nilai_peringkat_smt_5 = $data_peringkat_smt_5[0]->peringkat / $data_peringkat_smt_5[0]->jumlah_siswa;
          } else {
            $nilai_peringkat_smt_5 = 0;
          }

          //cek nilai untuk menambah pembagi
          if($nilai_peringkat_smt_5 > 0){
            $pembagi += 1;
          }

          if($pembagi > 5){
            $pembagi = 5;
          }

          $nilai_peringkat_mhs = ($nilai_peringkat_smt_1 + $nilai_peringkat_smt_2 + $nilai_peringkat_smt_3
          + $nilai_peringkat_smt_4 + $nilai_peringkat_smt_5) / $pembagi;

          try {
            //insert nilai peringkat
            mahasiswa::where('no_pendaftar', $value->no_pendaftar)
            ->update(['nilai_peringkat' => $nilai_peringkat_mhs]);
          } catch (\Illuminate\Database\QueryException $ex) {
            return Redirect::back()->withErrors('Gagal menyimpan data peringkat mahasiswa.');
            //$ex->getMessage();
          }
        }
        unset($value);
      });
      $memory3 = (memory_get_usage()) / (1024 ** 2);
    }

    //cek data
    if(DB::table('nilai_non_akademis')->count() > 0){
      //reset nilai prestasi jika melakukan olah data lagi, agar nilai tidak terakumulasi
      mahasiswa::where('periode', '2017')//where('periode', date('Y'))
      ->chunk(1000, function($reset){
        foreach ($reset as $value) {
          set_time_limit(0);
          $value->nilai_non_akademis = 0;
          $value->save();
        }
        unset($value);
      });
      //olah data prestasi
      nilai_non_akademis::chunk(1000, function($lomba){
        foreach ($lomba as $prestasi) {
          set_time_limit(0);
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

          //ambil nilai non akademis dari mahasiswa, tambahkan dengan nilai sebelumnya, lalu save
          $mahasiswa = mahasiswa::where('no_pendaftar', '=', $prestasi->no_pendaftar)->value('nilai_non_akademis');
          $mahasiswa = $mahasiswa + $nilai_prestasi;
          try {
            $mhs = mahasiswa::where('no_pendaftar', '=', $prestasi->no_pendaftar)
            ->update(['nilai_non_akademis' => $mahasiswa]);
          } catch(\Illuminate\Database\QueryException $ex){
            return Redirect::back()->withErrors('Gagal menyimpan data prestasi mahasiswa.');
            //$ex->getMessage();
            // Note any method of class PDOException can be called on $ex.
          }
        }
        unset($prestasi);
      });
      $memory4 = (memory_get_usage()) / (1024 ** 2);
    } else {
      return Response::json([
        'fail' => 1,
        'input' => 'Tidak ada data.',
        'message' => 'Data prestasi tidak ditemukan.'
      ]);
    }
    $total_memory = $memory1 + $memory2 + $memory3 + $memory4;

    //return success
    $response = array(
      'fail' => 0,
      'input' => 'Success',
      'message' => 'Data Pendaftar telah dinormalisasi',
      'memory' => $total_memory
    );
    return Response::json($response);
  }
}
