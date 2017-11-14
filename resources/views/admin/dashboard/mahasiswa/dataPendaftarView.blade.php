@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Dashboard Admin</li>
		<li class="active">Data Pendaftar</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Data Pendaftar</h3>
				</div>
				<div class="box-body">
					<ul class="nav nav-pills">
						@foreach ($prodi as $item_prodi)
							<li><a data-toggle="pill" href="#{{ $item_prodi->kode_prodi }}">{{ $item_prodi->nama_prodi }}</a></li>
						@endforeach
					</ul>
					<div class="tab-content">
						@foreach ($prodi as $item_prodi)
							<div id="{{ $item_prodi->kode_prodi }}" class="tab-pane fade">
								<table id="dataPendaftar" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>No. Pendaftar</th>
											<th>NISN</th>
											<th>Nama</th>
											<th>Jenis Kelamin</th>
											<th>Agama</th>
											<th>Tanggal Lahir</th>
											<th>Kota</th>
											<th>Tipe Sekolah</th>
											<th>Jenis Sekolah</th>
											<th>Akreditasi Sekolah</th>
											<th>Jurusan Asal</th>
											<th>Detail</th>
										</tr>
									</thead>
									<tbody>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tbody>
									<tfoot>
										<tr>
											<th>No. Pendaftar</th>
											<th>NISN</th>
											<th>Nama</th>
											<th>Jenis Kelamin</th>
											<th>Agama</th>
											<th>Tanggal Lahir</th>
											<th>Kota</th>
											<th>Tipe Sekolah</th>
											<th>Jenis Sekolah</th>
											<th>Akreditasi Sekolah</th>
											<th>Jurusan Asal</th>
											<th>Detail</th>
										</tr>
									</tfoot>
								</table>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('script')
	<script src="{{ URL::asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ URL::asset('admin/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
	<script>
		$(function(){
			$('#dataPendaftar').DataTable({"pagelength": 100});
		});
	</script>
@endsection
