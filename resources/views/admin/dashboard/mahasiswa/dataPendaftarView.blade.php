@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li class="active">Data Pendaftar</li>
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
			<div id="message">
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Data Pendaftar
						<button class="btn btn-primary btn-flat btn-sm" id="normalisasiMhs" title="Normalisasi" style="margin-left: 10px;">
							Normalisasi Data Pendaftar
						</button>
					</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label for="select_prodi">Pilih Program Studi</label>
						<select id="select_prodi" name="pilih_prodi" class="form-control">
							<option value="NONE">Pilih Program Studi</option>
							@foreach ($prodi as $item_prodi)
								<option value="{{ $item_prodi->kode_prodi }}">{{ $item_prodi->nama_prodi }}</option>
							@endforeach
						</select>
					</div>
					<table id="table_data" class="table table-bordered table-hover col-md-12">
						<thead>
							<tr>
								<th>No. Pendaftar</th>
								<th>NISN</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Agama</th>
								<th>Tanggal Lahir</th>
								<th>Kota</th>
								<th>Tipe Sekolah</th>
								<th>Jenis Sekolah</th>
								<th>Akreditasi Sekolah</th>
								<th>Jurusan Asal</th>
								<th>Detail</th>
							</tr>
						</thead>
						<tbody id="data_mhs" name="data_mhs">
						</tbody>
						<tfoot>
							<tr>
								<th>No. Pendaftar</th>
								<th>NISN</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Agama</th>
								<th>Tanggal Lahir</th>
								<th>Kota</th>
								<th>Tipe Sekolah</th>
								<th>Jenis Sekolah</th>
								<th>Akreditasi Sekolah</th>
								<th>Jurusan Asal</th>
								<th>Detail</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	<meta name="_token" content="{{ csrf_token() }}" />
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
		<script src="{{ asset('js/jquery-3.2.1.js') }}"></script>
		<script src="{{ asset('js/jquery-3.2.1.slim.js') }}"></script>
		<script>

			$(document).ready(function(){
				var prodi = $("#select_prodi").val();
				if(prodi != "NONE"){
					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
						}
					});

					$.ajax({
						url: "data_pendaftar/" + prodi,
						type:"POST",
						cache: false,
						dataType: 'json',
						data:{
							id: prodi,
						},
						success: function(data){
							console.log(data);
							$("#data_mhs tr").last().remove();
							var detail = "data_pendaftar";
							var details = "details";
							$.each(data, function(i, d){
								var view = '<tr>';
								$.each(d, function(j, e){
									view += '<td>' + e + '</td>';
								});
								view += '<td>';
								view += '<a class="btn btn-primary btn-flat btn-sm" href="' + detail + '/' + data[i].no_pendaftar + '/' + details + '">';
								view += '<i class="fa fa-list"> Detail </i>';
								view += '</a>';
								view += '</td>';
								view += '</tr>';
								$("#data_mhs").append(view);
							});
						},
						error: function(data, ajaxOptions, thrownError){

							alert(data.status);
						}
					});
				}

				$("#select_prodi").change(function(){
					var prodi = $("#select_prodi").val();
					if(prodi != "NONE"){
						$.ajaxSetup({
						  headers: {
						    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
						  }
						});

						$.ajax({
							url: "data_pendaftar/" + prodi,
							type:"POST",
							cache: false,
							dataType: 'json',
							data:{
								id: prodi,
							},
							success: function(data){
								console.log(data);
								$("#data_mhs tr").last().remove();
								var detail = "data_pendaftar";
								var details = "details";
								$.each(data, function(i, d){
									var view = '<tr>';
									$.each(d, function(j, e){
										view += '<td>' + e + '</td>';
									});
									view += '<td>';
									view += '<a class="btn btn-primary btn-flat btn-sm" href="' + detail + '/' + data[i].no_pendaftar + '/' + details + '">';
									view += '<i class="fa fa-list"> Detail </i>';
									view += '</a>';
									view += '</td>';
									view += '</tr>';
									$("#data_mhs").append(view);
								});
							},
							error: function(data, ajaxOptions, thrownError){
								alert(data.status);
							}
						});
					}else{

						alert('Anda belum memilih prodi');
						$("#data_mhs").html("");
					}
				});

				$("#normalisasiMhs").click(function(){
					var confirm = window.confirm("Mulai Normalisasi Data?");
					if (confirm == true) {
						$.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
							}
						});

						$.ajax({
							url: "data_pendaftar/olah_data",
							type:"GET",
							cache: false,
							dataType: 'json',
							success: function(data){
								console.log(data);
								var message = '<div class="alert alert-success alert-dismissable">';
								message += '<p>' + data.input + '</p>';
								message += '<p>' + data.message + '</p>';
								message += '</div>';
								$('#message').append(message);
							},
							error: function(data){
								console.log(data);
								var message = '<div class="alert alert-danger">';
								message += '<p>' + data.status + '</p>';
								message += '<p>' + data.Error + '</p>';
								message += '</div>';
								$('#message').append(message);
							}
						});
					}
				});
			})
		</script>
@endsection
