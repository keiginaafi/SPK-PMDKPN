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

  public function hitungConsistency(){
    //cek apakah tabel perbandingan sudah diisi semua atau belum
    $jumlah_kriteria = DB::table('kriteria')->count();
    $jumlah_tabel = $jumlah_kriteria ** 2;
    $jumlah_perbandingan = DB::table('tabel_perbandingan')->count();
    if ($jumlah_perbandingan != $jumlah_tabel) {
      $response = array(
        'fail' => 1,
        'input' => 'Tabel perbandingan belum diisi',
        'message' => 'Tidak bisa periksa consistency bila tabel ada yang kosong',
      );
      return Response::json($response);
    }

    //cari data kolom
    $kolom = DB::table('tabel_perbandingan')
    ->select('id_kriteria_2')
    ->distinct()
    ->get();

    //sum tiap kolom
    foreach ($kolom as $value) {
      try {
        $sum_kolom[] = DB::table('tabel_perbandingan')
        ->where('id_kriteria_2', $value->id_kriteria_2)
        ->sum('nilai_banding');
      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors($ex->getMessage());
      }
    }

    //normalisasi dg cara membagi tiap nilai dalam kolom dg sum kolomnya
    for ($i=0; $i < count($kolom); $i++) { //iterasi array kolom dari 0
      try {
        $normalisasi = tabel_perbandingan::where('id_kriteria_2', $kolom[$i]->id_kriteria_2)
        ->get();
      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors($ex->getMessage());
      }
      //var_dump($normalisasi);
      for ($j=0; $j < count($kolom); $j++) { //iterasi menyimpan nilai normalisasi
        $normalisasi[$j]->normalisasi = $normalisasi[$j]->nilai_banding / $sum_kolom[$i];
        if(!$normalisasi[$j]->save()){
          return Redirect::back()->withErrors('The server encountered an unexpected condition');
        }
      }
    }

    //cari data baris
    $baris = DB::table('tabel_perbandingan')
    ->select('id_kriteria_1')
    ->distinct()
    ->get();

    //cari average normalisasi tiap baris
    foreach ($baris as $value) {
      try {
        $avg_baris = DB::table('tabel_perbandingan')
        ->where('id_kriteria_1', $value->id_kriteria_1)
        ->avg('normalisasi');

        //simpan ke kriteria sebagai bobot kriteria tersebut
        $kriteria = kriteria::where('id_kriteria', $value->id_kriteria_1)
        ->update(['bobot_kriteria' => $avg_baris]);

      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors($ex->getMessage());
      }
    }

    //cek CI
    //cari nilai eigenvalue max
    $eigenmax = 0;
    for ($k=0; $k < count($sum_kolom) ; $k++) {
      try {
        $bobot = DB::table('kriteria')
        ->select('bobot_kriteria')
        ->where('id_kriteria', $baris[$k]->id_kriteria_1)
        ->get();

        $eigenmax = $eigenmax + ($sum_kolom[$k] * $bobot[0]->bobot_kriteria);
      } catch (\Illuminate\Database\QueryException $ex) {
        return Redirect::back()->withErrors($ex->getMessage());
      }
    }

    //cari CI
    $ci = ($eigenmax - count($baris)) / (count($baris) - 1);

    //cari cr berdasarkan ri berdasarkan jumlah kriteria
    switch (count($baris)) {
      case 1:
        $cr = $ci / 0;
        break;
      case 2:
        $cr = $ci / 0;
        break;
      case 3:
        $cr = $ci / 0.58;
        break;
      case 4:
        $cr = $ci / 0.9;
        break;
      case 5:
        $cr = $ci / 1.12;
        break;
      case 6:
        $cr = $ci / 1.24;
        break;
      case 7:
        $cr = $ci / 1.32;
        break;
      case 8:
        $cr = $ci / 1.41;
        break;
      case 9:
        $cr = $ci / 1.45;
        break;
      case 10:
        $cr = $ci / 1.49;
        break;
      case 11:
        $cr = $ci / 1.51;
        break;
      case 12:
        $cr = $ci / 1.58;
        break;
    }

    if ($cr > 0.1) {
      $response = array(
        'fail' => 1,
        'input' => 'Nilai consistency ratio lebih dari 10%',
        'message' => 'Tabel penilaian perlu diubah'
      );
      return Response::json($response);
    } else {
      $response = array(
        'fail' => 0,
        'input' => 'Nilai consistency ratio lebih kecil 10%',
        'message' => 'Tabel penilaian sudah konsisten'
      );
      return Response::json($response);
    }
  }
}
