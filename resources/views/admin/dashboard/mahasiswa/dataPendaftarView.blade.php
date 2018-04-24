@extends('admin.layout.master')
@section('breadcrump')
<style type="text/css">
	.modal {
	    display:    none;
	    position:   fixed;
	    z-index:    1000;
	    top:        0;
	    left:       0;
	    height:     100%;
	    width:      100%;
	    background: rgba( 255, 255, 255, .8 )
	                url('images/tenor.gif')
	                50% 50%
	                no-repeat;
	}

	/* When the body has the loading class, we turn
	   the scrollbar off with overflow:hidden */
	body.loading {
	    overflow: hidden;
	}

	/* Anytime the body has the loading class, our
	   modal element will be visible */
	body.loading .modal {
	    display: block;
	}
</style>
	<h1>
		Halaman Data Pendaftar
		<small></small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="{{route('admin')}}"><i class="fa fa-dashboard"></i>Halaman Utama</a></li>
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
			<div class="box" style="overflow: auto; min-width: 100%; width: auto; height: 100%;">
				<div class="box-header">
					<h3 class="box-title">Data Pendaftar
						@if(Auth::user()->level<=1)
							<button class="btn btn-primary btn-flat btn-sm" id="normalisasiMhs" title="Normalisasi" style="margin-left: 10px;">
								Normalisasi Data Pendaftar
							</button>
					  @endif
					</h3>
				</div>
				<div id="boxes" class="box-body">
					<div class="form-group">
						<label for="select_prodi">Pilih Program Studi</label>
						<select id="select_prodi" name="pilih_prodi" class="form-control">
							<option value="NONE">Pilih Program Studi</option>
							@foreach ($prodi as $item_prodi)
								<option value="{{ $item_prodi->kode_prodi }}">{{ $item_prodi->nama_prodi }}</option>
							@endforeach
						</select>
					</div>
					<table id="table_data" class="table table-bordered col-md-12" style="min-width: 100%; width: auto;">
						<thead style="position: sticky; top: 0">
							<tr>
								<th>No. Pendaftar</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Kota</th>
								<th>Tipe Sekolah</th>
								<th>Akreditasi Sekolah</th>
								<th>Jurusan Asal</th>
								<th>Pekerjaan Ayah</th>
								<th>Pendapatan Ayah</th>
								<th>Pekerjaan Ibu</th>
								<th>Pendapatan Ibu</th>
								<th>Bidik Misi</th>
								<th>Pilihan</th>
								<th>Detail</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<meta name="_token" content="{{ csrf_token() }}" />
	<div class="modal"></div>
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
		<script src="{{ asset('js/jquery-3.2.1.js') }}"></script>

		<script>
			$(document).ready(function(){
				var prodi = $("#select_prodi").val();
				if(prodi != "NONE"){
					$('#table_data').DataTable({
						processing: true,
						serverSide: true,
						ajax: {
							url: 'data_pendaftar/'+ prodi,
							type: 'POST',
							data: {
								_token: $('meta[name="_token"]').attr('content'),
								id: prodi,
							}
						},
						columns: [
							{ data: 'no_pendaftar'},
							{ data: 'nama'},
							{ data: 'jenis_kelamin'},
							{ data: 'kota'},
							{ data: 'tipe_sekolah'},
							{ data: 'akreditasi_sekolah'},
							{ data: 'jurusan_asal'},
							{ data: 'pekerjaan_ayah'},
							{ data: 'pendapatan_ayah'},
							{ data: 'pekerjaan_ibu'},
							{ data: 'pendapatan_ibu'},
							{ data: 'bidik_misi'},
							{ data: 'pilihan_ke'},
							{data: 'action', orderable: false, searchable: false}
						]
					})
				}

				$("#select_prodi").change(function(){
					var prodi = $("#select_prodi").val();
					if(prodi != "NONE"){
						$('#table_data').DataTable({
							destroy: true,
							processing: true,
							serverSide: true,
							ajax: {
								url: 'data_pendaftar/'+ prodi,
								type: 'POST',
								data: {
									_token: $('meta[name="_token"]').attr('content'),
									id: prodi,
								}
							},
							columns: [
								{ data: 'no_pendaftar'},
								{ data: 'nama'},
								{ data: 'jenis_kelamin'},
								{ data: 'kota'},
								{ data: 'tipe_sekolah'},
								{ data: 'akreditasi_sekolah'},
								{ data: 'jurusan_asal'},
								{ data: 'pekerjaan_ayah'},
								{ data: 'pendapatan_ayah'},
								{ data: 'pekerjaan_ibu'},
								{ data: 'pendapatan_ibu'},
								{ data: 'bidik_misi'},
								{ data: 'pilihan_ke'},
								{data: 'action', orderable: false, searchable: false}
							]
						})
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
							Accept: 'application/json',
							dataType: 'json',
							success: function(data){
								console.log(data);
								if (data.fail) {
									var message = '<div class="alert alert-danger">';
									message += '<p>' + data.input + '</p>';
									message += '<p>' + data.message + '</p>';
									message += '</div>';
									$('#message').append(message);
								} else {
									var message = '<div class="alert alert-success alert-dismissable">';
									message += '<p>' + data.input + '</p>';
									message += '<p>' + data.message + '</p>';
									message += '<p>' + data.memory + '</p>';
									message += '</div>';
									$('#message').append(message);
								}
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

				$(document).ajaxStart(function () {
					$("body").addClass("loading");
				});

				$(document).ajaxStop(function(){
				  $("body").removeClass("loading");
				});
			})
		</script>
@endsection
