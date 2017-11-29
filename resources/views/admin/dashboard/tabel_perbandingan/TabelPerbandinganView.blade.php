@extends('admin.layout.master')
@section('breadcrump')
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
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Tabel Perbandingan
						<button class="btn btn-success btn-sm" id="cekCI" title="Tambah" data-toggle="modal" data-target="#inputKriteria" style="margin-left: 10px;">
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
									<th class="col-md-1">\</th>
									@foreach($kriteria as $item)
										<th>{{ $item->nama_kriteria }}</th>
									@endforeach
								</tr>
							</thead>
							<tbody>
								<?php
									$iterate = 0;
									foreach ($kriteria as $item) {
										//echo " => ".$kriteria[1]."<br>";
										echo "<tr>
										<th>".$item->nama_kriteria."</th>";
										for ($i=0; $i < count($kriteria); $i++) {
											if ($i == $iterate) {
												echo '<td class="form-group col-md-2"><input class="form-control nilai" type="number" readonly
												id="'.$item->id_kriteria.$kriteria[$i % count($kriteria)]->id_kriteria.'"
												data-kriteria1="'.$item->id_kriteria.'" data-kriteria2="'.$kriteria[$i % count($kriteria)]->id_kriteria.'" />';
												echo "</td>";
											}elseif ($i <= $iterate) {
												echo '<td class="form-group col-md-2"><input class="form-control nilai" type="number"
												id="'.$item->id_kriteria.$kriteria[$i % count($kriteria)]->id_kriteria.'"
												readonly placeholder="field ini akan terisi otomatis" />';
												echo '</td>';
											}else {
												echo '<td class="form-group col-md-2"><input class="form-control nilai" type="number"
													id="'.$item->id_kriteria.$kriteria[$i % count($kriteria)]->id_kriteria.'"
													name="nilai[]" placeholder="isi field dengan nilai 0 - 9" min="0" max="9" required />';
												echo '<input hidden name="kriteria1[]" value="'.$item->id_kriteria.'"/>';
												echo '<input hidden name="kriteria2[]" value="'.$kriteria[$i % count($kriteria)]->id_kriteria.'"/>';
												echo '</td>';
											}
										}
										echo "</tr>";
										$iterate++;
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
				</div>
				<div id="asdf">
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
					/*$("#asdf").append(n+" => "+i+" ");
					$("#asdf").append(j+" => "+e+"<br>");*/
					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
						}
					});

					$.ajax({
						url: "kelola_tabel/get_nilai/" + i + e,
						type: "GET",
						cache: false,
						dataType: 'json',
						data:{
							id1: kriteria1,
							id2: kriteria2,
						},
						success: function(data){
							console.log(data);
							$("#"+i+e).val(data.nilai);
						},
						error: function(data, ajaxOptions, thrownError){
							alert(data.status);
						}
					});
				});
			});

		})
	</script>
@endsection
