<?php

namespace App\Http\Controllers\Prodi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Prodi as prodi;

class ProdiController extends Controller
{
    /*public function __construct(){
		$this->middleware('auth');
	}*/
	
	public function index(){
		$dataProdi = prodi::select(DB::raw("kode_prodi, nama_prodi, kuota_max"))
		->orderBy(DB::raw("kode_prodi"))
		->get();
		$data = array('prodi' => $dataProdi);
		return view('admin.dashboard.prodi.ProdiView', $data);
	}
	
	/*public function hapus($id){
		$prodiKode = prodi::where('prodiKode', '=', '$id')->first();
		if($prodiKode == null){
			App::abort(404);
		}
		$prodiKode->delete();
		return Redirect::action('Prodi\prodiController@index');
	}
	
	protected function validator(array $data){
		$messages = [
			'kode_prodi.required' => 'Kode Program Studi dibutuhkan',
			'nama_prodi.unique' => 'Nama Program Studi telah digunakan',
			'kuota_max.required' => 'Kuota Program Studi dibutuhkan',			
		];
		return Validator::make($data, [
			'kode_prodi' => 'required|unique:prodi',
			'nama_prodi' => 'required|max:60',
			'kuota_max' => 'required',
		], $messages);
	}
	
	protected function tambah(array $data){
		$prodi = new prodi();
		$prodi->prodiKode = $data['prodiKode'];
		$prodi->prodiNama = $data['prodiNama'];
		$prodi->prodiKodeJurusan = $data['prodiJurKode'];
		
		//save, jika gagal abort
		if(!$prodi->save()){
			App::abort(500);
		}
	}
	
	public function tambahProdi(Request $request){
		$validator = $this->validator($request->all());
		if($validator->fails()){
			$this->throwValidatorException(
				$request, $validator
			);
		}
		$this->tambah($request->all());
		return response()->json($request->all(), 200);
	}
	
	public function editProdi($id){
		$data = prodi::find($id);
		$jurusan = jurusan::orderBy('jurKode')->get();
		
		return view('admin.dashboard.prodi.editprodi' $data)->with('listjurusan', $jurusan);
	}
	
	public function simpanEdit($id){
		$input = Input::all();
		$messages = [
			'prodiKode.required' => 'Kode Program Studi dibutuhkan',
			'prodiNama.required' => 'nama Program Studi dibutuhkan',
			'prodiJurKode.required' => 'Kode Jurusan asal Program Studi dibutuhkan',
		];
		$validator = Validator:make($input, [
			'prodiKode' => 'required',
			'prodiNama' => 'required|max:60',
			'prodiJurKode' => 'required',
		], $messages);
		if($validator->fails()){
			//kembali ke halaman yg sama dengan pesan error
			return Redirect::back()->withErrors($validator)->withInput();
		}
		//bila sukses
		$editProdi = prodi::find($id);
		$editProdi->prodiKode = Input::get('prodiKode'); //atau $input['prodiKode']		
		$editProdi->prodiNama = $input['prodiNama'];
		$editProdi->prodiKodeJurusan = Input::get('prodiKodeJurusan');
		if(!$editProdi->save()){
			App::abort(500);
		}
		return Redirect::action('Prodi\prodiController@index')->with('successMessage', 
		'Data Prodi "'.Input::get('prodiNama').'" telah berhasil diubah')
	}*/
}