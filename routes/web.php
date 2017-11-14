<?php

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

/*Route::get('/data_pendaftar', function () {
    return view('admin.dashboard.mahasiswa.dataPendaftarView');
});*/
//route data pendaftar
Route::get('/data_pendaftar', array('as' => 'olah_data', 'uses' => 'Mahasiswa\PengolahDataController@index'));

/*Route::get('/input_data', function () {
    return view('admin.dashboard.mahasiswa.inputDataView');
});*/

//route Input Data
Route::get('/input_data', array('as' => 'input', 'uses' => 'Mahasiswa\InputDataController@index'));

Route::post('/input_data/tambah_prestasi', array('as' => 'input.prestasi', 'uses' => 'Mahasiswa\InputDataController@inputDataNonAkademis'));

Route::post('/input_data/tambah_mahasiswa', array('as' => 'input.mahasiswa', 'uses' => 'Mahasiswa\InputDataController@inputDataAkademis'));

/*Route::get('/kelola_kriteria', function () {
    return view('admin.dashboard.kriteria.KriteriaView');
});*/

//route Kriteria
Route::get('/kelola_kriteria', array('as' => 'kriteria', 'uses' => 'Kriteria\KriteriaController@index'));

Route::post('/kelola_kriteria/tambah', array('as' => 'kriteria.tambah', 'uses' => 'Kriteria\KriteriaController@tambahKriteria'));

Route::get('/kelola_kriteria/{id}/edit', array('as' => 'kriteria.edit', 'uses' => 'Kriteria\KriteriaController@editKriteria'));

Route::post('/kelola_kriteria/{id}/ubahKriteria', array('as' => 'kriteria.ubah', 'uses' => 'Kriteria\KriteriaController@ubahKriteria'));

Route::get('/kelola_kriteria/{id}/hapusKriteria', array('as' => 'kriteria.hapus', 'uses' => 'Kriteria\KriteriaController@hapusKriteria'));


Route::get('/kelola_tabel', function () {
    return view('admin.dashboard.tabel_perbandingan.TabelPerbandinganView');
});

/*Route::get('/kelola_prodi', function () {
    return view('admin.dashboard.prodi.ProdiView');
});*/

//route prodi
Route::get('/kelola_prodi', array('as' => 'prodi', 'uses' => 'Prodi\ProdiController@index'));

Route::post('/kelola_prodi/tambah', array('as' => 'prodi.tambah', 'uses' => 'Prodi\ProdiController@tambahProdi'));

Route::get('/kelola_prodi/{id}/edit', array('as' => 'prodi.edit', 'uses' => 'Prodi\ProdiController@editProdi'));

Route::post('/kelola_prodi/{id}/ubahProdi', array('as' => 'prodi.ubah', 'uses' => 'Prodi\ProdiController@ubahProdi'));

Route::get('/kelola_prodi/{id}/hapusProdi', array('as' => 'prodi.hapus', 'uses' => 'Prodi\ProdiController@hapusProdi'));

/*Route::get('/kelola_prodi/edit', function () {
    return view('admin.dashboard.prodi.EditView');
});*/

Route::get('/saran_penerimaan', function () {
    return view('admin.dashboard.saran_penerimaan.SaranPenerimaanView');
});
Auth::routes();

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
