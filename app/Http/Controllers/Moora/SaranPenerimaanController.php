<?php

namespace App\Http\Controllers\Moora;

ini_set('max_execution_time', '1800');
ini_set('memory_limit', '512M');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
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
use Yajra\Datatables\Datatables;

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
      ->select('kuota_sma', 'kuota_smk')
      ->where('kode_prodi', $id)
      ->get();

      $data_saran = DB::table('saran_penerimaan')
      ->join('mahasiswa', 'saran_penerimaan.no_pendaftar', '=', 'mahasiswa.no_pendaftar')
      ->select('saran_penerimaan.no_pendaftar', 'mahasiswa.nama', 'mahasiswa.jenis_kelamin',
      'mahasiswa.tipe_sekolah', 'mahasiswa.jurusan_asal', 'mahasiswa.pekerjaan_ayah',
      'mahasiswa.pendapatan_ayah', 'mahasiswa.pekerjaan_ibu', 'mahasiswa.pendapatan_ibu',
      'mahasiswa.jumlah_tanggungan', 'mahasiswa.bidik_misi',
      'mahasiswa.nilai_akhir', 'saran_penerimaan.ranking')
      //->where('mahasiswa.periode', date('Y'))
      ->where('mahasiswa.periode', '2017')
      ->where('saran_penerimaan.kode_prodi', $id)
      ->get();

      $data_saran = $data_saran->sortBy('ranking', SORT_NATURAL, true);
    } catch (\Illuminate\Database\QueryException $ex) {
      return Response::json($ex->getMessage());
    }

    $data_saran->transform(function($data){
      return array_dot($data);
    });

    return Datatables::of($data_saran)->make(true);
    /*$response = array(
      'sma' => $data_prodi[0]->kuota_sma,
      'smk' => $data_prodi[0]->kuota_smk,
      'saran' => $data_saran,
    );
    return Response::json($response);*/
    //var_dump($response);
  }

  //fungsi untuk menghasilkan saran penerimaan dengan metode moora
  public function saranPenerimaan(){
    //periksa CR
    $nilai_cr = $this->ahpService->hitungConsistency();
    if ($nilai_cr['cr'] == -1) {
      $response = array(
        'fail' => 1,
        'input' => 'Tabel perbandingan belum diisi',
        'message' => 'Tidak bisa periksa consistency bila tabel ada yang kosong',
      );
      return Response::json($response);
    } elseif ($nilai_cr['cr'] > 0.1) {
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
    //->where('periode', date('Y'))
    ->where('periode', '2017')
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
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->get();
    //initiate value sum of square nilai_akademis
    $sum_sqr_nilai_akademis = 0;
    foreach ($get_nilai_akademis as $value) {
      set_time_limit(0);
      $sum_sqr_nilai_akademis = $sum_sqr_nilai_akademis + ($value->nilai_akademis ** 2);
    }
    //define denominator for nilai_akademis
    $sqrt_nilai_akademis = sqrt($sum_sqr_nilai_akademis);

    $get_nilai_non_akademis = DB::table('mahasiswa')
    ->select('nilai_non_akademis')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->get();
    //initiate value sum of square nilai_non_akademis
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
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->get();
    //initiate value sum of square akreditasi
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

    $get_nilai_peringkat = DB::table('mahasiswa')
    ->select('nilai_peringkat')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->get();
    //initiate value sum of square peringkat
    $sum_sqr_peringkat = 0;
    foreach ($get_nilai_peringkat as $value) {
      set_time_limit(0);
      $sum_sqr_peringkat = $sum_sqr_peringkat + ($value->nilai_peringkat ** 2);
    }
    //define denominator of peringkat
    $sqrt_peringkat = sqrt($sum_sqr_peringkat);

    //normalisasi tiap nilai kriteria tiap mahasiswa dengan denominator masing-masing
    $data_mhs = DB::table('mahasiswa')
    ->select('no_pendaftar', 'nilai_akademis', 'nilai_non_akademis', 'akreditasi_sekolah', 'nilai_peringkat')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->orderBy('no_pendaftar', 'asc')
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

      //data kriteria maksimal
      /*$data_normalisasi[$value->no_pendaftar] = array(
        'Nilai Akademis' => $value->nilai_akademis/$sqrt_nilai_akademis,
        'Nilai Non Akademis' => $value->nilai_non_akademis/$sqrt_nilai_non_akademis,
        'Akreditasi Sekolah' => $akr / $sqrt_akreditasi_sekolah,
      );*/

      //data akademis
      $data_akademis[$value->no_pendaftar] = $value->nilai_akademis / $sqrt_nilai_akademis;

      //data Prestasi
      $data_prestasi[$value->no_pendaftar] = $value->nilai_non_akademis/$sqrt_nilai_non_akademis;

      //data akreditasi
      $data_akreditasi[$value->no_pendaftar] = $akr / $sqrt_akreditasi_sekolah;

      //data peringkat
      $data_peringkat[$value->no_pendaftar] = $value->nilai_peringkat / $sqrt_peringkat;
    }

    //ambil bobot
    /*$get_bobot = DB::table('kriteria')
    ->select('nama_kriteria', 'bobot_kriteria')
    ->where('nama_kriteria', '!=', 'Ranking Raport')
    ->orderBy('id_kriteria')
    ->get();

    //simpan bobot dalam array
    foreach ($get_bobot as $value) {
      set_time_limit(0);
      $bobot[$value->nama_kriteria] = $value->bobot_kriteria;
    }
    //var_dump($bobot);

    //mulai proses optimisasi
    //total nilai kriteria maksimal
    foreach ($data_normalisasi as $key => $value) {
      set_time_limit(0);
      $optimize_result[$key] = 0;
      foreach ($value as $k => $v) {
        set_time_limit(0);
        $optimize_result[$key] += ($v * $bobot[$k]);
      }
    }*/

    //ambil bobot masing-masing kriteria
    $bobot_akademis = DB::table('kriteria')
    ->select('bobot_kriteria')
    ->where('nama_kriteria', 'Nilai Akademis')
    ->get();

    $bobot_prestasi = DB::table('kriteria')
    ->select('bobot_kriteria')
    ->where('nama_kriteria', 'Nilai Non Akademis')
    ->get();

    $bobot_akreditasi = DB::table('kriteria')
    ->select('bobot_kriteria')
    ->where('nama_kriteria', 'Akreditasi Sekolah')
    ->get();

    $bobot_peringkat = DB::table('kriteria')
    ->select('bobot_kriteria')
    ->where('nama_kriteria', 'Ranking Raport')
    ->get();

    //kali kriteria dengan bobotnya
    //nilai akademis
    foreach ($data_akademis as $no_pendaftar => $nilai) {
      $nilai = $nilai * $bobot_akademis[0]->bobot_kriteria;
      $nilai_akhir_akademis[$no_pendaftar] = $nilai;
    }
    unset($nilai);

    //nilai prestasi
    foreach ($data_prestasi as $no_pendaftar2 => $nilai2) {
      $nilai2 = $nilai2 * $bobot_prestasi[0]->bobot_kriteria;
      $nilai_akhir_prestasi[$no_pendaftar2] = $nilai2;
    }
    unset($nilai2);

    //nilai akreditasi
    foreach ($data_akreditasi as $no_pendaftar3 => $nilai3) {
      $nilai3 = $nilai3 * $bobot_akreditasi[0]->bobot_kriteria;
      $nilai_akhir_akreditasi[$no_pendaftar3] = $nilai3;
    }
    unset($nilai3);

    //nilai peringkat
    foreach ($data_peringkat as $no_pendaftar4 => $nilai4) {
      $nilai4 = $nilai4 * $bobot_peringkat[0]->bobot_kriteria;
      $nilai_akhir_peringkat[$no_pendaftar4] = $nilai4;
    }
    unset($nilai4);

    //proses optimisasi per mahasiswa
    $daftar_mhs = DB::table('mahasiswa')
    ->select('no_pendaftar')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->orderBy('no_pendaftar', 'asc')
    ->get();

    foreach ($daftar_mhs as $mhs) {
      $optimize_result[$mhs->no_pendaftar] = ($nilai_akhir_akademis[$mhs->no_pendaftar] + $nilai_akhir_prestasi[$mhs->no_pendaftar] + $nilai_akhir_akreditasi[$mhs->no_pendaftar]) - $nilai_akhir_peringkat[$mhs->no_pendaftar];
    }

    //save nilai akhir ke database
    foreach ($optimize_result as $key => $value) {
      set_time_limit(0);
      //var_dump($key.' => '.$value);
      try {
        $mhs = mahasiswa::where('no_pendaftar', $key)
        //->where('periode', date('Y'))
        ->update(['nilai_akhir' => $value]);
      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors('Gagal menyimpan data nilai akhir '.$ex->getMessage());
      }
    }

    //perankingan mahasiswa per Prodi
    //tiap jurusan disendirikan karena kondisi khusus
    //jurusan teknik => nilai matematika >= 70
    //jurusan bisnis => nilai bhs inggris >= 70
    $this->alokasiProdiSMA();
    $this->alokasiProdiSMK();
    /*$this->rankProdiTeknik();
    $this->rankProdiBisnis();
    $this->rankProdiAkuntansi();*/

    date_default_timezone_set('Asia/Jakarta');
    $tglnow = date('d-m-y H-i-s');
    //tempat penyimpanan data perhitungan
    $filename = 'Perhitungan Moora '.$tglnow;
    $path = 'uploads\Data Perhitungan\Perhitungan Moora';
    $dl_path = $path.'\\'.$filename.'.xlsx';

    //print data perhitungan ke file Excel
    Excel::create($filename, function($excel) use($data_mhs, $sqrt_nilai_akademis, $sqrt_nilai_non_akademis,
    $sqrt_akreditasi_sekolah, $sqrt_peringkat, $daftar_mhs, $data_akademis, $data_prestasi, $data_akreditasi, $data_peringkat,
    $nilai_akhir_akademis, $nilai_akhir_prestasi, $nilai_akhir_akreditasi, $nilai_akhir_peringkat, $optimize_result){
      //perhitungan moora
      $excel->sheet('Matriks Keputusan', function($sheet) use($data_mhs){
        //matriks keputusan
        $sheetHeader1 = array();
        $sheetHeader1[] = array('Nomor Pendaftar', 'Nilai Akademis', 'Nilai Prestasi', 'Akreditasi Sekolah', 'Ranking Raport');
        foreach ($data_mhs as $row) {
          $sheetHeader1[] = array($row->no_pendaftar, $row->nilai_akademis, $row->nilai_non_akademis, $row->akreditasi_sekolah, $row->nilai_peringkat);
        }

        //format kolom excel
        $sheet->setColumnFormat(array(
          'B' => 0.00,
          'C' => 0.00,
          'D' => 0.00,
          'E' => 0.00,
        ));

        $sheet->fromArray($sheetHeader1, null, 'A1', true, false);
      });

      //denominator
      $excel->sheet('Denominator', function($sheet) use($sqrt_nilai_akademis, $sqrt_nilai_non_akademis,
      $sqrt_akreditasi_sekolah, $sqrt_peringkat){
        //inisiasi array sheet
        $sheetArray = array();

        //header
        $sheetArray[] = array('Denominator');

        //denominator nilai akademis
        $sheetArray[] = array('Nilai Akademis', $sqrt_nilai_akademis);

        //denominator nilai non akademis
        $sheetArray[] = array('Nilai Prestasi', $sqrt_nilai_non_akademis);

        //denominator akreditasi
        $sheetArray[] = array('Akreditasi Sekolah', $sqrt_akreditasi_sekolah);

        //denominator Ranking
        $sheetArray[] = array('Ranking Raport', $sqrt_peringkat);

        //format kolom excel
        $sheet->setColumnFormat(array(
          'B' => 0.00,
          'C' => 0.00,
          'D' => 0.00,
          'E' => 0.00,
        ));

        $sheet->fromArray($sheetArray, null, 'A1', true, false);
      });

      //hasil normalisasi
      $excel->sheet('Hasil Normalisasi', function($sheet2) use($daftar_mhs, $data_akademis, $data_prestasi,
      $data_akreditasi, $data_peringkat){
        //inisiasi sheet array
        $sheetArray2 = array();

        //header
        $sheetArray2[] = array('Nomor Pendaftar', 'Nilai Akademis', 'Nilai Prestasi', 'Akreditasi Sekolah', 'Ranking Raport');

        //nilai normalisasi
        foreach ($daftar_mhs as $mhs) {
          $sheetArray2[] = array($mhs->no_pendaftar, $data_akademis[$mhs->no_pendaftar], $data_prestasi[$mhs->no_pendaftar], $data_akreditasi[$mhs->no_pendaftar],
          $data_peringkat[$mhs->no_pendaftar]);
        }

        //format kolom excel
        $sheet2->setColumnFormat(array(
          'B' => 0.00,
          'C' => 0.00,
          'D' => 0.00,
          'E' => 0.00,
        ));

        $sheet2->fromArray($sheetArray2, null, 'A1', true, false);
      });

      //nilai akhir
      $excel->sheet('Hasil Akhir', function($sheet3) use($daftar_mhs, $nilai_akhir_akademis, $nilai_akhir_prestasi,
      $nilai_akhir_akreditasi, $nilai_akhir_peringkat, $optimize_result){
        //inisiasi sheet array
        $sheetArray3 = array();

        //header
        $sheetArray3[] = array('Nomor Pendaftar', 'Nilai Akademis', 'Nilai Prestasi', 'Akreditasi Sekolah',
        'Ranking Raport', 'Nilai Akhir');

        //nilai normalisasi x bobot dan nilai akhir
        foreach ($daftar_mhs as $mhs) {
          $sheetArray3[] = array($mhs->no_pendaftar, $nilai_akhir_akademis[$mhs->no_pendaftar], $nilai_akhir_prestasi[$mhs->no_pendaftar],
          $nilai_akhir_akreditasi[$mhs->no_pendaftar], $nilai_akhir_peringkat[$mhs->no_pendaftar], $optimize_result[$mhs->no_pendaftar]);
        }

        //format kolom excel
        $sheet3->setColumnFormat(array(
          'B' => 0.00,
          'C' => 0.00,
          'D' => 0.00,
          'E' => 0.00,
          'F' => 0.00,
        ));

        $sheet3->fromArray($sheetArray3, null, 'A1', true, false);
      });
    })->store('xlsx', $path);

    $response = array(
      'fail' => 0,
      'input' => 'Success',
      'message' => 'Saran Penerimaan telah dihasilkan',
      'mooraUrl' => $dl_path,
      'AHPurl' => $nilai_cr['dl_path']
    );
    return Response::json($response);
  }

  public function cetakDataPenerimaan(){
    date_default_timezone_set('Asia/Jakarta');
    $tglnow = date('d-m-y H-i-s');

    $filename = 'Data Penerimaan '.$tglnow.'.xlsx';
    $path = 'Data Penerimaan/';
    $dl_path = storage_path('Data Penerimaan/'.$filename.'.xlsx');

    try {
      $data_prodi = DB::table('prodi')
      ->select('kode_prodi', 'nama_prodi')
      ->get();
    } catch (\Illuminate\Database\QueryException $ex) {
      return Response::json($ex->getMessage());
    }

    Excel::create('Data Saran Penerimaan '.$tglnow, function($excel) use($data_prodi){
      //cetak tiap prodi dalam sheet
      foreach ($data_prodi as $prodi) {
        $excel->sheet($prodi->kode_prodi, function($sheet) use($prodi){
          //ambil data dari database
          try {
            $data_cetak = DB::table('saran_penerimaan')
            ->join('mahasiswa', 'saran_penerimaan.no_pendaftar', '=', 'mahasiswa.no_pendaftar')
            ->join('pilihan_mhs', 'saran_penerimaan.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
            ->select('saran_penerimaan.no_pendaftar', 'mahasiswa.nama', 'mahasiswa.jenis_kelamin',
            'mahasiswa.tipe_sekolah', 'mahasiswa.jurusan_asal', 'mahasiswa.pekerjaan_ayah',
            'mahasiswa.pendapatan_ayah', 'mahasiswa.pekerjaan_ibu', 'mahasiswa.pendapatan_ibu',
            'mahasiswa.jumlah_tanggungan', 'mahasiswa.bidik_misi', 'pilihan_mhs.pilihan_ke',
            'mahasiswa.nilai_akademis', 'mahasiswa.nilai_non_akademis', 'mahasiswa.akreditasi_sekolah',
            'mahasiswa.nilai_peringkat', 'mahasiswa.nilai_akhir', 'saran_penerimaan.ranking')
            ->where('saran_penerimaan.kode_prodi', $prodi->kode_prodi)
            //->where('mahasiswa.periode', date('Y'))
            ->where('mahasiswa.periode', '2017')
            ->where('pilihan_mhs.pilihan_prodi', $prodi->kode_prodi)
            ->orderBy('mahasiswa.tipe_sekolah')
            ->orderBy('saran_penerimaan.ranking')
            ->get();
          } catch (\Illuminate\Database\QueryException $ex) {
            return Response::json($ex->getMessage());
          }

          //$data_cetak = $data_cetak->sortBy('ranking', SORT_NATURAL, true);

          //inisiasi sheet array
          $sheetArray = array();

          //header tiap sheet
          $sheetArray[] = array('Nomor Pendaftar', 'Nama', 'Jenis Kelamin', 'Tipe Sekolah', 'Jurusan Asal',
          'Pekerjaan Ayah', 'Pendapatan Ayah', 'Pekerjaan Ibu', 'Pendapatan Ibu', 'Jumlah Tanggungan',
          'Bidik Misi', 'Pilihan ke', 'Nilai Akademis', 'Nilai Prestasi', 'Akreditasi Sekolah',
          'Rerata Peringkat', 'Nilai Akhir', 'Rank');

          //cetak isi dari database
          foreach ($data_cetak as $cetak) {
            $sheetArray[] = array($cetak->no_pendaftar, $cetak->nama, $cetak->jenis_kelamin, $cetak->tipe_sekolah,
            $cetak->jurusan_asal, $cetak->pekerjaan_ayah, $cetak->pendapatan_ayah, $cetak->pekerjaan_ibu,
            $cetak->pendapatan_ibu, $cetak->jumlah_tanggungan, $cetak->bidik_misi, $cetak->pilihan_ke,
            $cetak->nilai_akademis, $cetak->nilai_non_akademis, $cetak->akreditasi_sekolah, $cetak->nilai_peringkat,
            $cetak->nilai_akhir, $cetak->ranking);
          }

          $sheet->fromArray($sheetArray, null, 'A1', true, false);
        });
      }

      //rank calon mahasiswa
      $excel->sheet('Rank Seluruh Calon Mahasiswa', function($sheet){
        $nilai_mhs = DB::table('mahasiswa')
        ->select('no_pendaftar', 'nilai_akhir')
        //->where('periode', date('Y'))
        ->where('periode', '2017')
        ->get();

        $rank_mhs = array();

        foreach ($nilai_mhs as $value) {
          set_time_limit(0);
          $rank_mhs[$value->no_pendaftar] = $value->nilai_akhir;
        }
        arsort($rank_mhs);

        $sheetHeader[] = array();
        $sheetHeader[] = array('Nomor Pendaftar', 'Nama', 'Tipe Sekolah', 'Nilai Akademis', 'Nilai Prestasi',
        'Akreditasi Sekolah', 'Rerata Peringkat', 'Nilai Akhir', 'Rank');
        $rank = 1;

        foreach ($rank_mhs as $no_pendaftar => $nilai_akhir) {
          $mahasiswa = DB::table('mahasiswa')
          ->select('nama', 'tipe_sekolah', 'nilai_akademis', 'nilai_non_akademis', 'akreditasi_sekolah',
          'nilai_peringkat', 'nilai_akhir')
          ->where('no_pendaftar', $no_pendaftar)
          //->where('periode', date('Y'))
          ->where('periode', '2017')
          ->get();

          $sheetHeader[] = array($no_pendaftar, $mahasiswa[0]->nama, $mahasiswa[0]->tipe_sekolah, $mahasiswa[0]->nilai_akademis,
          $mahasiswa[0]->nilai_non_akademis, $mahasiswa[0]->akreditasi_sekolah, $mahasiswa[0]->nilai_peringkat,
          $mahasiswa[0]->nilai_akhir, $rank);
          $rank++;
        }
        $sheet->fromArray($sheetHeader, null, 'A1', true, false);
      });
    })->export('xlsx');

    //$headers = array('Content-Type'=> 'application/xlsx');
    //return response()->download($dl_path, $filename, $headers);
    //return Response::json($dl_path);
  }

  //ranking SMA
  public function alokasiProdiSMA(){
    //ambil data semua calon mahasiswa dari sma
    $data_mhs_sma = DB::table('mahasiswa')
    ->select('no_pendaftar', 'nilai_akhir')
    ->where('tipe_sekolah', 'like', 'SMA%')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->get();

    //inisiasi array untuk mengurutkan ranking
    $rank_sma = array();

    //urutkan berdasarkan nilai akhir terbesar
    foreach ($data_mhs_sma as $value) {
      set_time_limit(0);
      $rank_sma[$value->no_pendaftar] = $value->nilai_akhir;
    }
    arsort($rank_sma);

    /*foreach ($rank_sma as $key => $value) {
      var_dump($key.' => '.$value.'<br>');
    }*/
    //$iterate = 1;

    //proses ranking selesai, mulai alokasi tiap calon mahasiswa ke prodi pilihan
    foreach (array_chunk($rank_sma, 500, true) as $mhs) {
      set_time_limit(0);
      //tes
      //var_dump(key($mhs));
      //iterasi nilai dalam chunk
      foreach ($mhs as $no_pendaftar => $nilai_akhir) {
        //var_dump($no_pendaftar.' => '.$nilai_akhir);
        //ambil data prodi pilihan dan kuota sma prodi
        $prodi_pilihan = DB::table('pilihan_mhs')
        ->join('prodi', 'pilihan_mhs.pilihan_prodi', '=', 'prodi.kode_prodi')
        ->select('pilihan_mhs.pilihan_prodi', 'prodi.kuota_sma')
        ->where('pilihan_mhs.no_pendaftar', $no_pendaftar)
        ->orderBy('pilihan_mhs.pilihan_ke', 'asc')
        ->get();

        //var_dump($iterate);
        //$iterate++;

        //cek mahasiswa yang sudah dialokasi di prodi, kalau penuh, ke pilihan 2
        foreach ($prodi_pilihan as $prodi) {
          set_time_limit(0);
          //cek prodi teknik, bisnis, atau akuntansi
          if (preg_match('/^31|^32|^33|^41|^42|^43/i', $prodi->pilihan_prodi)) {
            //untuk prodi pilihan teknik, cek nilai matematika
            $cek_nilai_matematika = DB::table('nilai_akademis')
            ->select('nilai_mapel_koreksi')
            ->where('no_pendaftar', $no_pendaftar)
            ->where('mapel', 'like', 'matematika%')
            ->where('nilai_mapel_koreksi', '<', '70')
            ->first();

            if (!$cek_nilai_matematika) {
              //nilai matematika lebih dari 70, lanjut ke alokasi prodi
              //cek ranking terbesar
              $max_rank = DB::table('saran_penerimaan')
              ->where('kode_prodi', $prodi->pilihan_prodi)
              ->where('tipe_sekolah', 'like', 'SMA%')
              ->max(DB::raw("cast(ranking as int)"));
              //var_dump($max_rank);

              //cek hasil query kosong atau tidak
              if ($max_rank) {
                //var_dump('cek max rank ada => '.$max_rank);
                //bandingkan ranking max dan kuota
                if($max_rank < $prodi->kuota_sma){
                  //var_dump('lolos if max rank < kuota sma');
                  $periode = DB::table('mahasiswa')
                  ->select('tipe_sekolah', 'periode')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->get();

                  //cek data sudah ada atau belum
                  $cek_sama = DB::table('saran_penerimaan')
                  ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->where('kode_prodi', $prodi->pilihan_prodi)
                  ->where('tipe_sekolah', $periode[0]->tipe_sekolah)
                  ->where('periode', $periode[0]->periode)
                  ->count();

                  //jika tidak ada, tambahkan
                  if ($cek_sama == 0) {
                    $saran = new saran_penerimaan();
                    $saran->no_pendaftar = $no_pendaftar;
                    $saran->kode_prodi = $prodi->pilihan_prodi;
                    $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                    $saran->periode = $periode[0]->periode;
                    $saran->ranking = $max_rank + 1;
                    if (!$saran->save()) {
                      return Redirect::back()->withErrors('The server encountered an unexpected condition');
                    }
                    //jika masuk ke prodi, maka break
                    break;
                  }
                }
              } else {
                $periode = DB::table('mahasiswa')
                ->select('tipe_sekolah', 'periode')
                ->where('no_pendaftar', $no_pendaftar)
                ->get();

                //jika max rank kosong, tambah baru
                $saran = new saran_penerimaan();
                $saran->no_pendaftar = $no_pendaftar;
                $saran->kode_prodi = $prodi->pilihan_prodi;
                $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                $saran->periode = $periode[0]->periode;
                $saran->ranking = 1;
                if (!$saran->save()) {
                  return Redirect::back()->withErrors('The server encountered an unexpected condition');
                }
                //jika masuk ke prodi, maka break
                break;
              }
            }
          //cek prodi bisnis
        } elseif (preg_match('/^35|^45/i', $prodi->pilihan_prodi)) {
            //untuk prodi bisnis, cek nilai bahasa inggris
            $cek_nilai_inggris = DB::table('nilai_akademis')
            ->select('nilai_mapel_koreksi')
            ->where('no_pendaftar', $no_pendaftar)
            ->where('mapel', 'like', '%inggris%')
            ->where('nilai_mapel_koreksi', '<', '70')
            ->first();

            if (!$cek_nilai_inggris) {
              //jika nilai inggris lebih dari 70 semua, lanjut ke alokasi prodi
              //cek ranking terbesar
              $max_rank = DB::table('saran_penerimaan')
              ->where('kode_prodi', $prodi->pilihan_prodi)
              ->where('tipe_sekolah', 'like', 'SMA%')
              ->max(DB::raw("cast(ranking as int)"));
              var_dump($max_rank);

              //cek hasil query kosong atau tidak
              if ($max_rank) {
                //bandingkan ranking max dan kuota
                if($max_rank < $prodi->kuota_sma){
                  $periode = DB::table('mahasiswa')
                  ->select('tipe_sekolah', 'periode')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->get();

                  //cek data sudah ada atau belum
                  $cek_sama = DB::table('saran_penerimaan')
                  ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->where('kode_prodi', $prodi->pilihan_prodi)
                  ->where('tipe_sekolah', $periode[0]->tipe_sekolah)
                  ->where('periode', $periode[0]->periode)
                  ->count();

                  //jika tidak ada, tambahkan
                  if ($cek_sama == 0) {
                    $saran = new saran_penerimaan();
                    $saran->no_pendaftar = $no_pendaftar;
                    $saran->kode_prodi = $prodi->pilihan_prodi;
                    $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                    $saran->periode = $periode[0]->periode;
                    $saran->ranking = $max_rank + 1;
                    if (!$saran->save()) {
                      return Redirect::back()->withErrors('The server encountered an unexpected condition');
                    }
                    //jika masuk ke prodi, maka break
                    break;
                  }
                }
              } else {
                $periode = DB::table('mahasiswa')
                ->select('tipe_sekolah', 'periode')
                ->where('no_pendaftar', $no_pendaftar)
                ->get();

                //jika max rank kosong, tambah baru
                $saran = new saran_penerimaan();
                $saran->no_pendaftar = $no_pendaftar;
                $saran->kode_prodi = $prodi->pilihan_prodi;
                $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                $saran->periode = $periode[0]->periode;
                $saran->ranking = 1;
                if (!$saran->save()) {
                  return Redirect::back()->withErrors('The server encountered an unexpected condition');
                }
                //jika masuk ke prodi, maka break
                break;
              }
            }
          //prodi akuntansi
          } else {
            //cek ranking terbesar
            $max_rank = DB::table('saran_penerimaan')
            ->where('kode_prodi', $prodi->pilihan_prodi)
            ->where('tipe_sekolah', 'like', 'SMA%')
            ->max(DB::raw("cast(ranking as int)"));
            //var_dump($max_rank);

            //cek hasil query kosong atau tidak
            if ($max_rank) {
              //bandingkan ranking max dan kuota
              if($max_rank < $prodi->kuota_sma){
                $periode = DB::table('mahasiswa')
                ->select('tipe_sekolah', 'periode')
                ->where('no_pendaftar', $no_pendaftar)
                ->get();

                //cek data sudah ada atau belum
                $cek_sama = DB::table('saran_penerimaan')
                ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
                ->where('no_pendaftar', $no_pendaftar)
                ->where('kode_prodi', $prodi->pilihan_prodi)
                ->where('tipe_sekolah', $periode[0]->tipe_sekolah)
                ->where('periode', $periode[0]->periode)
                ->count();

                //jika tidak ada, tambahkan
                if ($cek_sama == 0) {
                  $saran = new saran_penerimaan();
                  $saran->no_pendaftar = $no_pendaftar;
                  $saran->kode_prodi = $prodi->pilihan_prodi;
                  $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                  $saran->periode = $periode[0]->periode;
                  $saran->ranking = $max_rank + 1;
                  if (!$saran->save()) {
                    return Redirect::back()->withErrors('The server encountered an unexpected condition');
                  }
                  //jika masuk ke prodi, maka break
                  break;
                }
              }
            } else {
              $periode = DB::table('mahasiswa')
              ->select('tipe_sekolah', 'periode')
              ->where('no_pendaftar', $no_pendaftar)
              ->get();

              //jika max rank kosong, tambah baru
              $saran = new saran_penerimaan();
              $saran->no_pendaftar = $no_pendaftar;
              $saran->kode_prodi = $prodi->pilihan_prodi;
              $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
              $saran->periode = $periode[0]->periode;
              $saran->ranking = 1;
              if (!$saran->save()) {
                return Redirect::back()->withErrors('The server encountered an unexpected condition');
              }
              //jika masuk ke prodi, maka break
              break;
            }
          }
        }
      }
      unset($prodi);
      unset($no_pendaftar);
      unset($nilai_akhir);
    }
    unset($mhs);
  }

  //ranking SMK
  public function alokasiProdiSMK(){
    //ambil data semua calon mahasiswa dari sma
    $data_mhs_smk = DB::table('mahasiswa')
    ->select('no_pendaftar', 'nilai_akhir')
    ->where('tipe_sekolah', 'like', 'SMK%')
    //->where('periode', date('Y'))
    ->where('periode', '2017')
    ->get();

    //inisiasi array untuk mengurutkan ranking
    $rank_smk = array();

    //urutkan berdasarkan nilai akhir terbesar
    foreach ($data_mhs_smk as $value) {
      set_time_limit(0);
      $rank_smk[$value->no_pendaftar] = $value->nilai_akhir;
    }
    arsort($rank_smk);
    //$iterate = 1;

    //proses ranking selesai, mulai alokasi tiap calon mahasiswa ke prodi pilihan
    foreach (array_chunk($rank_smk, 500, true) as $mhs) {
      set_time_limit(0);
      //tes
      //var_dump($no_pendaftar.' => '.$nilai_akhir);
      foreach ($mhs as $no_pendaftar => $nilai_akhir) {
        //ambil data prodi pilihan dan kuota smk prodi
        $prodi_pilihan = DB::table('pilihan_mhs')
        ->join('prodi', 'pilihan_mhs.pilihan_prodi', '=', 'prodi.kode_prodi')
        ->select('pilihan_mhs.pilihan_prodi', 'prodi.kuota_smk')
        ->where('pilihan_mhs.no_pendaftar', $no_pendaftar)
        ->orderBy('pilihan_mhs.pilihan_ke', 'asc')
        ->get();

        //var_dump($iterate);
        //$iterate++;

        //cek mahasiswa yang sudah dialokasi di prodi, kalau penuh, ke pilihan 2
        foreach ($prodi_pilihan as $prodi) {
          set_time_limit(0);
          //cek prodi teknik, bisnis, atau akuntansi
          if (preg_match('/^31|^32|^33|^41|^42|^43/i', $prodi->pilihan_prodi)) {
            //untuk prodi pilihan teknik, cek nilai matematika
            $cek_nilai_matematika = DB::table('nilai_akademis')
            ->select('nilai_mapel_koreksi')
            ->where('no_pendaftar', $no_pendaftar)
            ->where('mapel', 'like', 'matematika%')
            ->where('nilai_mapel_koreksi', '<', '70')
            ->first();

            if (!$cek_nilai_matematika) {
              //nilai matematika lebih dari 70, lanjut ke alokasi prodi
              //cek ranking terbesar
              $max_rank = DB::table('saran_penerimaan')
              ->where('kode_prodi', $prodi->pilihan_prodi)
              ->where('tipe_sekolah', 'like', 'SMK%')
              ->max(DB::raw("cast(ranking as int)"));

              //cek hasil query kosong atau tidak
              if ($max_rank) {
                //bandingkan ranking max dan kuota
                if($max_rank < $prodi->kuota_smk){
                  $periode = DB::table('mahasiswa')
                  ->select('tipe_sekolah', 'periode')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->get();

                  //cek data sudah ada atau belum
                  $cek_sama = DB::table('saran_penerimaan')
                  ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->where('kode_prodi', $prodi->pilihan_prodi)
                  ->where('tipe_sekolah', $periode[0]->tipe_sekolah)
                  ->where('periode', $periode[0]->periode)
                  ->count();

                  //jika tidak ada, tambahkan
                  if ($cek_sama == 0) {
                    $saran = new saran_penerimaan();
                    $saran->no_pendaftar = $no_pendaftar;
                    $saran->kode_prodi = $prodi->pilihan_prodi;
                    $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                    $saran->periode = $periode[0]->periode;
                    $saran->ranking = $max_rank + 1;
                    if (!$saran->save()) {
                      return Redirect::back()->withErrors('The server encountered an unexpected condition');
                    }
                    //jika masuk ke prodi, maka break
                    break;
                  }
                }
              } else {
                $periode = DB::table('mahasiswa')
                ->select('tipe_sekolah', 'periode')
                ->where('no_pendaftar', $no_pendaftar)
                ->get();

                //jika max rank kosong, tambah baru
                $saran = new saran_penerimaan();
                $saran->no_pendaftar = $no_pendaftar;
                $saran->kode_prodi = $prodi->pilihan_prodi;
                $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                $saran->periode = $periode[0]->periode;
                $saran->ranking = 1;
                if (!$saran->save()) {
                  return Redirect::back()->withErrors('The server encountered an unexpected condition');
                }
                //jika masuk ke prodi, maka break
                break;
              }
            }
          //cek prodi bisnis
        } elseif (preg_match('/^35|^45/i', $prodi->pilihan_prodi)) {
            //untuk prodi bisnis, cek nilai bahasa inggris
            $cek_nilai_inggris = DB::table('nilai_akademis')
            ->select('nilai_mapel_koreksi')
            ->where('no_pendaftar', $no_pendaftar)
            ->where('mapel', 'like', '%inggris%')
            ->where('nilai_mapel_koreksi', '<', '70')
            ->first();

            if (!$cek_nilai_inggris) {
              //jika nilai inggris lebih dari 70 semua, lanjut ke alokasi prodi
              //cek ranking terbesar
              $max_rank = DB::table('saran_penerimaan')
              ->where('kode_prodi', $prodi->pilihan_prodi)
              ->where('tipe_sekolah', 'like', 'SMK%')
              ->max(DB::raw("cast(ranking as int)"));

              //cek hasil query kosong atau tidak
              if ($max_rank) {
                //bandingkan ranking max dan kuota
                if($max_rank < $prodi->kuota_smk){
                  $periode = DB::table('mahasiswa')
                  ->select('tipe_sekolah', 'periode')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->get();

                  //cek data sudah ada atau belum
                  $cek_sama = DB::table('saran_penerimaan')
                  ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
                  ->where('no_pendaftar', $no_pendaftar)
                  ->where('kode_prodi', $prodi->pilihan_prodi)
                  ->where('tipe_sekolah', $periode[0]->tipe_sekolah)
                  ->where('periode', $periode[0]->periode)
                  ->count();

                  //jika tidak ada, tambahkan
                  if ($cek_sama == 0) {
                    $saran = new saran_penerimaan();
                    $saran->no_pendaftar = $no_pendaftar;
                    $saran->kode_prodi = $prodi->pilihan_prodi;
                    $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                    $saran->periode = $periode[0]->periode;
                    $saran->ranking = $max_rank + 1;
                    if (!$saran->save()) {
                      return Redirect::back()->withErrors('The server encountered an unexpected condition');
                    }
                    //jika masuk ke prodi, maka break
                    break;
                  }
                }
              } else {
                $periode = DB::table('mahasiswa')
                ->select('tipe_sekolah', 'periode')
                ->where('no_pendaftar', $no_pendaftar)
                ->get();

                //jika max rank kosong, tambah baru
                $saran = new saran_penerimaan();
                $saran->no_pendaftar = $no_pendaftar;
                $saran->kode_prodi = $prodi->pilihan_prodi;
                $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                $saran->periode = $periode[0]->periode;
                $saran->ranking = 1;
                if (!$saran->save()) {
                  return Redirect::back()->withErrors('The server encountered an unexpected condition');
                }
                //jika masuk ke prodi, maka break
                break;
              }
            }
          //prodi akuntansi
          } else {
            //cek ranking terbesar
            $max_rank = DB::table('saran_penerimaan')
            ->where('kode_prodi', $prodi->pilihan_prodi)
            ->where('tipe_sekolah', 'like', 'SMK%')
            ->max(DB::raw("cast(ranking as int)"));

            //cek hasil query kosong atau tidak
            if ($max_rank) {
              //bandingkan ranking max dan kuota
              if($max_rank < $prodi->kuota_smk){
                $periode = DB::table('mahasiswa')
                ->select('tipe_sekolah', 'periode')
                ->where('no_pendaftar', $no_pendaftar)
                ->get();

                //cek data sudah ada atau belum
                $cek_sama = DB::table('saran_penerimaan')
                ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
                ->where('no_pendaftar', $no_pendaftar)
                ->where('kode_prodi', $prodi->pilihan_prodi)
                ->where('tipe_sekolah', $periode[0]->tipe_sekolah)
                ->where('periode', $periode[0]->periode)
                ->count();

                //jika tidak ada, tambahkan
                if ($cek_sama == 0) {
                  $saran = new saran_penerimaan();
                  $saran->no_pendaftar = $no_pendaftar;
                  $saran->kode_prodi = $prodi->pilihan_prodi;
                  $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
                  $saran->periode = $periode[0]->periode;
                  $saran->ranking = $max_rank + 1;
                  if (!$saran->save()) {
                    return Redirect::back()->withErrors('The server encountered an unexpected condition');
                  }
                  //jika masuk ke prodi, maka break
                  break;
                }
              }
            } else {
              $periode = DB::table('mahasiswa')
              ->select('tipe_sekolah', 'periode')
              ->where('no_pendaftar', $no_pendaftar)
              ->get();

              //jika max rank kosong, tambah baru
              $saran = new saran_penerimaan();
              $saran->no_pendaftar = $no_pendaftar;
              $saran->kode_prodi = $prodi->pilihan_prodi;
              $saran->tipe_sekolah = $periode[0]->tipe_sekolah;
              $saran->periode = $periode[0]->periode;
              $saran->ranking = 1;
              if (!$saran->save()) {
                return Redirect::back()->withErrors('The server encountered an unexpected condition');
              }
              //jika masuk ke prodi, maka break
              break;
            }
          }
        }
      }
      unset($prodi);
      unset($no_pendaftar);
      unset($nilai_akhir);
    }
    unset($mhs);
  }

  //ranking prodi teknik
  /*public function rankProdiTeknik(){
    //lakukan perankingan dengan input ke tabel saran penerimaan dg nilai akhir terbesar
    try {
      //ambil data prodi
      $data_prodi_teknik = DB::table('prodi')
      ->select('kode_prodi')
      ->where('kode_prodi', 'like', '31%')
      ->orwhere('kode_prodi', 'like', '32%')
      ->orwhere('kode_prodi', 'like', '33%')
      ->orwhere('kode_prodi', 'like', '41%')
      ->orwhere('kode_prodi', 'like', '42%')
      ->orwhere('kode_prodi', 'like', '43%')
      ->orderBy('kode_prodi', 'asc')
      ->get();
      //var_dump($data_prodi_teknik);

      //ambil data mhs dari tiap prodi
      //ranking sma
      foreach ($data_prodi_teknik as $value) {
        set_time_limit(0);

        //ambil data mahasiswa dengan nilai matematika >= 70
        //nilai matematika < 70 dinyatakan gugur = tidak diranking
        $data_moora = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->join('nilai_akademis', 'mahasiswa.no_pendaftar', '=', 'nilai_akademis.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->where('mahasiswa.tipe_sekolah', 'like', 'SMA%')
        ->where('nilai_akademis.mapel', 'like', 'matematika%')
        ->where('nilai_akademis.nilai_mapel_koreksi', '>=', '70')
        ->get();

        $rank_sma = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora as $val) {
          set_time_limit(0);
          $rank_sma[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank_sma);

        //ambil kuota sma prodi
        $kuota_sma = DB::table('prodi')
        ->select('kuota_sma', 'kuota_cadangan')
        ->where('kode_prodi', $value->kode_prodi)
        ->get();

        foreach ($kuota_sma as $vals) {
          $sma = $vals->kuota_sma;
          $cadangan = $vals->kuota_cadangan;
        }
        $total_kuota_sma = $sma + $cadangan;

        //perankingan sma
        $i = 1; //counter ranking
        //save ke database
        foreach ($rank_sma as $k => $v) {
          set_time_limit(0);

          $cek_sama = DB::table('saran_penerimaan')
          ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
          ->where('no_pendaftar', $k)
          ->where('kode_prodi', $value->kode_prodi)
          ->where('periode', '2017')
          ->count();

          if ($cek_sama == 0) {
            $saran = new saran_penerimaan();
            $saran->no_pendaftar = $k;
            $saran->kode_prodi = $value->kode_prodi;
            $saran->periode = '2017';
            $saran->ranking = $i;
            if (!$saran->save()) {
              return Redirect::back()->withErrors('The server encountered an unexpected condition');
            }
            $i += 1;
          }

          //jika sudah memenuhi kuota dan cadangan, keluar dari loop
          if ($i == $total_kuota_sma) {
            break;
          }
        }
      }
      unset($value);

      //ranking SMK
      foreach ($data_prodi_teknik as $value) {
        set_time_limit(0);

        $data_moora_smk = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->join('nilai_akademis', 'mahasiswa.no_pendaftar', '=', 'nilai_akademis.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->where('mahasiswa.tipe_sekolah', 'like', 'SMK%')
        ->where('nilai_akademis.mapel', 'like', 'matematika%')
        ->where('nilai_akademis.nilai_mapel_koreksi', '>=', '70')
        ->get();

        $rank_smk = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora_smk as $val) {
          set_time_limit(0);
          $rank_smk[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank_smk);

        //ambil kuota sma prodi
        $kuota_smk = DB::table('prodi')
        ->select('kuota_smk', 'kuota_cadangan')
        ->where('kode_prodi', $value->kode_prodi)
        ->get();

        foreach ($kuota_smk as $vals) {
          $smk = $vals->kuota_smk;
          $cadangan = $vals->kuota_cadangan;
        }
        $total_kuota_smk = $smk + $cadangan;

        //perankingan
        $j = 1; //counter ranking
        //save ke database
        foreach ($rank_smk as $k => $v) {
          set_time_limit(0);

          $cek_smk = DB::table('saran_penerimaan')
          ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
          ->where('no_pendaftar', $k)
          ->where('kode_prodi', $value->kode_prodi)
          ->where('periode', '2017')
          ->count();

          if ($cek_smk == 0) {
            $saran = new saran_penerimaan();
            $saran->no_pendaftar = $k;
            $saran->kode_prodi = $value->kode_prodi;
            $saran->periode = '2017';
            $saran->ranking = $j;
            if (!$saran->save()) {
              return Redirect::back()->withErrors('The server encountered an unexpected condition');
            }
            $j += 1;
          }

          //jika sudah memenuhi kuota dan cadangan, keluar dari loop
          if($j == $total_kuota_smk){
            break;
          }
        }
      }
    } catch (\Illuminate\Database\QueryException $ex) {
      return Redirect::back()->withErrors($ex->getMessage());
    }
  }*/

  //ranking prodi administrasi bisnis
  /*public function rankProdiBisnis(){
    //lakukan perankingan dengan input ke tabel saran penerimaan dg nilai akhir terbesar
    try {
      //ambil data prodi administrasi bisnis
      $data_prodi_bisnis = DB::table('prodi')
      ->select('kode_prodi')
      ->where('kode_prodi', 'like', '35%')
      ->orwhere('kode_prodi', 'like', '45%')
      ->orderBy('kode_prodi', 'asc')
      ->get();
      //var_dump($data_prodi_bisnis);

      //ambil data mhs dari tiap prodi
      //ranking sma
      foreach ($data_prodi_bisnis as $value) {
        set_time_limit(0);

        //ambil data mahasiswa dengan nilai inggris >= 70
        //nilai inggris < 70 dinyatakan gugur = tidak diranking
        $data_moora = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->join('nilai_akademis', 'mahasiswa.no_pendaftar', '=', 'nilai_akademis.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->where('mahasiswa.tipe_sekolah', 'like', 'SMA%')
        ->where('nilai_akademis.mapel', 'like', '%inggris%')
        ->where('nilai_akademis.nilai_mapel_koreksi', '>=', '70')
        ->get();

        $rank_sma = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora as $val) {
          set_time_limit(0);
          $rank_sma[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank_sma);

        //ambil kuota sma prodi
        $kuota_sma = DB::table('prodi')
        ->select('kuota_sma', 'kuota_cadangan')
        ->where('kode_prodi', $value->kode_prodi)
        ->get();

        foreach ($kuota_sma as $vals) {
          $sma = $vals->kuota_sma;
          $cadangan = $vals->kuota_cadangan;
        }
        $total_kuota_sma = $sma + $cadangan;

        //perankingan sma
        $i = 1; //counter ranking
        //save ke database
        foreach ($rank_sma as $k => $v) {
          set_time_limit(0);

          $cek_sama = DB::table('saran_penerimaan')
          ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
          ->where('no_pendaftar', $k)
          ->where('kode_prodi', $value->kode_prodi)
          ->where('periode', '2017')
          ->count();

          if ($cek_sama == 0) {
            $saran = new saran_penerimaan();
            $saran->no_pendaftar = $k;
            $saran->kode_prodi = $value->kode_prodi;
            $saran->periode = '2017';
            $saran->ranking = $i;
            if (!$saran->save()) {
              return Redirect::back()->withErrors('The server encountered an unexpected condition');
            }
            $i += 1;
          }

          //jika sudah memenuhi kuota dan cadangan, keluar dari loop
          if ($i == $total_kuota_sma) {
            break;
          }
        }
      }
      unset($value);

      //ranking SMK
      foreach ($data_prodi_bisnis as $value) {
        set_time_limit(0);

        $data_moora_smk = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->join('nilai_akademis', 'mahasiswa.no_pendaftar', '=', 'nilai_akademis.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->where('mahasiswa.tipe_sekolah', 'like', 'SMK%')
        ->where('nilai_akademis.mapel', 'like', '%inggris%')
        ->where('nilai_akademis.nilai_mapel_koreksi', '>=', '70')
        ->get();

        $rank_smk = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora_smk as $val) {
          set_time_limit(0);
          $rank_smk[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank_smk);

        //ambil kuota sma prodi
        $kuota_smk = DB::table('prodi')
        ->select('kuota_smk', 'kuota_cadangan')
        ->where('kode_prodi', $value->kode_prodi)
        ->get();

        foreach ($kuota_smk as $vals) {
          $smk = $vals->kuota_smk;
          $cadangan = $vals->kuota_cadangan;
        }
        $total_kuota_smk = $smk + $cadangan;

        //perankingan
        $j = 1; //counter ranking
        //save ke database
        foreach ($rank_smk as $k => $v) {
          set_time_limit(0);

          $cek_smk = DB::table('saran_penerimaan')
          ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
          ->where('no_pendaftar', $k)
          ->where('kode_prodi', $value->kode_prodi)
          ->where('periode', '2017')
          ->count();

          if ($cek_smk == 0) {
            $saran = new saran_penerimaan();
            $saran->no_pendaftar = $k;
            $saran->kode_prodi = $value->kode_prodi;
            $saran->periode = '2017';
            $saran->ranking = $j;
            if (!$saran->save()) {
              return Redirect::back()->withErrors('The server encountered an unexpected condition');
            }
            $j += 1;
          }

          //jika sudah memenuhi kuota dan cadangan, keluar dari loop
          if($j == $total_kuota_smk){
            break;
          }
        }
      }
    } catch (\Illuminate\Database\QueryException $ex) {
      return Redirect::back()->withErrors($ex->getMessage());
    }
  }*/

  //perankingan selain prodi teknik dan bisnis / prodi akuntansi only
  /*public function rankProdiAkuntansi(){
    //lakukan perankingan dengan input ke tabel saran penerimaan dg nilai akhir terbesar
    try {
      //ambil data prodi administrasi bisnis
      $data_prodi_akun = DB::table('prodi')
      ->select('kode_prodi')
      ->where('kode_prodi', 'like', '34%')
      ->orwhere('kode_prodi', 'like', '44%')
      ->orderBy('kode_prodi', 'asc')
      ->get();
      //var_dump($data_prodi_akun);

      //ambil data mhs dari tiap prodi
      //ranking sma
      foreach ($data_prodi_akun as $value) {
        set_time_limit(0);

        //ambil data mahasiswa dengan nilai inggris >= 70
        //nilai inggris < 70 dinyatakan gugur = tidak diranking
        $data_moora = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->join('nilai_akademis', 'mahasiswa.no_pendaftar', '=', 'nilai_akademis.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->where('mahasiswa.tipe_sekolah', 'like', 'SMA%')
        ->get();

        $rank_sma = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora as $val) {
          set_time_limit(0);
          $rank_sma[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank_sma);

        //ambil kuota sma prodi
        $kuota_sma = DB::table('prodi')
        ->select('kuota_sma', 'kuota_cadangan')
        ->where('kode_prodi', $value->kode_prodi)
        ->get();

        foreach ($kuota_sma as $vals) {
          $sma = $vals->kuota_sma;
          $cadangan = $vals->kuota_cadangan;
        }
        $total_kuota_sma = $sma + $cadangan;

        //perankingan sma
        $i = 1; //counter ranking
        //save ke database
        foreach ($rank_sma as $k => $v) {
          set_time_limit(0);

          $cek_sama = DB::table('saran_penerimaan')
          ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
          ->where('no_pendaftar', $k)
          ->where('kode_prodi', $value->kode_prodi)
          ->where('periode', '2017')
          ->count();

          if ($cek_sama == 0) {
            $saran = new saran_penerimaan();
            $saran->no_pendaftar = $k;
            $saran->kode_prodi = $value->kode_prodi;
            $saran->periode = '2017';
            $saran->ranking = $i;
            if (!$saran->save()) {
              return Redirect::back()->withErrors('The server encountered an unexpected condition');
            }
            $i += 1;
          }

          //jika sudah memenuhi kuota dan cadangan, keluar dari loop
          if ($i == $total_kuota_sma) {
            break;
          }
        }
      }
      unset($value);

      //ranking SMK
      foreach ($data_prodi_akun as $value) {
        set_time_limit(0);

        $data_moora_smk = DB::table('mahasiswa')
        ->join('pilihan_mhs', 'mahasiswa.no_pendaftar', '=', 'pilihan_mhs.no_pendaftar')
        ->join('nilai_akademis', 'mahasiswa.no_pendaftar', '=', 'nilai_akademis.no_pendaftar')
        ->select('mahasiswa.no_pendaftar', 'pilihan_mhs.pilihan_prodi', 'mahasiswa.nilai_akhir')
        ->where('pilihan_mhs.pilihan_prodi', $value->kode_prodi)
        ->where('mahasiswa.tipe_sekolah', 'like', 'SMK%')
        ->get();

        $rank_smk = array();
        //masukkan ke array untuk di sort
        foreach ($data_moora_smk as $val) {
          set_time_limit(0);
          $rank_smk[$val->no_pendaftar] = $val->nilai_akhir;
        }
        arsort($rank_smk);

        //ambil kuota sma prodi
        $kuota_smk = DB::table('prodi')
        ->select('kuota_smk', 'kuota_cadangan')
        ->where('kode_prodi', $value->kode_prodi)
        ->get();

        foreach ($kuota_smk as $vals) {
          $smk = $vals->kuota_smk;
          $cadangan = $vals->kuota_cadangan;
        }
        $total_kuota_smk = $smk + $cadangan;

        //perankingan
        $j = 1; //counter ranking
        //save ke database
        foreach ($rank_smk as $k => $v) {
          set_time_limit(0);

          $cek_smk = DB::table('saran_penerimaan')
          ->select('no_pendaftar', 'kode_prodi', 'periode', 'ranking')
          ->where('no_pendaftar', $k)
          ->where('kode_prodi', $value->kode_prodi)
          ->where('periode', '2017')
          ->count();

          if ($cek_smk == 0) {
            $saran = new saran_penerimaan();
            $saran->no_pendaftar = $k;
            $saran->kode_prodi = $value->kode_prodi;
            $saran->periode = '2017';
            $saran->ranking = $j;
            if (!$saran->save()) {
              return Redirect::back()->withErrors('The server encountered an unexpected condition');
            }
            $j += 1;
          }

          //jika sudah memenuhi kuota dan cadangan, keluar dari loop
          if($j == $total_kuota_smk){
            break;
          }
        }
      }
    } catch (\Illuminate\Database\QueryException $ex) {
      return Redirect::back()->withErrors($ex->getMessage());
    }
  }*/
}
