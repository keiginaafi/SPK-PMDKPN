<?php

namespace App\Http\Controllers\Mahasiswa;

//ini_set('max_execution_time', '600');
ini_set('memory_limit', '256M');

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
    public function __construct(){
      $this->middleware('auth');
    }

    public function index(){
      $periode = DB::table('mahasiswa')
      ->select('periode')
      ->distinct()
      ->get();

      //var_dump($periode->count());
      if (!empty($periode) && $periode->count() > 0) {
        foreach ($periode as $value) {
          $dataHistory = DB::table('mahasiswa')
          ->count('no_pendaftar');
          $data = array(
            'periode' => $value->periode,
            'mahasiswa' => $dataHistory,
          );
        }
        return view('admin.dashboard.mahasiswa.inputDataView', $data);
      } else {
        $data = array(
          'periode' => 0,
          'mahasiswa' => 0,
        );
        return view('admin.dashboard.mahasiswa.inputDataView', $data);
      }
    }

    protected function inputDataAkademis(Request $request){
      //$start = microtime(true);
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
  		  $data = Excel::filter('chunk')->load($path)->chunk(300, function($results){
          set_time_limit(0);
          //$reader->ignoreEmpty();
          //$results = $reader->get();
          //data mahasiswa
          try {
            if(!empty($results) && $results->count() > 0){
      			  foreach ($results as $col) {
                set_time_limit(0);
                $insert_mhs[] = [
                  'no_pendaftar' => $col->nomor_pendaftaran,
                  'nisn' => $col->nisn,
                  'nama' => $col->nama_lengkap,
                  'jenis_kelamin' => $col->lp,
                  'agama' => $col->agama,
                  'tgl_lahir' => $col->tanggal_lahir,
                  'kecamatan' => $col->kecamatan,
                  'kota' => $col->kota,
                  'provinsi' => $col->provinsi,
                  'npsn' => $col->npsn,
                  'tipe_sekolah' => $col->tipe_sekolah,
                  'jenis_sekolah' => $col->jenis_sekolah,
                  'akreditasi_sekolah' => $col->akreditasi_sekolah,
                  'jurusan_asal' => $col->jurusan_asal,
                  'nilai_akademis' => 0,
                  'nilai_non_akademis' => 0,
                  'nilai_akhir' => 0,
                  'periode' => date_parse($col->tanggal_simpan_data)['year']
                ];
      			  }
            }

            if(!empty($insert_mhs)){
    				  DB::table('mahasiswa')->insert($insert_mhs);
    			  }
          } catch (\Illuminate\Database\QueryException $ex) {
            return Redirect::back()->withErrors('Gagal melakukan input data.<br>'.$ex->getMessage());
          }

          //nilai akademis
          try {
            if(!empty($results) && $results->count() > 0){
      			  foreach ($results as $col) {
                set_time_limit(0);
                if($col->semester_1_matematika && $col->semester_1_matematika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Matematika',
                    'nilai_mapel' => $col->semester_1_matematika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_matematika && $col->semester_2_matematika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Matematika',
                    'nilai_mapel' => $col->semester_2_matematika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_matematika && $col->semester_3_matematika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Matematika',
                    'nilai_mapel' => $col->semester_3_matematika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_matematika && $col->semester_4_matematika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Matematika',
                    'nilai_mapel' => $col->semester_4_matematika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_matematika && $col->semester_5_matematika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Matematika',
                    'nilai_mapel' => $col->semester_5_matematika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //bahasa indonesia
                if($col->semester_1_bahasa_indonesia && $col->semester_1_bahasa_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Bahasa Indonesia',
                    'nilai_mapel' => $col->semester_1_bahasa_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_bahasa_indonesia && $col->semester_2_bahasa_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Bahasa Indonesia',
                    'nilai_mapel' => $col->semester_2_bahasa_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_bahasa_indonesia && $col->semester_3_bahasa_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Bahasa Indonesia',
                    'nilai_mapel' => $col->semester_3_bahasa_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_bahasa_indonesia && $col->semester_4_bahasa_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Bahasa Indonesia',
                    'nilai_mapel' => $col->semester_4_bahasa_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_bahasa_indonesia && $col->semester_5_bahasa_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Bahasa Indonesia',
                    'nilai_mapel' => $col->semester_5_bahasa_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //bahasa inggris
                if($col->semester_1_bahasa_inggris && $col->semester_1_bahasa_inggris != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Bahasa Inggris',
                    'nilai_mapel' => $col->semester_1_bahasa_inggris,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_bahasa_inggris && $col->semester_2_bahasa_inggris != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Bahasa Inggris',
                    'nilai_mapel' => $col->semester_2_bahasa_inggris,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_bahasa_inggris && $col->semester_3_bahasa_inggris != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Bahasa Inggris',
                    'nilai_mapel' => $col->semester_3_bahasa_inggris,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_bahasa_inggris && $col->semester_4_bahasa_inggris != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Bahasa Inggris',
                    'nilai_mapel' => $col->semester_4_bahasa_inggris,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_bahasa_inggris && $col->semester_5_bahasa_inggris != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Bahasa Inggris',
                    'nilai_mapel' => $col->semester_5_bahasa_inggris,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //ipa-fisika
                if($col->semester_1_ipa_fisika && $col->semester_1_ipa_fisika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Fisika',
                    'nilai_mapel' => $col->semester_1_ipa_fisika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_ipa_fisika && $col->semester_2_ipa_fisika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Fisika',
                    'nilai_mapel' => $col->semester_2_ipa_fisika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_ipa_fisika && $col->semester_3_ipa_fisika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Fisika',
                    'nilai_mapel' => $col->semester_3_ipa_fisika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_ipa_fisika && $col->semester_4_ipa_fisika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Fisika',
                    'nilai_mapel' => $col->semester_4_ipa_fisika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_ipa_fisika && $col->semester_5_ipa_fisika != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Fisika',
                    'nilai_mapel' => $col->semester_5_ipa_fisika,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //ipa-kimia
                if($col->semester_1_ipa_kimia && $col->semester_1_ipa_kimia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Kimia',
                    'nilai_mapel' => $col->semester_1_ipa_kimia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_ipa_kimia && $col->semester_2_ipa_kimia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Kimia',
                    'nilai_mapel' => $col->semester_2_ipa_kimia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_ipa_kimia && $col->semester_3_ipa_kimia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Kimia',
                    'nilai_mapel' => $col->semester_3_ipa_kimia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_ipa_kimia && $col->semester_4_ipa_kimia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Kimia',
                    'nilai_mapel' => $col->semester_4_ipa_kimia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_ipa_kimia && $col->semester_5_ipa_kimia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Kimia',
                    'nilai_mapel' => $col->semester_5_ipa_kimia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //ipa-biologi
                if($col->semester_1_ipa_biologi && $col->semester_1_ipa_biologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Biologi',
                    'nilai_mapel' => $col->semester_1_ipa_biologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_ipa_biologi && $col->semester_2_ipa_biologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Biologi',
                    'nilai_mapel' => $col->semester_2_ipa_biologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_ipa_biologi && $col->semester_3_ipa_biologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Biologi',
                    'nilai_mapel' => $col->semester_3_ipa_biologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_ipa_biologi && $col->semester_4_ipa_biologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Biologi',
                    'nilai_mapel' => $col->semester_4_ipa_biologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_ipa_biologi && $col->semester_5_ipa_biologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Biologi',
                    'nilai_mapel' => $col->semester_5_ipa_biologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //ips-ekonomi
                if($col->semester_1_ips_ekonomi && $col->semester_1_ips_ekonomi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Ekonomi',
                    'nilai_mapel' => $col->semester_1_ips_ekonomi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_ips_ekonomi && $col->semester_2_ips_ekonomi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Ekonomi',
                    'nilai_mapel' => $col->semester_2_ips_ekonomi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_ips_ekonomi && $col->semester_3_ips_ekonomi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Ekonomi',
                    'nilai_mapel' => $col->semester_3_ips_ekonomi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_ips_ekonomi && $col->semester_4_ips_ekonomi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Ekonomi',
                    'nilai_mapel' => $col->semester_4_ips_ekonomi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_ips_ekonomi && $col->semester_5_ips_ekonomi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Ekonomi',
                    'nilai_mapel' => $col->semester_5_ips_ekonomi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //ips-geografi
                if($col->semester_1_ips_geografi && $col->semester_1_ips_geografi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Geografi',
                    'nilai_mapel' => $col->semester_1_ips_geografi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_ips_geografi && $col->semester_2_ips_geografi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Geografi',
                    'nilai_mapel' => $col->semester_2_ips_geografi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_ips_geografi && $col->semester_3_ips_geografi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Geografi',
                    'nilai_mapel' => $col->semester_3_ips_geografi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_ips_geografi && $col->semester_4_ips_geografi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Geografi',
                    'nilai_mapel' => $col->semester_4_ips_geografi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_ips_geografi && $col->semester_5_ips_geografi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Geografi',
                    'nilai_mapel' => $col->semester_5_ips_geografi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //ips-sosiologi
                if($col->semester_1_ips_sosiologi && $col->semester_1_ips_sosiologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Sosiologi',
                    'nilai_mapel' => $col->semester_1_ips_sosiologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_ips_sosiologi && $col->semester_2_ips_sosiologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Sosiologi',
                    'nilai_mapel' => $col->semester_2_ips_sosiologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_ips_sosiologi && $col->semester_3_ips_sosiologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Sosiologi',
                    'nilai_mapel' => $col->semester_3_ips_sosiologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_ips_sosiologi && $col->semester_4_ips_sosiologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Sosiologi',
                    'nilai_mapel' => $col->semester_4_ips_sosiologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_ips_sosiologi && $col->semester_5_ips_sosiologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Sosiologi',
                    'nilai_mapel' => $col->semester_5_ips_sosiologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //bahasa-sastra indonesia
                if($col->semester_1_bahasa_sastra_indonesia && $col->semester_1_bahasa_sastra_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Sastra Indonesia',
                    'nilai_mapel' => $col->semester_1_bahasa_sastra_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_bahasa_sastra_indonesia && $col->semester_2_bahasa_sastra_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Sastra Indonesia',
                    'nilai_mapel' => $col->semester_2_bahasa_sastra_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_bahasa_sastra_indonesia && $col->semester_3_bahasa_sastra_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Sastra Indonesia',
                    'nilai_mapel' => $col->semester_3_bahasa_sastra_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_bahasa_sastra_indonesia && $col->semester_4_bahasa_sastra_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Sastra Indonesia',
                    'nilai_mapel' => $col->semester_4_bahasa_sastra_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_bahasa_sastra_indonesia && $col->semester_5_bahasa_sastra_indonesia != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Sastra Indonesia',
                    'nilai_mapel' => $col->semester_5_bahasa_sastra_indonesia,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //bahasa-antropologi
                if($col->semester_1_bahasa_antropologi && $col->semester_1_bahasa_antropologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Antropologi',
                    'nilai_mapel' => $col->semester_1_bahasa_antropologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_bahasa_antropologi && $col->semester_2_bahasa_antropologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Antropologi',
                    'nilai_mapel' => $col->semester_2_bahasa_antropologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_bahasa_antropologi && $col->semester_3_bahasa_antropologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Antropologi',
                    'nilai_mapel' => $col->semester_3_bahasa_antropologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_bahasa_antropologi && $col->semester_4_bahasa_antropologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Antropologi',
                    'nilai_mapel' => $col->semester_4_bahasa_antropologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_bahasa_antropologi && $col->semester_5_bahasa_antropologi != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Antropologi',
                    'nilai_mapel' => $col->semester_5_bahasa_antropologi,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //bahasa-asing
                if($col->semester_1_bahasa_bahasa_asing && $col->semester_1_bahasa_bahasa_asing != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Bahasa Asing',
                    'nilai_mapel' => $col->semester_1_bahasa_bahasa_asing,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_bahasa_bahasa_asing && $col->semester_2_bahasa_bahasa_asing != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Bahasa Asing',
                    'nilai_mapel' => $col->semester_2_bahasa_bahasa_asing,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_bahasa_bahasa_asing && $col->semester_3_bahasa_bahasa_asing != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Bahasa Asing',
                    'nilai_mapel' => $col->semester_3_bahasa_bahasa_asing,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_bahasa_bahasa_asing && $col->semester_4_bahasa_bahasa_asing != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Bahasa Asing',
                    'nilai_mapel' => $col->semester_4_bahasa_bahasa_asing,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_bahasa_bahasa_asing && $col->semester_5_bahasa_bahasa_asing != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Bahasa Asing',
                    'nilai_mapel' => $col->semester_5_bahasa_bahasa_asing,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //agama-tafsir
                if($col->semester_1_agama_tafsir && $col->semester_1_agama_tafsir != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Tafsir',
                    'nilai_mapel' => $col->semester_1_agama_tafsir,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_agama_tafsir && $col->semester_2_agama_tafsir != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Tafsir',
                    'nilai_mapel' => $col->semester_2_agama_tafsir,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_agama_tafsir && $col->semester_3_agama_tafsir != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Tafsir',
                    'nilai_mapel' => $col->semester_3_agama_tafsir,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_agama_tafsir && $col->semester_4_agama_tafsir != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Tafsir',
                    'nilai_mapel' => $col->semester_4_agama_tafsir,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_agama_tafsir && $col->semester_5_agama_tafsir != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Tafsir',
                    'nilai_mapel' => $col->semester_5_agama_tafsir,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //agama-fikih
                if($col->semester_1_agama_fikih && $col->semester_1_agama_fikih != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_1_agama_fikih,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_agama_fikih && $col->semester_2_agama_fikih != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_2_agama_fikih,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_agama_fikih && $col->semester_3_agama_fikih != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_3_agama_fikih,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_agama_fikih && $col->semester_4_agama_fikih != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_4_agama_fikih,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_agama_fikih && $col->semester_5_agama_fikih != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_5_agama_fikih,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //agama-hadist
                if($col->semester_1_agama_hadist && $col->semester_1_agama_hadist != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_1_agama_hadist,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_agama_hadist && $col->semester_2_agama_hadist != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_2_agama_hadist,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_agama_hadist && $col->semester_3_agama_hadist != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_3_agama_hadist,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_agama_hadist && $col->semester_4_agama_hadist != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_4_agama_hadist,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_agama_hadist && $col->semester_5_agama_hadist != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'Fikih',
                    'nilai_mapel' => $col->semester_5_agama_hadist,
                    'nilai_mapel_koreksi' => 0
                  ];
                }

                //smk rata-rata kejuruan
                if($col->semester_1_smk_rata_rata_kejuruanproduktif && $col->semester_1_smk_rata_rata_kejuruanproduktif != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'jenis_nilai' => $col->semester_1_jenis_nilai,
                    'mapel' => 'SMK Rata Rata Kejuruan',
                    'nilai_mapel' => $col->semester_1_smk_rata_rata_kejuruanproduktif,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_2_smk_rata_rata_kejuruanproduktif && $col->semester_2_smk_rata_rata_kejuruanproduktif != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'jenis_nilai' => $col->semester_2_jenis_nilai,
                    'mapel' => 'SMK Rata Rata Kejuruan',
                    'nilai_mapel' => $col->semester_2_smk_rata_rata_kejuruanproduktif,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_3_smk_rata_rata_kejuruanproduktif && $col->semester_3_smk_rata_rata_kejuruanproduktif != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'jenis_nilai' => $col->semester_3_jenis_nilai,
                    'mapel' => 'SMK Rata Rata Kejuruan',
                    'nilai_mapel' => $col->semester_3_smk_rata_rata_kejuruanproduktif,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_4_smk_rata_rata_kejuruanproduktif && $col->semester_4_smk_rata_rata_kejuruanproduktif != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'jenis_nilai' => $col->semester_4_jenis_nilai,
                    'mapel' => 'SMK Rata Rata Kejuruan',
                    'nilai_mapel' => $col->semester_4_smk_rata_rata_kejuruanproduktif,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
                if($col->semester_5_smk_rata_rata_kejuruanproduktif && $col->semester_5_smk_rata_rata_kejuruanproduktif != 0){
                  $insert_nilai[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'jenis_nilai' => $col->semester_5_jenis_nilai,
                    'mapel' => 'SMK Rata Rata Kejuruan',
                    'nilai_mapel' => $col->semester_5_smk_rata_rata_kejuruanproduktif,
                    'nilai_mapel_koreksi' => 0
                  ];
                }
              }
            }

            if(!empty($insert_nilai)){
    				  DB::table('nilai_akademis')->insert($insert_nilai);
    			  }
          } catch (\Illuminate\Database\QueryException $ex) {
            return Redirect::back()->withErrors('Gagal melakukan input data.<br>'.$ex->getMessage());
          }

          //peringkat
          try {
            if(!empty($results) && $results->count() > 0){
              foreach ($results as $col) {
                set_time_limit(0);
                if($col->semester_1_peringkat){
                  $insert_peringkat[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 1,
                    'peringkat' => $col->semester_1_peringkat,
                    'jumlah_siswa' => $col->semester_1_jumlah_siswa
                  ];
                }
                if($col->semester_2_peringkat){
                  $insert_peringkat[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 2,
                    'peringkat' => $col->semester_2_peringkat,
                    'jumlah_siswa' => $col->semester_2_jumlah_siswa
                  ];
                }
                if($col->semester_3_peringkat){
                  $insert_peringkat[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 3,
                    'peringkat' => $col->semester_3_peringkat,
                    'jumlah_siswa' => $col->semester_3_jumlah_siswa
                  ];
                }
                if($col->semester_4_peringkat){
                  $insert_peringkat[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 4,
                    'peringkat' => $col->semester_4_peringkat,
                    'jumlah_siswa' => $col->semester_4_jumlah_siswa
                  ];
                }
                if($col->semester_5_peringkat){
                  $insert_peringkat[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'semester' => 5,
                    'peringkat' => $col->semester_5_peringkat,
                    'jumlah_siswa' => $col->semester_5_jumlah_siswa
                  ];
                }
              }
            }

            if (!empty($insert_peringkat)) {
              DB::table('peringkat')->insert($insert_peringkat);
            }
          } catch (\Illuminate\Database\QueryException $ex) {
            return Redirect::back()->withErrors('Gagal melakukan input data.<br>'.$ex->getMessage());
          }

          //pilihan prodi
          try {
            if(!empty($results) && $results->count() > 0){
              foreach ($results as $col) {
                set_time_limit(0);
                if($col->pilihan_poltek_1 && $col->pilihan_poltek_1 != "0" && $col->pilihan_poltek_1 == "Politeknik Negeri Semarang"){
                  $id_prodi = DB::table('prodi')
                  ->select('kode_prodi')
                  ->where('nama_prodi', $col->pilihan_prodi_1)
                  ->get();

                  $insert_pilihan[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'pilihan_ke' => 1,
                    'pilihan_prodi' => $id_prodi[0]->kode_prodi
                  ];
                }
                if($col->pilihan_poltek_2 && $col->pilihan_poltek_2 != "0" && $col->pilihan_poltek_2 == "Politeknik Negeri Semarang"){
                  $id_prodi = DB::table('prodi')
                  ->select('kode_prodi')
                  ->where('nama_prodi', $col->pilihan_prodi_2)
                  ->get();

                  $insert_pilihan[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'pilihan_ke' => 2,
                    'pilihan_prodi' => $id_prodi[0]->kode_prodi
                  ];
                }
                if($col->pilihan_poltek_3 && $col->pilihan_poltek_3 != "0" && $col->pilihan_poltek_3 == "Politeknik Negeri Semarang"){
                  $id_prodi = DB::table('prodi')
                  ->select('kode_prodi')
                  ->where('nama_prodi', $col->pilihan_prodi_3)
                  ->get();

                  $insert_pilihan[] = [
                    'no_pendaftar' => $col->nomor_pendaftaran,
                    'pilihan_ke' => 3,
                    'pilihan_prodi' => $id_prodi[0]->kode_prodi
                  ];
                }
              }
            }

            if (!empty($insert_pilihan)) {
              DB::table('pilihan_mhs')->insert($insert_pilihan);
            }
          } catch (\Illuminate\Database\QueryException $ex) {
            return Redirect::back()->withErrors('Gagal melakukan input data.<br>'.$ex->getMessage());
          }
            //var_dump($col, '<br>');
        }, false);
        //$time_elapsed = microtime(true) - $start;
        //var_dump($time_elapsed);
        return Redirect::back()->with('successMessage', 'Data Akademis berhasil disimpan.');
  		}else{
        //$time_elapsed = microtime(true) - $start;
        //var_dump($time_elapsed);
        return Redirect::back()->withErrors('Error','Mohon periksa kembali file yang di-upload.');
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
        $data = Excel::filter('chunk')->load($path)->chunk(250, function($results){
          //$reader->ignoreEmpty();
          //$results = $reader->get();
          try {
            if(!empty($results) && $results->count() > 0){
      			  foreach ($results as $key => $value) {
                $insert[] = ['no_pendaftar' => $value->nomor_pendaftaran, 'nama_prestasi' => $value->nama_prestasi,
                'skala_prestasi' => $value->skala_prestasi, 'jenis_prestasi' => $value->jenis_prestasi,
                'juara_prestasi' => $value->juara_prestasi, 'tahun_prestasi' => $value->tahun_prestasi];
      			  }

      			  if(!empty($insert)){
      				  DB::table('nilai_non_akademis')->insert($insert);
      			  }
      		  }
          } catch (\Illuminate\Database\QueryException $ex) {
            return Redirect::back()->withErrors('Gagal melakukan input data.<br>'.$ex->getMessage());
          }
        }, false);
        return Redirect::back()->with('successMessage', 'Data Prestasi berhasil disimpan.');
  		}else{
        return Redirect::back()->withErrors('Error','Mohon periksa kembali file yang di-upload.');
      }
    }
}
