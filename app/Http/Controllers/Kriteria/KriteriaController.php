<?php

namespace App\Http\Controllers\Kriteria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Kriteria as kriteria;
use App\TabelPerbandingan as tabel_perbandingan;

class KriteriaController extends Controller
{
  public function __construct(){
    $this->middleware('auth');
  }

  public function index(){
    $dataKriteria = kriteria::select(DB::raw("id_kriteria, nama_kriteria, bobot_kriteria"))
    ->orderBy(DB::raw("id_kriteria"))
    ->get();
    $data = array('kriteria' => $dataKriteria);
    return view('admin.dashboard.kriteria.KriteriaView', $data);
  }

  protected function validator(array $data){
    $messages = [
      'nama_kriteria.required' => 'Nama Kriteria dibutuhkan',
      'nama_kriteria.unique' => 'Nama Kriteria sudah dipakai',
    ];
    return Validator::make($data, [
      'nama_kriteria' => 'required|unique:kriteria|max:30',
    ], $messages);
  }

  protected function tambah(array $data){
    $kriteria = new kriteria();
    $kriteria->nama_kriteria = $data['nama_kriteria'];
    $kriteria->bobot_kriteria = 0;

    //save, jika gagal abort
    if(!$kriteria->save()){
      return Redirect::back()->withErrors('The server encountered an unexpected condition');
    }

    //input ke tabel Perbandingan
    $tabel = new tabel_perbandingan();
    $tabel->id_kriteria_1 = $kriteria->id_kriteria;
    $tabel->id_kriteria_2 = $kriteria->id_kriteria;
    $tabel->nilai_banding = 1;
    $tabel->normalisasi = 0;
    $tabel->save();
  }

  public function tambahKriteria(Request $request){
    $validator = $this->validator($request->all());
    if($validator->fails()){
      $this->throwValidationException(
        $request, $validator
      );
    }
    $this->tambah($request->all());
    //return response()->json($request->all(), 200);
    return Redirect::to('/kelola_kriteria')->with('successMessage', 'Data Kriteria berhasil disimpan.');
  }

  public function hapusKriteria($id){
    $kriteria = kriteria::where('id_kriteria', '=', $id)->first();
    $tabel_1 = tabel_perbandingan::where('id_kriteria_1', $id);
    $tabel_2 = tabel_perbandingan::where('id_kriteria_2', $id);
    if($kriteria == null){
      return Redirect::back()->withErrors('Kriteria does not exist');
    }
    $kriteria->delete();
    $tabel_1->delete();
    $tabel_2->delete();
    return Redirect::action('Kriteria\KriteriaController@index')->with('successMessage',
    'Data Kriteria telah berhasil dihapus');
  }

  public function editKriteria($id){
    $data = kriteria::find($id);
    //$dataProdi = prodi::orderBy('kode_prodi')->get();

    return view('admin.dashboard.kriteria.EditKriteriaView', $data);
  }

  public function ubahKriteria($id){
    $input = Input::all();
    $messages = [
      'nama_kriteria.required' => 'Nama Kriteria dibutuhkan',
    ];
    $validator = Validator::make($input, [
      'nama_kriteria' => 'required|unique:kriteria|max:30',
    ], $messages);
    if($validator->fails()){
      //kembali ke halaman yg sama dengan pesan error
      return Redirect::back()->withErrors($validator)->withInput();
    }
    //bila sukses
    $editKriteria = kriteria::find($id);
    $editKriteria->nama_kriteria = $input['nama_kriteria'];

    if(!$editKriteria->save()){
      return Redirect::back()->withErrors('The server encountered an unexpected condition');
    }
    return Redirect::action('Kriteria\KriteriaController@index')->with('successMessage',
    'Data Kriteria "'.Input::get('nama_kriteria').'" telah berhasil diubah');
  }
}
