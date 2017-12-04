<?php

use Illuminate\Support\Facades\DB;
use App\Mahasiswa as mahasiswa;
use App\Peringkat as peringkat;
use App\PilihanMhs as pilihan_mhs;
use App\NilaiAkademis as nilai_akademis;
use App\NilaiNonAkademis as nilai_non_akademis;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin.dashboard.main');
});

//route data pendaftar
Route::get('/data_pendaftar', array('as' => 'olah_data', 'uses' => 'Mahasiswa\PengolahDataController@index'));

Route::post('/data_pendaftar/{id}', array('as' => 'olah_data.get_data', 'uses' => 'Mahasiswa\PengolahDataController@getDataMhs'));

Route::get('/data_pendaftar/{id}/details', array('as' => 'olah_data.details', 'uses' => 'Mahasiswa\PengolahDataController@detailMhs'));

Route::get('/data_pendaftar/olah_data', array('as' => 'olah_data.normalisasi', 'uses' => 'Mahasiswa\PengolahDataController@olahDataMhs'));

//route Input Data
Route::get('/input_data', array('as' => 'input', 'uses' => 'Mahasiswa\InputDataController@index'));

Route::post('/input_data/tambah_prestasi', array('as' => 'input.prestasi', 'uses' => 'Mahasiswa\InputDataController@inputDataNonAkademis'));

Route::post('/input_data/tambah_mahasiswa', array('as' => 'input.mahasiswa', 'uses' => 'Mahasiswa\InputDataController@inputDataAkademis'));

//route Kriteria
Route::get('/kelola_kriteria', array('as' => 'kriteria', 'uses' => 'Kriteria\KriteriaController@index'));

Route::post('/kelola_kriteria/tambah', array('as' => 'kriteria.tambah', 'uses' => 'Kriteria\KriteriaController@tambahKriteria'));

Route::get('/kelola_kriteria/{id}/edit', array('as' => 'kriteria.edit', 'uses' => 'Kriteria\KriteriaController@editKriteria'));

Route::post('/kelola_kriteria/{id}/ubahKriteria', array('as' => 'kriteria.ubah', 'uses' => 'Kriteria\KriteriaController@ubahKriteria'));

Route::get('/kelola_kriteria/{id}/hapusKriteria', array('as' => 'kriteria.hapus', 'uses' => 'Kriteria\KriteriaController@hapusKriteria'));

//route tabel perbandingan
Route::get('/kelola_tabel', array('as' => 'tabel', 'uses' => 'Tabel\TabelPerbandinganController@index'));

Route::post('/kelola_tabel/tambah', array('as' => 'tabel.tambah', 'uses' => 'Tabel\TabelPerbandinganController@inputNilaiPerbandingan'));

Route::get('/kelola_tabel/get_nilai/{id1}_{id2}', array('as' => 'tabel.get', 'uses' => 'Tabel\TabelPerbandinganController@getNilaiBanding'));

Route::get('/kelola_tabel/cek_ci', array('as' => 'tabel.cek_ci', 'uses' => 'Tabel\TabelPerbandinganController@periksaCr'));
/*Route::get('/kelola_tabel', function () {
    return view('admin.dashboard.tabel_perbandingan.TabelPerbandinganView');
});*/

//route prodi
Route::get('/kelola_prodi', array('as' => 'prodi', 'uses' => 'Prodi\ProdiController@index'));

Route::post('/kelola_prodi/tambah', array('as' => 'prodi.tambah', 'uses' => 'Prodi\ProdiController@tambahProdi'));

Route::get('/kelola_prodi/{id}/edit', array('as' => 'prodi.edit', 'uses' => 'Prodi\ProdiController@editProdi'));

Route::post('/kelola_prodi/{id}/ubahProdi', array('as' => 'prodi.ubah', 'uses' => 'Prodi\ProdiController@ubahProdi'));

Route::get('/kelola_prodi/{id}/hapusProdi', array('as' => 'prodi.hapus', 'uses' => 'Prodi\ProdiController@hapusProdi'));

//route saran penerimaan
Route::get('/saran_penerimaan', array('as' => 'moora', 'uses' => 'Moora\SaranPenerimaanController@index'));

Route::get('/saran_penerimaan/hasilkan_saran', array('as' => 'moora.saran', 'uses' => 'Moora\SaranPenerimaanController@saranPenerimaan'));

Route::post('/saran_penerimaan/{id}', array('as' => 'moora.get_data', 'uses' => 'Moora\SaranPenerimaanController@getDataPenerimaan'));
/*Route::get('/saran_penerimaan', function () {
    return view('admin.dashboard.saran_penerimaan.SaranPenerimaanView');
});
Auth::routes();*/

Route::get('/home', 'HomeController@index')->name('home');

//route CRUD jurusan
/*Route::group(['middleware' => ['web', 'auth', 'level:1']], function(){
	//index
	Route::get('/jurusan', array('as' => 'jurusan', 'uses' => 'Jurusan\jurusanController@index'));

	//form tambah jurusan
	Route::get('/jurusan/tambah', array('as' => 'jurusan.tambah', 'uses' => 'Jurusan\jurusanController@tambah'));

	//route menyimpan form jurusan
	Route::post('/jurusan/tambahJurusan', array('as' => 'jurusan.tambah.simpan', 'uses' => 'Jurusan\jurusanController@tambahJurusan'));

	//menghapus jurusan
	Route::get('jurusan/{id}/hapus', array('as' => 'jurusan.hapus', 'uses' => 'Jurusan\jurusanController@hapus'));
});*/
