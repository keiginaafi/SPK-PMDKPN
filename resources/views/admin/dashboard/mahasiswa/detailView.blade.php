@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Dashboard Admin</li>
		<li class="active">Kelola Prodi</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Nama Mahasiswa
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
