@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Halaman Ubah Program Studi
		<small></small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Halaman Utama</a></li>
		<li><a href="{{{ URL::to('kelola_prodi') }}}">Kelola Prodi</a></li>
		<li class="active">Edit Prodi</li>
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
					<h3 class="box-title"> Ubah Program Studi - {{ $nama_prodi }}
					</h3>
				</div>
				<div class="box-body">
					<form id="formEditProdi" class="col-md-4" role="form" method="POST" action="{{ url('/kelola_prodi/'.$kode_prodi.'/ubahProdi') }}">
						{{ csrf_field() }}
						<div class="form-group">
							<label class="control-label">Nama Program Studi</label>
							<div>
								<input type="text" class="form-control" name="nama_prodi"
								placeholder="Nama Program Studi" maxlength="51" value="{{ $nama_prodi }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Kuota SMA Program Studi</label>
							<div>
								<input type="number" class="form-control" name="kuota_sma"
								placeholder="Kuota SMA Program Studi" value="{{ $kuota_sma }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Kuota SMK Program Studi</label>
							<div>
								<input type="number" class="form-control" name="kuota_smk"
								placeholder="Kuota SMK Program Studi" value="{{ $kuota_smk }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<!--<div class="form-group">
							<label class="control-label">Kuota Cadangan Program Studi</label>
							<div>
								<input type="number" class="form-control" name="kuota_cadangan"
								placeholder="Kuota Cadangan Program Studi" value="" required></input>
								<small class="help-block"></small>
							</div>
						</div>-->
							<button type="submit" class="btn btn-primary" id="button-reg">Ubah data</button>
							<a class="btn btn-default" id="button-back" href="{{{ URL::to('kelola_prodi') }}}">Kembali</a>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('script')
	<script>
		$(document).on('submit', '#formEditProdi', function(e) {
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
                var input = '#formEditProdi input[name=' + key + ']';

                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
            });
        });
    });
	</script>
@endsection
