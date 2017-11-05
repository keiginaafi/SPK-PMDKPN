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

Route::get('/data_pendaftar', function () {
    return view('admin.dashboard.mahasiswa.dataPendaftarView');
});

Route::get('/input_data', function () {
    return view('admin.dashboard.mahasiswa.inputDataView');
});

Route::get('/kelola_kriteria', function () {
    return view('admin.dashboard.kriteria.KriteriaView');
});

Route::get('/kelola_tabel', function () {
    return view('admin.dashboard.tabel_perbandingan.TabelPerbandinganView');
});

/*Route::get('/kelola_prodi', function () {
    return view('admin.dashboard.prodi.ProdiView');
});*/

Route::get('/kelola_prodi', array('as' => 'prodi', 'uses' => 'Prodi\ProdiController@index'));

Route::post('/kelola_prodi/tambah', array('as' => 'prodi.tambah', 'uses' => 'Prodi\ProdiController@tambahProdi'));

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
