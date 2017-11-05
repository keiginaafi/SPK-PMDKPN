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
						<li class="active"><a data-toggle="pill" href="#home">Home</a></li>
						<li><a data-toggle="pill" href="#menu1">Menu 1</a></li>
						<li><a data-toggle="pill" href="#menu2">Menu 2</a></li>
					</ul>
					<div class="tab-content">
						<div id="home" class="tab-pane fade in active">
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
						<div id="menu1" class="tab-pane fade">
							<h3>Menu 1</h3>
							<p>Some content in menu 1.</p>
						</div>
						<div id="menu2" class="tab-pane fade">
							<h3>Menu 2</h3>
							<p>Some content in menu 2.</p>
						</div>
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
