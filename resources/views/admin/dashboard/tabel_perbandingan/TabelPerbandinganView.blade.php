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
		<li>Dashboard Admin</li>
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
					<p>{{ Session::get('successMessage') }}</p>
				</div>
			@endif
			<div id="message">
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Tabel Perbandingan
						<button class="btn btn-success btn-sm" id="cekCI" title="cekCi" style="margin-left: 10px;">
							Periksa CI
						</button>
					</h3>
				</div>
				<div class="box-body">
					<form id="tabelForm" role="form" method="POST" action="{{ url('/kelola_tabel/tambah') }}">
						{{ csrf_field() }}
						<table id="tabelPerbandingan" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="col-md-1">Kriteria</th>
									@if(count($kriteria) > 0)
										@foreach($kriteria as $item)
											<th>{{ $item->nama_kriteria }}</th>
										@endforeach
									@else
										<th></th>
									@endif
								</tr>
							</thead>
							<tbody>
								<?php
									if (count($kriteria) > 0) {
										$iterate = 0;
										foreach ($kriteria as $item) {
											//echo " => ".$kriteria[1]."<br>";
											echo "<tr>
											<th>".$item->nama_kriteria."</th>";
											for ($i=0; $i < count($kriteria); $i++) {
												if ($i == $iterate) {
													echo '<td class="form-group col-md-2"><input class="form-control nilai" type="number" readonly
													id="'.$item->id_kriteria.$kriteria[$i % count($kriteria)]->id_kriteria.'"
													data-kriteria1="'.$item->id_kriteria.'" data-kriteria2="'.$kriteria[$i % count($kriteria)]->id_kriteria.'"
													step="any" />';
													echo "</td>";
												} elseif ($i <= $iterate) {
													echo '<td class="form-group col-md-2"><input class="form-control nilai" type="number"
													id="'.$item->id_kriteria.$kriteria[$i % count($kriteria)]->id_kriteria.'"
													readonly placeholder="field ini akan terisi otomatis"
													step="any" />';
													echo '</td>';
												} else {
													echo '<td class="form-group col-md-2"><input class="form-control nilai" type="number"
														id="'.$item->id_kriteria.$kriteria[$i % count($kriteria)]->id_kriteria.'"
														name="nilai[]" placeholder="isi field dengan nilai 0 - 9" min="0" max="9" required
														step="any" />';
													echo '<input hidden name="kriteria1[]" value="'.$item->id_kriteria.'"/>';
													echo '<input hidden name="kriteria2[]" value="'.$kriteria[$i % count($kriteria)]->id_kriteria.'"/>';
													echo '</td>';
												}
											}
											echo "</tr>";
											$iterate++;
										}
									} else {
										echo "<td></td>";
										echo "<td></td>";
									}
								?>
							</tbody>
						</table>
						<div class="col-md-12">
							* Field diatas adalah nilai perbandingan antara kriteria pada baris dibandingkan dengan kriteria pada kolom
						</div>
						<div class="col-md-10">
						</div>
						<div class="form-group col-md-1">
							<button type="submit" class="btn btn-primary" name="submitNilaiTabel" value="submitNilaiTabel">Submit</button>
						</div>
						<div class="form-group col-md-1">
							<button type="reset" class="btn btn-default" name="resetNilaiTabel" value="resetNilaiTabel">Reset</button>
						</div>
					</form>
					<div class="form-group">
						<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#petunjuk" aria-expanded="false" aria-controls="petunjuk">
							Petunjuk Pengisian Nilai
						</button>
					</div>
					<div class="collapse form-group" id="petunjuk">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Derajat Kepentingan</th>
									<th>Definisi</th>
									<th>Keterangan</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td>Sama Pentingnya</td>
									<td>Kedua kriteria yang sama-sama penting</td>
								</tr>
								<tr>
									<td>2</td>
									<td>Kepentingan antara 1 dan 3</td>
									<td></td>
								</tr>
								<tr>
									<td>3</td>
									<td>Sedikit Lebih Penting</td>
									<td>Pengalaman dan pendapat sedikit lebih
										mementingkan suatu kriteria dibanding pasangannya</td>
								</tr>
								<tr>
									<td>4</td>
									<td>Kepentingan antara 3 dan 5</td>
									<td></td>
								</tr>
								<tr>
									<td>5</td>
									<td>Lebih Penting</td>
									<td>Pengalaman dan pendapat lebih
										mementingkan suatu kriteria dibanding pasangannya</td>
								</tr>
								<tr>
									<td>6</td>
									<td>Kepentingan antara 5 dan 7</td>
									<td></td>
								</tr>
								<tr>
									<td>7</td>
									<td>Sangat Penting</td>
									<td>Pengalaman dan pendapat sangat
										mementingkan suatu kriteria dibanding pasangannya;
										terlihat jelas kepentingannya dalam keadaan nyata</td>
								</tr>
								<tr>
									<td>8</td>
									<td>Kepentingan antara 7 dan 9</td>
									<td></td>
								</tr>
								<tr>
									<td>9</td>
									<td>Mutlak Lebih Penting</td>
									<td>Suatu kriteria mutlak lebih penting
										daripada pasangannya, pada keyakinan tertinggi</td>
								</tr>
								<tr>
									<td>Kebalikan nilai diatas</td>
									<td>Jika kriteria <i>i</i> memiliki
										salah satu angka kepentingan diatas
										bila dibandingkan dengan kriteria <i>j</i>,
										maka kriteria <i>j</i> memiliki nilai kebalikan
										ketika dibandingkan dengan kriteria <i>i</i></td>
									<td>Contoh:
									jika kriteria <i>a</i> dibanding kriteria <i>b</i> = 4
									maka kriteria <i>b</i> dibanding kriteria <i>a</i> = 1/4
									</td>
								</tr>
								<tr>
									<td>1.1 - 1.9</td>
									<td>Jika kepentingan dari pasangan
										kriteria sangat berdekatan</td>
									<td>Jika terdapat kesulitan memberikan nilai
										kepentingan tetapi jika dibandingkan dengan
										kriteria pasangannya, nilai kecil tersebut
										tidak terlalu berpengaruh, namun nilai tersebut
										masih bisa menunjukkan kepentingan dari kriteria tersebut</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<meta name="_token" content="{{ csrf_token() }}" />
	<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
	<script src="{{ asset('js/jquery-3.2.1.js') }}"></script>
	<script src="{{ asset('js/jquery-3.2.1.slim.js') }}"></script>
	<script>
		//to load nilai banding value based on id_kriteria
		$(document).ready(function(){
			var kriteria1 = $("input.nilai").map(function(){
				return $(this).data("kriteria1");
			}).get();
			var kriteria2 = $("input.nilai").map(function(){
				return $(this).data("kriteria2");
			}).get();

			$.each(kriteria1, function(n, i){
				$.each(kriteria2, function(j, e){
					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
						}
					});

					$.ajax({
						url: "kelola_tabel/get_nilai/" + i + "_" + e,
						type: "GET",
						cache: false,
						dataType: 'json',
						success: function(data){
							console.log(data);
							$("#"+i+e).val(data.nilai);
						},
						error: function(data, ajaxOptions, thrownError){
							//alert(data.status);
						}
					});
				});
			});

			$("#cekCI").click(function(){
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				});

				$.ajax({
					url: "kelola_tabel/cek_ci",
					type: "GET",
					cache: false,
					dataType: 'json',
					success: function(data){
						console.log(data);
						if (data.fail) {
							var message = '<div class="alert alert-danger">';
							message += '<p>' + data.input + '</p>';
							message += '<p>' + data.message + '</p>';
							message += "<p><a href='" + data.AHPurl + "'> Download Perhitungan Bobot </a></p>";
							message += '</div>';
							$('#message').append(message);
						} else {
							var message = '<div class="alert alert-success alert-dismissable">';
							message += '<p>' + data.input + '</p>';
							message += '<p>' + data.message + '</p>';
							message += "<p><a href='" + data.AHPurl + "'> Download Perhitungan Bobot </a></p>";
							//$('#message').append("<a href='" + data.AHPurl + "'> Download Perhitungan Bobot </a>");
							message += '</div>';
							$('#message').append(message);
							//$('body').append("<iframe src='" + data.AHPurl + "'></iframe>");
						}
					},
					error: function(data, ajaxOptions, thrownError){
						console.log(data);
						var message = '<div class="alert alert-danger">';
						message += '<p>' + data.status + '</p>';
						message += '<p>' + data.Error + '</p>';
						message += '</div>';
						$('#message').append(message);
					}
				});
			})

			$(document).ajaxStart(function () {
				$("body").addClass("loading");
			});

			$(document).ajaxStop(function(){
				$("body").removeClass("loading");
			});

		})
	</script>
@endsection
