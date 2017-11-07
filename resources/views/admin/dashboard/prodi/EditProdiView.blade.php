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
							<label class="control-label">Kode Program Studi</label>
							<div>
								<input type="text" class="form-control" name="kode_prodi"
								placeholder="Kode Program Studi" maxlength="20" value="{{ $kode_prodi }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Nama Program Studi</label>
							<div>
								<input type="text" class="form-control" name="nama_prodi"
								placeholder="Nama Program Studi" maxlength="40" value="{{ $nama_prodi }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label">Kuota Program Studi</label>
							<div>
								<input type="number" class="form-control" name="kuota_max"
								placeholder="Kuota Program Studi" value="{{ $kuota_max }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
							<button type="submit" class="btn btn-primary" id="button-reg">Submit</button>
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
                var input = '#formTambahProdi input[name=' + key + ']';

                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
            });
        });
    });
	</script>
@endsection
