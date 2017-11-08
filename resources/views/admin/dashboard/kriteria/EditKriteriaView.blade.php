@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Dashboard Admin</li>
		<li>Kelola Kriteria</li>
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
				<p>{{  Session::get('successMessage') }}</p>
			</div>
			@endif
			<div class="box">
				<div class="box-header">
					<h3 class="box-title"> Ubah Kriteria - {{ $nama_kriteria }}
					</h3>
				</div>
				<div class="box-body">
					<form id="formEditKriteria" class="col-md-4" role="form" method="POST" action="{{ url('/kelola_kriteria/'.$id_kriteria.'/ubahKriteria') }}">
						{{ csrf_field() }}
						<div class="form-group">
							<label class="control-label">Nama Kriteria</label>
							<div>
								<input type="text" class="form-control" name="nama_kriteria"
								placeholder="Nama Kriteria" maxlength="30" value="{{ $nama_kriteria }}" required></input>
								<small class="help-block"></small>
							</div>
						</div>
							<button type="submit" class="btn btn-primary" id="button-reg">Submit</button>
							<a class="btn btn-default" id="button-back" href="{{{ URL::to('kelola_kriteria') }}}">Kembali</a>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('script')
	<script>
		$(document).on('submit', '#formEditKriteria', function(e) {
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
                var input = '#formEditKriteria input[name=' + key + ']';

                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
            });
        });
    });
	</script>
@endsection
