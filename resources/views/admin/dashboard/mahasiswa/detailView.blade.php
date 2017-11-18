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
					<h3 class="box-title">{{ $data_mhs[0]->nama }}
						<button class="btn btn-success btn-flat btn-sm" id="tambahProdi" title="Tambah" data-toggle="modal" data-target="#inputProdi" style="margin-left: 10px;">
							<i class="fa fa-plus"></i>
						</button>
					</h3>
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
								<tr>
									@foreach($data_mhs as $akademis)
										<td>{{ $akademis->semester }}</td>
										<td>{{ $akademis->jenis_nilai }}</td>
										<td>{{ $akademis->mapel }}</td>
										<td>{{ $akademis->nilai }}</td>
									@endforeach
								</tr>
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
									<th>Kode Prodi</th>
									<th>Nama Prodi</th>
									<th>Kuota</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>									
								</tr>
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
