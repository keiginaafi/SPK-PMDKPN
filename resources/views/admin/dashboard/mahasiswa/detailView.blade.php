@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Data Pendaftar</li>
		<li class="active">Detail Pendaftar</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">{{ $data_mhs[0]->mahasiswa->nama }}	</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label class="control-label">Data Akademis</label>
						<table id="dataAkademis" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>Semester</th>
									<th>Jenis Nilai</th>
									<th>Mata Pelajaran</th>
									<th>Nilai</th>
								</tr>
							</thead>
							<tbody>
									@foreach($data_mhs as $akademis)
									<tr>
										<td>{{ $akademis->semester }}</td>
										<td>{{ $akademis->jenis_nilai }}</td>
										<td>{{ $akademis->mapel }}</td>
										<td>{{ $akademis->nilai_mapel }}</td>
									</tr>
									@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th>Semester</th>
									<th>Jenis Nilai</th>
									<th>Mata Pelajaran</th>
									<th>Nilai</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="form-group">
						<label class="control-label">Data Prestasi</label>
						<table id="dataPrestasi" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>Nama Prestasi</th>
									<th>Skala Prestasi</th>
									<th>Jenis Prestasi</th>
									<th>Juara Prestasi</th>
									<th>Tahun Prestasi</th>
								</tr>
							</thead>
							<tbody>
								@if($data_mhs[0]->mahasiswa->nilai_non_akademis != NULL)
									@foreach($data_mhs[0]->mahasiswa->nilai_non_akademis as $prestasi)
									<tr>
										<td>{{ $prestasi->nama_prestasi }}</td>
										<td>{{ $prestasi->skala_prestasi }}</td>
										<td>{{ $prestasi->jenis_prestasi }}</td>
										<td>{{ $prestasi->juara_prestasi }}</td>
										<td>{{ $prestasi->tahun_prestasi }}</td>
									</tr>
									@endforeach
								@endif
							</tbody>
							<tfoot>
								<tr>
									<th>Nama Prestasi</th>
									<th>Skala Prestasi</th>
									<th>Jenis Prestasi</th>
									<th>Juara Prestasi</th>
									<th>Tahun Prestasi</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="form-group">
						<div class="col-md-10"></div>
						<a class="btn btn-primary col-md-2" id="button-back" href="{{{ URL::to('data_pendaftar') }}}">Kembali</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
