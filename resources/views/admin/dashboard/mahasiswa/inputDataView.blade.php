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
					<h3 class="box-title">Unggah Data</h3>
				</div>
				<div class="box-body">
					<div class="col-md-1">
					</div>
					<div class="col-md-4" style="border: 1px solid grey;">
						<form id="dataAkademis" role="form" method="POST" action="{{ url('/input_data/tambah_mahasiswa') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-group" style="padding-top: 15px;">
								<label for="nilaiAkademis" class="control-label">Nilai Akademis</label>
								<input type="file" class="form-control-file" id="nilaiAkademis" name="nilai_akademis" style="padding-top: 5px;"></input>
								<p class="help-block" style="padding-top: 5px;">Pilih file nilai akademis yang akan diunggah. (.xls)</p>
							</div>
							<div class="form-group" style="padding-top: 15px;">
								<center>
									<button type="submit" class="btn btn-primary" name="submitAkademis" value="submitAkademis">Unggah Data</button>
								</center>
							</div>
						</form>
					</div>
					<div class="col-md-2">
					</div>
					<div class="col-md-4" style="border: 1px solid grey;">
						<form id="dataNonAkademis" role="form" method="POST" action="{{ url('/input_data/tambah_prestasi') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-group" style="padding-top: 15px;">
								<label for="nilaiNonAkademis" class="control-label">Nilai Non Akademis</label>
								<input type="file" class="form-control-file" id="nilaiNonAkademis" name="nilai_non_akademis" style="padding-top: 5px;"></input>
								<p class="help-block" style="padding-top: 5px;">Pilih file prestasi yang akan diunggah. (.xls)</p>
							</div>
							<div class="form-group" style="padding-top: 15px;">
								<center>
									<button type="submit" class="btn btn-primary" name="submitNonAkademis" value="submitNonAkademis">Unggah Data</button>
								</center>
							</div>
						</form>
					</div>
				</div>
				<div class="box-header" style="padding-top: 35px;">
					<h3 class="box-title">Jumlah Data</h3>
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
							@if($periode != 0)
								@for($i = 1; $i <= count($periode); $i++)
									<tr>
										<td>{{ $i }}</td>
										<td>{{ $periode }}</td>
										<td>{{ $mahasiswa }}</td>
									</tr>
								@endfor
							@endif
						</tbody>
					</table>
					</center>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('script')
	<script src="{{ URL::asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ URL::asset('admin/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
	<script>
		$(document).on('submit', '#dataNonAkademis', function(e) {
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
                var input = '#dataNonAkademis input[name=' + key + ']';

                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
            });
        });
    });
	</script>
@endsection
