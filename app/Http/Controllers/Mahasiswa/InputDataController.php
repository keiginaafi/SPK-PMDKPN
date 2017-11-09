<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
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

    protected function validator(array $data){
      $messages = [
        'kode_prodi.required' => 'Kode Program Studi dibutuhkan',
        'kode_prodi.unique' => 'Kode Program Studi telah digunakan',
        'nama_prodi.required' => 'Nama Program Studi dibutuhkan',
        'kuota_max.required' => 'Kuota Program Studi dibutuhkan',
      ];
      return Validator::make($data, [
        'kode_prodi' => 'required|unique:prodi',
        'nama_prodi' => 'required|max:40',
        'kuota_max' => 'required',
      ], $messages);
    }

    protected function tambah(array $data){
      $kuota_penerimaan = $data['kuota_max'] * (90/100);
      $kuota_sma = 0.5 * $kuota_penerimaan;
      $kuota_smk = 0.3 * $kuota_penerimaan;
      $kuota_cadangan = $kuota_penerimaan - ($kuota_sma + $kuota_smk);

      $prodi = new prodi();
      $prodi->kode_prodi = $data['kode_prodi'];
      $prodi->nama_prodi = $data['nama_prodi'];
      $prodi->kuota_max = $data['kuota_max'];
      $prodi->kuota_penerimaan = $kuota_penerimaan;
      $prodi->kuota_sma = $kuota_sma;
      $prodi->kuota_smk = $kuota_smk;
      $prodi->kuota_cadangan = $kuota_cadangan;

      //save, jika gagal abort
      if(!$prodi->save()){
        return Redirect::back()->withErrors('The server encountered an unexpected condition');
      }
    }

    public function tambahProdi(Request $request){
      $validator = $this->validator($request->all());
      if($validator->fails()){
        $this->throwValidationException(
          $request, $validator
        );
      }
      $this->tambah($request->all());
      //return response()->json($request->all(), 200);
      return Redirect::to('/kelola_prodi')->with('successMessage', 'Data Program Studi berhasil disimpan.');
    }
}
