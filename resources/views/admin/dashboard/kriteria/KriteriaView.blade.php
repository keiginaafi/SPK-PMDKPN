@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Dashboard Admin</li>		
		<li class="active">Kelola Kriteria</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">				
					<h3 class="box-title">Kelola Kriteria
						<button class="btn btn-success btn-flat btn-sm" id="tambahKriteria" title="Tambah" data-toggle="modal" data-target="#inputKriteria" style="margin-left: 10px;">
							<i class="fa fa-plus"></i>
						</button>
					</h3>					
				</div>				
				<div class="box-body">
					<table id="dataKriteria" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>No.</th>								
								<th>Kriteria</th>
								<th>Bobot</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tbody>
						<tfoot>
							<tr>
								<th>No.</th>								
								<th>Kriteria</th>
								<th>Bobot</th>
								<th>Aksi</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="inputKriteria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="inputKriteriaLabel"> Kriteria - Tambah</h4>
				</div>
				<div class="modal-body">
					<form id="formTambahKriteria" class="form-horizontal" role="form" method="POST" action="{{ url('/prodi/tambah') }}">
						{{ csrf_field() }}						
						<div class="form-group">
							<label class="col-md-4 control-label">Nama</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="kriteriaNama" 
								placeholder="Nama Kriteria" maxlength="60" required></input>
								<small class="help-block"></small>
							</div>
						</div>						
						<div class="form-group">					
							<div class="dol-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" id="button-reg">Simpan</button>						
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- End of Modal -->
@endsection
@section('script')
	<script>
		$(function(){
			$('#tambahKriteria').click(function(){
				$('input+small').text('');
				$('input').parent().removeClass('has-error');
				$('select').parent().removeClass('has-error');
				
				$('#inputKriteria').modal('show');
				//console.log('test');
				return false;
			});
		});
	</script>
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