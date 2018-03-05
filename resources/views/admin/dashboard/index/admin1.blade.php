@extends ('admin.layout.master')
@section ('breadcrump')
  <h1>
    Halaman Utama
    <small></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('admin')}}"><i class="fa fa-dashboard active"></i>Halaman Utama</a></li>    
  </ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Sistem Pendukung Keputusan - Penerimaan Mahasiswa Jalur PMDK-PN
					</h3>
          <div class="box-tools pull-right">
            <button data-original-title="Collapse" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="">
              <i class="fa fa-minus"></i>
            </button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i>
            </button>
          </div>
				</div>
				<div style="display: block" class="box-body">

          <justified>Aplikasi ini adalah sistem pendukung keputusan yang menghasilkan daftar
          mahasiswa yang berhak diterima masuk ke Politeknik Negeri Semarang melalui jalur
          Penelusuran Minat dan Bakat Politeknik Negeri menggunakan metode Moora dan AHP.</justified>
          <br><br><br>
          <p>Petunjuk Penggunaan</p>
          <ol>
            <li>Menu Data Pendaftar digunakan untuk input data pendaftar, melihat data pendaftar, dan menghasilkan data yang digunakan pada penerimaan calon mahasiswa</li>
            <li>Menu Program Studi digunakan untuk mengelola data Program Studi</li>
            <li>Menu Kriteria digunakan untuk mengelola kriteria penerimaan dan menghasilkan bobot kriteria</li>
            <li>Menu Saran Penerimaan digunakan untuk menghasilkan saran penerimaan dan melihat hasil penerimaan berdasarkan metode Moora dan AHP</li>
          </ol>
				</div>
			</div>
		</div>
	</div>
@endsection
