@extends('admin.layout.master')
@section('breadcrump')
	<h1>
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li>Dashboard Admin</li>
		<li class="active">Data Pendaftar</li>
	</ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Data Pendaftar</h3>
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
					<div class="form-group">
						<div id="data_mhs" name="data-mhs">
						</div>
					</div>
					<!--
					<div class="tab-content">

							<div id="" class="tab-pane fade">

							</div>

					</div>-->
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
				$.ajaxSetup({
				  headers: {
				    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				  }
				});

				$.ajax({
					url: "data_pendaftar/" + prodi,
					type: "POST",
					dataType: 'json',
					data:{
						id: prodi,
					},
					success: function(data){
						console.log(data);

						var detail = "data_pendaftar";
						var edit = "edit";
						var view = '<table id="dataPendaftar" class="table table-bordered table-hover">';
							view += '<thead>';
								view += '<tr>';
									view += '<th>No. Pendaftar</th>';
	          			view += '<th>NISN</th>';
	          			view += '<th>Nama</th>';
	          			view += '<th>Jenis Kelamin</th>';
	          			view += '<th>Agama</th>';
	          			view += '<th>Tanggal Lahir</th>';
	          			view += '<th>Kota</th>';
	          			view += '<th>Tipe Sekolah</th>';
	          			view += '<th>Jenis Sekolah</th>';
	          			view += '<th>Akreditasi Sekolah</th>';
	          			view += '<th>Jurusan Asal</th>';
	          			view += '<th>Detail</th>';
								view += '</tr>';
							view += '</thead>';
							view += '<tbody>';
							$.each(data, function(i, d){
								view += '<tr>';
								$.each(d, function(j, e){
									view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
					        view += '<td>' + e + '</td>';
									view += '<td>';
					        view += '<a class="btn btn-primary btn-flat btn-sm" href="(' + detail + '/' + e + '/' + edit + ')">';
					        view += '<i class="fa fa-list"> Detail </i>';
					          view += '</a>';
					        view += '</td>';
								});
								view += '</tr>';
							});
							view += '</tbody>';
							view += '<tfoot>'
				        view += '<tr>';
				          view += '<th>No. Pendaftar</th>';
				          view += '<th>NISN</th>';
				         	view += '<th>Nama</th>';
				          view += '<th>Jenis Kelamin</th>';
				          view += '<th>Agama</th>';
				          view += '<th>Tanggal Lahir</th>';
				          view += '<th>Kota</th>';
				          view += '<th>Tipe Sekolah</th>';
				          view += '<th>Jenis Sekolah</th>';
				          view += '<th>Akreditasi Sekolah</th>';
				          view += '<th>Jurusan Asal</th>';
				          view += '<th>Detail</th>';
				        view += '</tr>';
				      view += '</tfoot>';
				    view += '</table>';
						$("#data_mhs").append(view);
					},
					error: function(){
						alert('data kosong');
					}
				});

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
							dataType: 'json',
							data:{
								id: prodi,
							},
							success: function(data){
								console.log(data);

								var detail = "data_pendaftar";
								var edit = "edit";
								var view = '<table id="dataPendaftar" class="table table-bordered table-hover">';
									view += '<thead>';
										view += '<tr>';
											view += '<th>No. Pendaftar</th>';
			          			view += '<th>NISN</th>';
			          			view += '<th>Nama</th>';
			          			view += '<th>Jenis Kelamin</th>';
			          			view += '<th>Agama</th>';
			          			view += '<th>Tanggal Lahir</th>';
			          			view += '<th>Kota</th>';
			          			view += '<th>Tipe Sekolah</th>';
			          			view += '<th>Jenis Sekolah</th>';
			          			view += '<th>Akreditasi Sekolah</th>';
			          			view += '<th>Jurusan Asal</th>';
			          			view += '<th>Pilihan</th>';
			          			view += '<th>Detail</th>';
										view += '</tr>';
									view += '</thead>';
									view += '<tbody>';
									$.each(data, function(i, d){
										view += '<tr>';
										$.each(d, function(j, e){
											view += '<td>' + e + '</td>';
										});
											view += '<td>';
											view += '<a class="btn btn-primary btn-flat btn-sm" href="' + detail + '/' + data[i].no_pendaftar + '/' + edit + '">';
											view += '<i class="fa fa-list"> Detail </i>';
												view += '</a>';
											view += '</td>';
										view += '</tr>';
									});
									view += '</tbody>';
									view += '<tfoot>'
						        view += '<tr>';
						          view += '<th>No. Pendaftar</th>';
						          view += '<th>NISN</th>';
						          view += '<th>Nama</th>';
						          view += '<th>Jenis Kelamin</th>';
						          view += '<th>Agama</th>';
						          view += '<th>Tanggal Lahir</th>';
						          view += '<th>Kota</th>';
						          view += '<th>Tipe Sekolah</th>';
						          view += '<th>Jenis Sekolah</th>';
						          view += '<th>Akreditasi Sekolah</th>';
						          view += '<th>Jurusan Asal</th>';
						          view += '<th>Pilihan</th>';
						          view += '<th>Detail</th>';
						        view += '</tr>';
						      view += '</tfoot>';
						    view += '</table>';
								$("#data_mhs").append(view);
							},
							error: function(data, ajaxOptions, thrownError){

								alert(data.status);
							}
						});
					}else{
						console.log(data);
						alert('Anda belum memilih mahasiswa');
						$("#data_mhs").html("");
					}
				});
			})
		</script>
@endsection
