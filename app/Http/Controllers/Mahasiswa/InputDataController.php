<?php

namespace App\Http\Controllers\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Mahasiswa as mahasiswa;
use App\Peringkat as peringkat;
use App\PilihanMhs as pilihan_mhs;
use App\NilaiAkademis as nilai_akademis;
use App\NilaiNonAkademis as nilai_non_akademis;

class InputDataController extends Controller
{
    public function index(){
      $dataHistory = mahasiswa::select(DB::raw("no_pendaftar, periode"))
      ->get();
      $data = array('mahasiswa' => $dataHistory);
      return view('admin.dashboard.mahasiswa.inputDataView', $data);
    }

    protected function inputDataAkademis(Request $request){
      //import to database
      $validator = Validator::make(Input::all(), [
  			'nilai_akademis' => 'required',
  		]);
      if($validator->fails()){
        return Redirect::back()->withErrors($validator);
      }
      //import to database
      if($request->hasFile('nilai_akademis')){
        $path = $request->file('nilai_akademis')->getRealPath();
  		  $data = Excel::load($path, function($reader){
          //$reader->ignoreEmpty();
          $results = $reader->get();
          foreach($results as $col){
            //data mahasiswa
            $akademis = new mahasiswa();
            $akademis->no_pendaftar = $col->nomor_pendaftaran;
            $akademis->nisn = $col->nisn;
            $akademis->nama = $col->nama_lengkap;
            $akademis->jenis_kelamin = $col->lp;
            $akademis->agama = $col->agama;
            $akademis->tgl_lahir = $col->tanggal_lahir;
            $akademis->kecamatan = $col->kecamatan;
            $akademis->kota = $col->kota;
            $akademis->provinsi = $col->provinsi;
            $akademis->npsn = $col->npsn;
            $akademis->tipe_sekolah = $col->tipe_sekolah;
            $akademis->jenis_sekolah = $col->jenis_sekolah;
            $akademis->akreditasi_sekolah = $col->akreditasi_sekolah;
            $akademis->jurusan_asal = $col->jurusan_asal;
            $akademis->nilai_akademis = 0;
            $akademis->nilai_non_akademis = 0;
            $akademis->nilai_akhir = 0;
            $akademis->periode = date('Y');
            /*var_dump($akademis->no_pendaftar, $akademis->nisn, $akademis->nama, $akademis->jenis_kelamin,
            $akademis->agama, $akademis->tgl_lahir, $akademis->kecamatan, $akademis->kota, $akademis->provinsi,
            $akademis->npsn, $akademis->tipe_sekolah, $akademis->jenis_sekolah, $akademis->akreditasi_sekolah,
            $akademis->jurusan_asal, $akademis->nilai_akademis, $akademis->nilai_non_akademis,
            $akademis->nilai_akhir, $akademis->periode. '<br>');*/
            //var_dump($col, '<br>');
            $akademis->save();
          }
        });
        return Redirect::back()->with('successMessage', 'Data Akademis berhasil disimpan.');
  		}else{
        return Redirect::back()->withErrors('error','Please Check your file, Something is wrong there.');
      }
    }

    public function inputDataNonAkademis(Request $request){
      //validation
  		$validator = Validator::make(Input::all(), [
  			'nilai_non_akademis' => 'required',
  		]);
      if($validator->fails()){
        return Redirect::back()->withErrors($validator);
      }
      //import to database
      if($request->hasFile('nilai_non_akademis')){
        $path = $request->file('nilai_non_akademis')->getRealPath();
  		  $data = Excel::load($path, function($reader){
          //$reader->ignoreEmpty();
          $results = $reader->get();
          foreach($results as $col){
            $prestasi = new nilai_non_akademis();
            $prestasi->no_pendaftar = $col->nomor_pendaftaran;
            $prestasi->nama_prestasi = $col->nama_prestasi;
            $prestasi->skala_prestasi = $col->skala_prestasi;
            $prestasi->jenis_prestasi = $col->jenis_prestasi;
            $prestasi->juara_prestasi = $col->juara_prestasi;
            $prestasi->tahun_prestasi = $col->tahun_prestasi;
            /*var_dump($prestasi->no_pendaftar, $prestasi->nama_prestasi, $prestasi->skala_prestasi,
            $prestasi->jenis_prestasi, $prestasi->juara_prestasi, $prestasi->tahun_prestasi. '<br>');*/
            $prestasi->save();
          }
        });
        return Redirect::back()->with('successMessage', 'Data Prestasi berhasil disimpan.');
        /*if(!empty($data) && $data->count()){
  			  foreach ($data as $key => $value) {
            $insert[] = ['no_pendaftar' => $value->no_pendaftar, 'nama_prestasi' => $value->nama_prestasi,
            'skala_prestasi' => $value->skala_prestasi, 'jenis_prestasi' => $value->jenis_prestasi,
            'juara_prestasi' => $value->juara_prestasi, 'tahun_prestasi' => $value->tahun_prestasi];
  			  }

  			  if(!empty($insert)){
  				  DB::table('nilai_non_akademis')->insert($insert);
  				  return back()->with('success','Insert Record successfully.');
  			  }
  		  }*/
  		}else{
        return Redirect::back()->withErrors('error','Please Check your file, Something is wrong there.');
      }
    }
}
