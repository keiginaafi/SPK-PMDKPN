@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>		
		<li class="active">Kelola Prodi</li>
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
				<p>{{  Session::get('successMessage') }}</p>
			</div>
			@endif
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Kelola Program Studi
						<button class="btn btn-success btn-flat btn-sm" id="tambahProdi" title="Tambah" data-toggle="modal" data-target="#inputProdi" style="margin-left: 10px;">
							<i class="fa fa-plus"></i>
						</button>
					</h3>
				</div>
				<div class="box-body">
					<table id="dataProdi" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Kode Prodi</th>
								<th>Nama Prodi</th>
								<th>Kuota</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($prodi as $item_prodi)
								<tr>
									<td>{{ $item_prodi->kode_prodi }}</td>
									<td>{{ $item_prodi->nama_prodi }}</td>
									<td>{{ $item_prodi->kuota_max }}</td>
									<td>
										<a class="btn btn-primary btn-flat btn-sm" href="{{{ URL::to('kelola_prodi/'.$item_prodi->kode_prodi.'/edit') }}}">
											<i class="fa fa-list"> Edit </i>
										</a> |
										<a class="btn btn-danger btn-flat btn-sm" href="{{{ action('Prodi\ProdiController@hapusProdi', [$item_prodi->kode_prodi]) }}}"
										title="hapus"
										onclick="return confirm('Apakah anda yakin akan menghapus program studi
										{{{ $item_prodi->nama_prodi }}} ?')">
											<i class="fa fa-trash"> Delete </i>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th>Kode Prodi</th>
								<th>Nama Prodi</th>
								<th>Kuota</th>
								<th>Aksi</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Create -->
	<div class="modal fade" id="inputProdi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="inputProdiLabel"> Program Studi - Tambah </h4>
				</div>
				<div class="modal-body">
					<form id="formTambahProdi" class="form-horizontal" role="form" method="POST" action="{{ url('/kelola_prodi/tambah') }}">
						{{ csrf_field() }}
						<div class="form-group">
							<label class="col-md-4 control-label">Kode Program Studi</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="kode_prodi"
								placeholder="Kode Program Studi" maxlength="20" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Nama Program Studi</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="nama_prodi"
								placeholder="Nama Program Studi" maxlength="40" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">Kuota Program Studi</label>
							<div class="col-md-6">
								<input type="number" class="form-control" name="kuota_max"
								placeholder="Kuota Program Studi" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" id="button-reg">Submit</button>
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
	@section('script')
		<script>
			$(function(){
				$('#tambahProdi').click(function(){
					$('input+small').text('');
					$('input').parent().removeClass('has-error');

					$('#inputProdi').modal('show');
					//console.log('test');
					return false;
				});
			});

			$(document).on('submit', '#formTambahProdi', function(e) {
	        e.preventDefault();

	        $('input+small').text('');
	        $('input').parent().removeClass('has-error');

	        $.ajax({
	            method: $(this).attr('method'),
	            url: $(this).attr('action'),
	            data: $(this).serialize(),
	            dataType: "json"
	        })

	        .done(function(data) {
	            console.log(data);

	            $('.alert-success').removeClass('hidden');
	            $('#myModal').modal('hide');

	            //window.location.href='/kelola_prodi';
	        })

	        .fail(function(data) {
	            console.log(data.responeJSON);
	            $.each(data.responseJSON, function (key, value) {
	                var input = '#formTambahProdi input[name=' + key + ']';

	                $(input + '+small').text(value);
	                $(input).parent().addClass('has-error');
	            });
	        });
	    });
		</script>
	@endsection
@endsection
