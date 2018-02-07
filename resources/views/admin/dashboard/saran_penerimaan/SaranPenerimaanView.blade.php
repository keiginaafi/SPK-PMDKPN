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
		Dashboard
		<small>Control Panel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
		<li class="active">Saran Penerimaan</li>
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
					<h3 class="box-title">Saran Penerimaan
						<button class="btn btn-primary btn-flat btn-sm" id="mooraMethod" title="Saran Penerimaan" style="margin-left: 10px;">
							<i class="fa"> Hasilkan Saran Penerimaan </i>
						</button>
						<a class="btn btn-primary btn-flat btn-sm" id="cetak_hasil" title="cetak" href="{{{ action('Moora\SaranPenerimaanController@cetakDataPenerimaan') }}}"
						data-confirm="Cetak data penerimaan?" style="position: relative; left: 134%; right: 10%;">
							<i class="fa fa-print" aria-hidden="true"> Cetak Saran Penerimaan </i>
						</a>
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
					<table id="table_data" class="table table-bordered table-hover col-md-12" style="min-width: 100%; width: auto;">
						<thead>
							<tr>
								<th>No. Pendaftar</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Tipe Sekolah</th>
								<th>Jurusan Asal</th>
								<th>Pekerjaan Ayah</th>
								<th>Pendapatan Ayah</th>
								<th>Pekerjaan Ibu</th>
								<th>Pendapatan Ibu</th>
								<th>Jumlah Tanggungan</th>
								<th>Bidik Misi</th>
								<th>Nilai Akhir</th>
								<th>Rank</th>
							</tr>
						</thead>
						<tbody id="data_mhs" name="data_mhs">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div id="kuota">
	</div>
	<meta name="_token" content="{{ csrf_token() }}" />
	<div class="modal"></div>
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
		<script src="{{ asset('js/jquery-3.2.1.js') }}"></script>
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
						url: "saran_penerimaan/" + prodi,
						type:"POST",
						cache: false,
						dataType: 'json',
						data:{
							id: prodi,
						},
						success: function(data){
							console.log(data);
							$("#data_mhs tr").last().remove();
							var success = data.sma;
							var smk = data.smk;
							var cadangan = data.cadangan;
							var counter = 1;
							$.each(data.saran, function(i, d){
								if (counter <= success) {
									var view = '<tr class="bg-success">';
								} else if ((counter > success) && (counter <= (success + smk))) {
									var view = '<tr class="bg-primary">';
								} else if ((counter > (success + smk)) && (counter <= (success + smk + cadangan))) {
									var view = '<tr class="bg-warning">';
								} else {
									var view = '<tr class="bg-danger">';
								}
								$.each(d, function(j, e){
									view += '<td>' + e + '</td>';
								});
								view += '</tr>';
								$("#data_mhs").append(view);
								counter += 1;
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
							url: "saran_penerimaan/" + prodi,
							type:"POST",
							cache: false,
							dataType: 'json',
							data:{
								id: prodi,
							},
							success: function(data){
								console.log(data);
								$("#data_mhs tr").remove();
								var success = data.sma;
								var smk = data.smk;
								var cadangan = data.cadangan;
								var counter = 1;
								$.each(data.saran, function(i, d){
									if (counter <= success) {
										var view = '<tr class="bg-success">';
									} else if ((counter > success) && (counter <= (success + smk))) {
										var view = '<tr class="bg-primary">';
									} else if ((counter > (success + smk)) && (counter <= (success + smk + cadangan))) {
										var view = '<tr class="bg-warning">';
									} else {
										var view = '<tr class="bg-danger">';
									}
									$.each(d, function(j, e){
										view += '<td>' + e + '</td>';
									});
									view += '</tr>';
									$("#data_mhs").append(view);
									counter += 1;
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

				$("#mooraMethod").click(function(){
					var confirm = window.confirm("Mulai Hasilkan Saran Penerimaan?");
					if (confirm == true) {
						$.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
							}
						});

						$.ajax({
							url: "saran_penerimaan/hasilkan_saran",
							type:"GET",
							cache: false,
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
									message += "<p><a href='" + data.AHPurl + "'> Download Perhitungan Bobot </a></p>";
									message += "<p><a href='" + data.mooraUrl + "'> Download Perhitungan Moora </a></p>";
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

				$('#cetak_hasil').click(function(event){

					var confirm = window.confirm($('#cetak_hasil').attr('data-confirm'));
					if(confirm == true){
						window.location.href = $('#cetak_hasil').attr('href');
						/*$.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
							}
						});

						$.ajax({
							url: "saran_penerimaan/cetak_saran",
							type:"GET",
							cache: false,
							success: function(data){
								console.log(data);
								//window.location.href = data;
								$("body").appendChild("<iframe src='" + data + "' style='display: none;'></iframe>")
							},
							error: function(data){
								alert('Gagal mencetak data.');
							}
						});*/
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
