<?php

namespace App\Http\Controllers\Tabel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Validator;
use App\Kriteria as kriteria;
use App\TabelPerbandingan as tabel_perbandingan;

class TabelPerbandinganController extends Controller
{
  /*public function __construct(){
    $this->middleware('auth');
  }*/

  public function index(){
    $kriteria = kriteria::select(DB::raw("id_kriteria, nama_kriteria"))
    ->orderBy(DB::raw("id_kriteria"))
    ->get();

    $nilai_perbandingan = tabel_perbandingan::select(DB::raw("id_kriteria_1, id_kriteria_2, nilai_banding"))
    ->get();
    /*foreach ($kriteria as $value) {
      var_dump($value);
    }*/
    $data = array(
      'kriteria' => $kriteria,
      'nilai_banding' => $nilai_perbandingan,
    );
    return view('admin.dashboard.tabel_perbandingan.TabelPerbandinganView', $data);
  }

  public function inputNilaiPerbandingan(Request $request){
    //var_dump($request->all());
    for ($i=0; $i < count($request->kriteria1); $i++) {
      /*echo $request->kriteria1[$i]." to ";
      echo $request->kriteria2[$i]." => ";
      echo $request->nilai[$i]." reverse => ";
      //hitung nilai kebalikan
      echo 1/$request->nilai[$i]."<br>";*/

      try {
        //perbandingan 1
        $tabel = tabel_perbandingan::updateOrCreate(
          ['id_kriteria_1' => $request->kriteria1[$i], 'id_kriteria_2' => $request->kriteria2[$i]],
          ['nilai_banding' => $request->nilai[$i], 'normalisasi' => 0]
        );

        //perbandingan kebalikan 1
        $tabel = tabel_perbandingan::updateOrCreate(
          ['id_kriteria_1' => $request->kriteria2[$i], 'id_kriteria_2' => $request->kriteria1[$i]],
          ['nilai_banding' => 1/$request->nilai[$i], 'normalisasi' => 0]
        );
      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors($ex->getMessage());
        //dd($ex->getMessage());
      }
    }
    return Redirect::to('/kelola_tabel')->with('successMessage', 'Data Perbandingan berhasil disimpan.');
  }

  public function getNilaiBanding($id1, $id2){
    $nilai = tabel_perbandingan::select("nilai_banding")
    ->where("id_kriteria_1", $id1)
    ->where("id_kriteria_2", $id2)
    ->get();
    //return success
    $response = array(
      'nilai' => $nilai[0]->nilai_banding,      
    );
    return Response::json($response);
  }
}
