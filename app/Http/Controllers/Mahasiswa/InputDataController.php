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
  /*public function __construct(){
    $this->middleware('auth');
    }*/
    public function index(){
      $periode = DB::table('mahasiswa')
      ->select('periode')
      ->distinct()
      ->get();

      foreach ($periode as $value) {
        $dataHistory = DB::table('mahasiswa')
        ->count('no_pendaftar');
        $data = array(
          'periode' => $value->periode,
          'mahasiswa' => $dataHistory,
        );
      }
      //var_dump($data);
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
            $akademis->save();

            //nilai akademis
            //matematika
            if($col->semester_1_matematika && $col->semester_1_matematika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "matematika";
              $nilai->nilai_mapel = $col->semester_1_matematika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_matematika && $col->semester_2_matematika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "matematika";
              $nilai->nilai_mapel = $col->semester_2_matematika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_matematika && $col->semester_3_matematika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "matematika";
              $nilai->nilai_mapel = $col->semester_3_matematika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_matematika && $col->semester_4_matematika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "matematika";
              $nilai->nilai_mapel = $col->semester_4_matematika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_matematika && $col->semester_5_matematika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "matematika";
              $nilai->nilai_mapel = $col->semester_5_matematika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //bahasa indonesia
            if($col->semester_1_bahasa_indonesia && $col->semester_1_bahasa_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "bahasa indonesia";
              $nilai->nilai_mapel = $col->semester_1_bahasa_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_bahasa_indonesia && $col->semester_2_bahasa_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "bahasa indonesia";
              $nilai->nilai_mapel = $col->semester_2_bahasa_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_bahasa_indonesia && $col->semester_3_bahasa_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "bahasa indonesia";
              $nilai->nilai_mapel = $col->semester_3_bahasa_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_bahasa_indonesia && $col->semester_4_bahasa_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "bahasa indonesia";
              $nilai->nilai_mapel = $col->semester_4_bahasa_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_bahasa_indonesia && $col->semester_5_bahasa_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "bahasa indonesia";
              $nilai->nilai_mapel = $col->semester_5_bahasa_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //bahasa inggris
            if($col->semester_1_bahasa_inggris && $col->semester_1_bahasa_inggris != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "bahasa inggris";
              $nilai->nilai_mapel = $col->semester_1_bahasa_inggris;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_bahasa_inggris && $col->semester_2_bahasa_inggris != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "bahasa inggris";
              $nilai->nilai_mapel = $col->semester_2_bahasa_inggris;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_bahasa_inggris && $col->semester_3_bahasa_inggris != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "bahasa inggris";
              $nilai->nilai_mapel = $col->semester_3_bahasa_inggris;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_bahasa_inggris && $col->semester_4_bahasa_inggris != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "bahasa inggris";
              $nilai->nilai_mapel = $col->semester_4_bahasa_inggris;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_bahasa_inggris && $col->semester_5_bahasa_inggris != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "bahasa inggris";
              $nilai->nilai_mapel = $col->semester_5_bahasa_inggris;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //ipa-fisika
            if($col->semester_1_ipa_fisika && $col->semester_1_ipa_fisika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "fisika";
              $nilai->nilai_mapel = $col->semester_1_ipa_fisika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_ipa_fisika && $col->semester_2_ipa_fisika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "fisika";
              $nilai->nilai_mapel = $col->semester_2_ipa_fisika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_ipa_fisika && $col->semester_3_ipa_fisika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "fisika";
              $nilai->nilai_mapel = $col->semester_3_ipa_fisika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_ipa_fisika && $col->semester_4_ipa_fisika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "fisika";
              $nilai->nilai_mapel = $col->semester_4_ipa_fisika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_ipa_fisika && $col->semester_5_ipa_fisika != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "fisika";
              $nilai->nilai_mapel = $col->semester_5_ipa_fisika;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //ipa-kimia
            if($col->semester_1_ipa_kimia && $col->semester_1_ipa_kimia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "kimia";
              $nilai->nilai_mapel = $col->semester_1_ipa_kimia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_ipa_kimia && $col->semester_2_ipa_kimia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "kimia";
              $nilai->nilai_mapel = $col->semester_2_ipa_kimia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_ipa_kimia && $col->semester_3_ipa_kimia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "kimia";
              $nilai->nilai_mapel = $col->semester_3_ipa_kimia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_ipa_kimia && $col->semester_4_ipa_kimia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "kimia";
              $nilai->nilai_mapel = $col->semester_4_ipa_kimia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_ipa_kimia && $col->semester_5_ipa_kimia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "kimia";
              $nilai->nilai_mapel = $col->semester_5_ipa_kimia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //ipa-biologi
            if($col->semester_1_ipa_biologi && $col->semester_1_ipa_biologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "biologi";
              $nilai->nilai_mapel = $col->semester_1_ipa_biologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_ipa_biologi && $col->semester_2_ipa_biologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "biologi";
              $nilai->nilai_mapel = $col->semester_2_ipa_biologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_ipa_biologi && $col->semester_3_ipa_biologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "biologi";
              $nilai->nilai_mapel = $col->semester_3_ipa_biologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_ipa_biologi && $col->semester_4_ipa_biologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "biologi";
              $nilai->nilai_mapel = $col->semester_4_ipa_biologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_ipa_biologi && $col->semester_5_ipa_biologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "biologi";
              $nilai->nilai_mapel = $col->semester_5_ipa_biologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //ips-ekonomi
            if($col->semester_1_ips_ekonomi && $col->semester_1_ips_ekonomi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "ekonomi";
              $nilai->nilai_mapel = $col->semester_1_ips_ekonomi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_ips_ekonomi && $col->semester_2_ips_ekonomi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "ekonomi";
              $nilai->nilai_mapel = $col->semester_2_ips_ekonomi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_ips_ekonomi && $col->semester_3_ips_ekonomi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "ekonomi";
              $nilai->nilai_mapel = $col->semester_3_ips_ekonomi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_ips_ekonomi && $col->semester_4_ips_ekonomi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "ekonomi";
              $nilai->nilai_mapel = $col->semester_4_ips_ekonomi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_ips_ekonomi && $col->semester_5_ips_ekonomi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "ekonomi";
              $nilai->nilai_mapel = $col->semester_5_ips_ekonomi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //ips-geografi
            if($col->semester_1_ips_geografi && $col->semester_1_ips_geografi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "geografi";
              $nilai->nilai_mapel = $col->semester_1_ips_geografi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_ips_geografi && $col->semester_2_ips_geografi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "geografi";
              $nilai->nilai_mapel = $col->semester_2_ips_geografi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_ips_geografi && $col->semester_3_ips_geografi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "geografi";
              $nilai->nilai_mapel = $col->semester_3_ips_geografi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_ips_geografi && $col->semester_4_ips_geografi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "geografi";
              $nilai->nilai_mapel = $col->semester_4_ips_geografi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_ips_geografi && $col->semester_5_ips_geografi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "geografi";
              $nilai->nilai_mapel = $col->semester_5_ips_geografi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //ips-sosiologi
            if($col->semester_1_ips_sosiologi && $col->semester_1_ips_sosiologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "sosiologi";
              $nilai->nilai_mapel = $col->semester_1_ips_sosiologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_ips_sosiologi && $col->semester_2_ips_sosiologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "sosiologi";
              $nilai->nilai_mapel = $col->semester_2_ips_sosiologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_ips_sosiologi && $col->semester_3_ips_sosiologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "sosiologi";
              $nilai->nilai_mapel = $col->semester_3_ips_sosiologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_ips_sosiologi && $col->semester_4_ips_sosiologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "sosiologi";
              $nilai->nilai_mapel = $col->semester_4_ips_sosiologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_ips_sosiologi && $col->semester_5_ips_sosiologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "sosiologi";
              $nilai->nilai_mapel = $col->semester_5_ips_sosiologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //bahasa-sastra indonesia
            if($col->semester_1_bahasa_sastra_indonesia && $col->semester_1_bahasa_sastra_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "sastra indonesia";
              $nilai->nilai_mapel = $col->semester_1_bahasa_sastra_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_bahasa_sastra_indonesia && $col->semester_2_bahasa_sastra_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "sastra indonesia";
              $nilai->nilai_mapel = $col->semester_2_bahasa_sastra_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_bahasa_sastra_indonesia && $col->semester_3_bahasa_sastra_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "sastra indonesia";
              $nilai->nilai_mapel = $col->semester_3_bahasa_sastra_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_bahasa_sastra_indonesia && $col->semester_4_bahasa_sastra_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "sastra indonesia";
              $nilai->nilai_mapel = $col->semester_4_bahasa_sastra_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_bahasa_sastra_indonesia && $col->semester_5_bahasa_sastra_indonesia != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "sastra indonesia";
              $nilai->nilai_mapel = $col->semester_5_bahasa_sastra_indonesia;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //bahasa-antropologi
            if($col->semester_1_bahasa_antropologi && $col->semester_1_bahasa_antropologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "antropologi";
              $nilai->nilai_mapel = $col->semester_1_bahasa_antropologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_bahasa_antropologi && $col->semester_2_bahasa_antropologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "antropologi";
              $nilai->nilai_mapel = $col->semester_2_bahasa_antropologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_bahasa_antropologi && $col->semester_3_bahasa_antropologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "antropologi";
              $nilai->nilai_mapel = $col->semester_3_bahasa_antropologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_bahasa_antropologi && $col->semester_4_bahasa_antropologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "antropologi";
              $nilai->nilai_mapel = $col->semester_4_bahasa_antropologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_bahasa_antropologi && $col->semester_5_bahasa_antropologi != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "antropologi";
              $nilai->nilai_mapel = $col->semester_5_bahasa_antropologi;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //bahasa-asing
            if($col->semester_1_bahasa_bahasa_asing && $col->semester_1_bahasa_bahasa_asing != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "bahasa asing";
              $nilai->nilai_mapel = $col->semester_1_bahasa_bahasa_asing;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_bahasa_bahasa_asing && $col->semester_2_bahasa_bahasa_asing != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "bahasa asing";
              $nilai->nilai_mapel = $col->semester_2_bahasa_bahasa_asing;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_bahasa_bahasa_asing && $col->semester_3_bahasa_bahasa_asing != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "bahasa asing";
              $nilai->nilai_mapel = $col->semester_3_bahasa_bahasa_asing;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_bahasa_bahasa_asing && $col->semester_4_bahasa_bahasa_asing != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "bahasa asing";
              $nilai->nilai_mapel = $col->semester_4_bahasa_bahasa_asing;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_bahasa_bahasa_asing && $col->semester_5_bahasa_bahasa_asing != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "bahasa asing";
              $nilai->nilai_mapel = $col->semester_5_bahasa_bahasa_asing;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //agama-tafsir
            if($col->semester_1_agama_tafsir && $col->semester_1_agama_tafsir != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "tafsir";
              $nilai->nilai_mapel = $col->semester_1_agama_tafsir;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_agama_tafsir && $col->semester_2_agama_tafsir != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "tafsir";
              $nilai->nilai_mapel = $col->semester_2_agama_tafsir;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_agama_tafsir && $col->semester_3_agama_tafsir != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "tafsir";
              $nilai->nilai_mapel = $col->semester_3_agama_tafsir;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_agama_tafsir && $col->semester_4_agama_tafsir != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "tafsir";
              $nilai->nilai_mapel = $col->semester_4_agama_tafsir;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_agama_tafsir && $col->semester_5_agama_tafsir != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "tafsir";
              $nilai->nilai_mapel = $col->semester_5_agama_tafsir;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //agama-fikih
            if($col->semester_1_agama_fikih && $col->semester_1_agama_fikih != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "fikih";
              $nilai->nilai_mapel = $col->semester_1_agama_fikih;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_agama_fikih && $col->semester_2_agama_fikih != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "fikih";
              $nilai->nilai_mapel = $col->semester_2_agama_fikih;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_agama_fikih && $col->semester_3_agama_fikih != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "fikih";
              $nilai->nilai_mapel = $col->semester_3_agama_fikih;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_agama_fikih && $col->semester_4_agama_fikih != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "fikih";
              $nilai->nilai_mapel = $col->semester_4_agama_fikih;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_agama_fikih && $col->semester_5_agama_fikih != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "fikih";
              $nilai->nilai_mapel = $col->semester_5_agama_fikih;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //agama-hadist
            if($col->semester_1_agama_hadist && $col->semester_1_agama_hadist != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "hadist";
              $nilai->nilai_mapel = $col->semester_1_agama_hadist;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_agama_hadist && $col->semester_2_agama_hadist != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "hadist";
              $nilai->nilai_mapel = $col->semester_2_agama_hadist;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_agama_hadist && $col->semester_3_agama_hadist != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "hadist";
              $nilai->nilai_mapel = $col->semester_3_agama_hadist;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_agama_hadist && $col->semester_4_agama_hadist != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "hadist";
              $nilai->nilai_mapel = $col->semester_4_agama_hadist;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_agama_hadist && $col->semester_5_agama_hadist != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "hadist";
              $nilai->nilai_mapel = $col->semester_5_agama_hadist;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //smk rata-rata kejuruan
            if($col->semester_1_smk_rata_rata_kejuruanproduktif && $col->semester_1_smk_rata_rata_kejuruanproduktif != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 1;
              $nilai->jenis_nilai = $col->semester_1_jenis_nilai;
              $nilai->mapel = "smk rata rata kejuruan";
              $nilai->nilai_mapel = $col->semester_1_smk_rata_rata_kejuruanproduktif;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_2_smk_rata_rata_kejuruanproduktif && $col->semester_2_smk_rata_rata_kejuruanproduktif != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 2;
              $nilai->jenis_nilai = $col->semester_2_jenis_nilai;
              $nilai->mapel = "smk rata rata kejuruan";
              $nilai->nilai_mapel = $col->semester_2_smk_rata_rata_kejuruanproduktif;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_3_smk_rata_rata_kejuruanproduktif && $col->semester_3_smk_rata_rata_kejuruanproduktif != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 3;
              $nilai->jenis_nilai = $col->semester_3_jenis_nilai;
              $nilai->mapel = "smk rata rata kejuruan";
              $nilai->nilai_mapel = $col->semester_3_smk_rata_rata_kejuruanproduktif;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_4_smk_rata_rata_kejuruanproduktif && $col->semester_4_smk_rata_rata_kejuruanproduktif != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 4;
              $nilai->jenis_nilai = $col->semester_4_jenis_nilai;
              $nilai->mapel = "smk rata rata kejuruan";
              $nilai->nilai_mapel = $col->semester_4_smk_rata_rata_kejuruanproduktif;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }
            if($col->semester_5_smk_rata_rata_kejuruanproduktif && $col->semester_5_smk_rata_rata_kejuruanproduktif != 0){
              $nilai = new nilai_akademis();
              $nilai->no_pendaftar = $col->nomor_pendaftaran;
              $nilai->semester = 5;
              $nilai->jenis_nilai = $col->semester_5_jenis_nilai;
              $nilai->mapel = "smk rata rata kejuruan";
              $nilai->nilai_mapel = $col->semester_5_smk_rata_rata_kejuruanproduktif;
              $nilai->nilai_mapel_koreksi = 0;
              $nilai->save();
            }

            //peringkat
            if($col->semester_1_peringkat){
              $peringkat = new peringkat();
              $peringkat->no_pendaftar = $col->nomor_pendaftaran;
              $peringkat->semester = 1;
              $peringkat->peringkat = $col->semester_1_peringkat;
              $peringkat->jumlah_siswa = $col->semester_1_jumlah_siswa;
              $peringkat->save();
            }
            if($col->semester_2_peringkat){
              $peringkat = new peringkat();
              $peringkat->no_pendaftar = $col->nomor_pendaftaran;
              $peringkat->semester = 2;
              $peringkat->peringkat = $col->semester_2_peringkat;
              $peringkat->jumlah_siswa = $col->semester_2_jumlah_siswa;
              $peringkat->save();
            }
            if($col->semester_3_peringkat){
              $peringkat = new peringkat();
              $peringkat->no_pendaftar = $col->nomor_pendaftaran;
              $peringkat->semester = 3;
              $peringkat->peringkat = $col->semester_3_peringkat;
              $peringkat->jumlah_siswa = $col->semester_3_jumlah_siswa;
              $peringkat->save();
            }
            if($col->semester_4_peringkat){
              $peringkat = new peringkat();
              $peringkat->no_pendaftar = $col->nomor_pendaftaran;
              $peringkat->semester = 4;
              $peringkat->peringkat = $col->semester_4_peringkat;
              $peringkat->jumlah_siswa = $col->semester_4_jumlah_siswa;
              $peringkat->save();
            }
            if($col->semester_5_peringkat){
              $peringkat = new peringkat();
              $peringkat->no_pendaftar = $col->nomor_pendaftaran;
              $peringkat->semester = 5;
              $peringkat->peringkat = $col->semester_5_peringkat;
              $peringkat->jumlah_siswa = $col->semester_5_jumlah_siswa;
              $peringkat->save();
            }

            //pilihan prodi
            if($col->pilihan_poltek_1 && $col->pilihan_poltek_1 != "0" && $col->pilihan_poltek_1 == "Politeknik Negeri Semarang"){
              $pilihan = new pilihan_mhs();
              $pilihan->no_pendaftar = $col->nomor_pendaftaran;
              $pilihan->pilihan_ke = 1;
              $pilihan->pilihan_poltek = $col->pilihan_poltek_1;
              $pilihan->pilihan_prodi = $col->pilihan_prodi_1;
              $pilihan->save();
            }
            if($col->pilihan_poltek_2 && $col->pilihan_poltek_2 != "0" && $col->pilihan_poltek_2 == "Politeknik Negeri Semarang"){
              $pilihan = new pilihan_mhs();
              $pilihan->no_pendaftar = $col->nomor_pendaftaran;
              $pilihan->pilihan_ke = 2;
              $pilihan->pilihan_poltek = $col->pilihan_poltek_2;
              $pilihan->pilihan_prodi = $col->pilihan_prodi_2;
              $pilihan->save();
            }
            if($col->pilihan_poltek_3 && $col->pilihan_poltek_3 != "0" && $col->pilihan_poltek_3 == "Politeknik Negeri Semarang"){
              $pilihan = new pilihan_mhs();
              $pilihan->no_pendaftar = $col->nomor_pendaftaran;
              $pilihan->pilihan_ke = 3;
              $pilihan->pilihan_poltek = $col->pilihan_poltek_3;
              $pilihan->pilihan_prodi = $col->pilihan_prodi_3;
              $pilihan->save();
            }
            //var_dump($col, '<br>');
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
