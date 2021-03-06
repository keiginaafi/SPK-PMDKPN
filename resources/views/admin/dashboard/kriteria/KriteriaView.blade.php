@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Halaman Kelola Kriteria
		<small></small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Halaman Utama</a></li>
		<li class="active">Kelola Kriteria</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			@if (count($errors) > 0)
        <div class="alert alert-danger">
        	<strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
          	@foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
      	</div>
      @endif
			@if (Session::has('successMessage'))
			<div class="alert alert-success alert-dismissable">
				<p>{{ Session::get('successMessage') }}</p>
			</div>
			@endif
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
							@foreach ($kriteria as $item_kriteria)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td id="nama_kriteria">{{ $item_kriteria->nama_kriteria }}</td>
									<td>{{ $item_kriteria->bobot_kriteria }}</td>
									<td>
										<a class="btn btn-primary btn-flat btn-sm" href="{{{ URL::to('kelola_kriteria/'.$item_kriteria->id_kriteria.'/edit') }}}">
											<i class="fa fa-list"> Ubah </i>
										</a> |
										<button class="btn btn-danger btn-flat btn-sm hapus" href="{{{ action('Kriteria\KriteriaController@hapusKriteria', [$item_kriteria->id_kriteria]) }}}"
										data-confirm="Yakin ingin hapus kriteria {{ $item_kriteria->nama_kriteria }} ?" title="hapus">
											<i class="fa fa-trash"> Hapus </i>
										</button>
									</td>
								</tr>
							@endforeach
						</tbody>
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
					<h4 class="modal-title" id="inputKriteriaLabel"> Kriteria - Tambah </h4>
				</div>
				<div class="modal-body">
					<form id="formTambahKriteria" class="form-horizontal" role="form" method="POST" action="{{ url('/kelola_kriteria/tambah') }}">
						{{ csrf_field() }}
						<div class="form-group">
							<label class="col-md-4 control-label">Nama Kriteria</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="nama_kriteria"
								placeholder="Nama Kriteria" maxlength="30" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<div class="dol-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" id="button-reg">Tambahkan</button>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Urungkan</button>
				</div>
			</div>
		</div>
	</div>
	<meta name="_token" content="{{ csrf_token() }}" />
	<!-- End of Modal -->
	<script src="{{ asset('js/jquery-3.2.1.js') }}"></script>
	<script src="{{ asset('js/jquery-3.2.1.slim.js') }}"></script>
	<script>
		var deleteLinks = document.querySelectorAll('.hapus');
		for (var i = 0; i < deleteLinks.length; i++) {
			deleteLinks[i].addEventListener('click', function(event) {
				event.preventDefault();

				var choice = confirm(this.getAttribute('data-confirm'));

				if (choice) {
					window.location.href = this.getAttribute('href');
				}
			});
		}

		$(function(){
			$('#tambahKriteria').click(function(){
				$('input+small').text('');
				$('input').parent().removeClass('has-error');
				//$('select').parent().removeClass('has-error');

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
