@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Dashboard Admin</li>		
		<li class="active">Input Data</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Input Data</h3>
				</div>				
				<div class="box-body">					
					<div class="col-md-1">						
					</div>
					<div class="col-md-4" style="border: 1px solid grey;">
						<form id="dataAkademis" role="form" method="POST" action="" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-group" style="padding-top: 15px;">
								<label for="nilaiAkademis" class="control-label">Nilai Akademis</label>
								<input type="file" class="form-control-file" id="nilaiAkademis" name="nilaiAkademis" style="padding-top: 5px;"></input>
								<p class="help-block" style="padding-top: 5px;">Pilih file berita acara yang akan diunggah.</p>
							</div>
							<div class="form-group" style="padding-top: 15px;">
								<center>
									<button type="submit" class="btn btn-primary" name="submitAkademis" value="submitAkademis">Submit</button>
								</center>
							</div>
						</form>
					</div>
					<div class="col-md-2">
					</div>
					<div class="col-md-4" style="border: 1px solid grey;">
						<form id="dataNonAkademis" role="form" method="POST" action="" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-group" style="padding-top: 15px;">
								<label for="nilaiNonAkademis" class="control-label">Nilai Non Akademis</label>
								<input type="file" class="form-control-file" id="nilaiNonAkademis" name="nilaiNonAkademis" style="padding-top: 5px;"></input>
								<p class="help-block" style="padding-top: 5px;">Pilih file berita acara yang akan diunggah.</p>
							</div>
							<div class="form-group" style="padding-top: 15px;">
								<center>
									<button type="submit" class="btn btn-primary" name="submitNonAkademis" value="submitNonAkademis">Submit</button>
								</center>
							</div>
						</form>
					</div>				
				</div>
				<div class="box-header" style="padding-top: 35px;">
					<h3 class="box-title">History</h3>
				</div>
				<div class="box-body">
					<center>
					<table id="histroy" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>No.</th>								
								<th>Periode</th>
								<th>Jumlah Pendaftar</th>								
							</tr>
						</thead>
						<tbody>
							<td></td>
							<td></td>
							<td></td>							
						</tbody>
						<tfoot>
							<tr>
								<th>No.</th>								
								<th>Periode</th>
								<th>Jumlah Pendaftar</th>
							</tr>
						</tfoot>
					</table>
					</center>
				</div>
			</div>
		</div>
	</div>
@endsection
<!--@section('script')
	<script src="{{ URL::asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ URL::asset('admin/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
	<script>
		$(function(){
			$('#dataMahasiswa').DataTable({"pagelength": 100});
		});
	</script>
@endsection-->