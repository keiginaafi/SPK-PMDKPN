<?php

namespace App\Http;

ini_set('memory_limit', '256M');

use Illuminate\Support\Facades\Redirect;
use DB;
use Response;
use App\Kriteria as kriteria;
use App\TabelPerbandingan as tabel_perbandingan;

class AHP
{
  public function hitungConsistency(){
    //cek apakah tabel perbandingan sudah diisi semua atau belum
    $jumlah_kriteria = DB::table('kriteria')->count();
    $jumlah_tabel = $jumlah_kriteria ** 2;
    $jumlah_perbandingan = DB::table('tabel_perbandingan')->count();
    if ($jumlah_perbandingan != $jumlah_tabel) {
      return $cr = -1;
    }

    //cari data kolom
    $kolom = DB::table('tabel_perbandingan')
    ->select('id_kriteria_2')
    ->distinct()
    ->get();

    //sum tiap kolom
    foreach ($kolom as $value) {
      set_time_limit(0);
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
      set_time_limit(0);
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
      set_time_limit(0);
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
      set_time_limit(0);
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

    return $cr;
  }
}
