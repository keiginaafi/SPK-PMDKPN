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

	public function hapusProdi($id){
		$kode_prodi = prodi::where('kode_prodi', '=', $id)->first();
		if($kode_prodi == null){
			return Redirect::back()->withErrors('Program Studi '.$id.' does not exist');
		}
		$kode_prodi->delete();
		return Redirect::action('Prodi\ProdiController@index')->with('successMessage',
		'Data Program Studi telah berhasil dihapus');
	}

	public function editProdi($id){
		$data = prodi::find($id);
		//$dataProdi = prodi::orderBy('kode_prodi')->get();

		return view('admin.dashboard.prodi.EditProdiView', $data);
	}

	public function ubahProdi($id){
		$input = Input::all();
		$messages = [
			'kode_prodi.required' => 'Kode Program Studi dibutuhkan',
			'kode_prodi.unique' => 'Kode Program Studi telah digunakan',
			'nama_prodi.required' => 'Nama Program Studi dibutuhkan',
			'kuota_max.required' => 'Kuota Program Studi dibutuhkan',
		];
		$validator = Validator::make($input, [
			'kode_prodi' => 'required|unique:prodi',
			'nama_prodi' => 'required|max:40',
			'kuota_max' => 'required',
		], $messages);
		if($validator->fails()){
			//kembali ke halaman yg sama dengan pesan error
			return Redirect::back()->withErrors($validator)->withInput();
		}
		//bila sukses
		$kuota_penerimaan = $input['kuota_max'] * (90/100);
		$kuota_sma = 0.5 * $kuota_penerimaan;
		$kuota_smk = 0.3 * $kuota_penerimaan;
		$kuota_cadangan = $kuota_penerimaan - ($kuota_sma + $kuota_smk);

		$editProdi = prodi::find($id);
		$editProdi->kode_prodi = Input::get('kode_prodi'); //atau $input['prodiKode']
		$editProdi->nama_prodi = $input['nama_prodi'];
		$editProdi->kuota_max = $input['kuota_max'];
		$editProdi->kuota_penerimaan = $kuota_penerimaan;
		$editProdi->kuota_sma = $kuota_sma;
		$editProdi->kuota_smk = $kuota_smk;
		$editProdi->kuota_cadangan = $kuota_cadangan;


		if(!$editProdi->save()){
			return Redirect::back()->withErrors('The server encountered an unexpected condition');
		}
		return Redirect::action('Prodi\ProdiController@index')->with('successMessage',
		'Data Program Studi "'.Input::get('nama_prodi').'" telah berhasil diubah');
	}
}
