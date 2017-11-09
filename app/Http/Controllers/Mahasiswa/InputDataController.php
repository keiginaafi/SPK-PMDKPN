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

    protected function inputDataAkademis(Request $request){
      //import to database
      if($request->hasFile('nilai_akademis')){

        $path = $request->file('nilai_akademis')->getRealPath();
  		  $data = Excel::load($path, function($reader) {})->get();

        if(!empty($data) && $data->count()){
  			  foreach ($data->toArray() as $key => $value) {
  				  if(!empty($value)){
  					  foreach ($value as $v) {
  						  $insert[] = ['title' => $v['title'], 'description' => $v['description']];
  					  }
  				  }
  			  }

  			  if(!empty($insert)){
  				  Item::insert($insert);
  				  return back()->with('success','Insert Record successfully.');
  			  }
  		  }
  		}else{
        return back()->with('error','Please Check your file, Something is wrong there.');
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
