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
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Tabel Perbandingan
						<button class="btn btn-success btn-sm" id="cekCI" title="Tambah" data-toggle="modal" data-target="#inputKriteria" style="margin-left: 10px;">
							Periksa CI
						</button>
					</h3>
				</div>				
				<div class="box-body">
					<form id="tabelForm" role="form" method="POST" action="">
						<table id="tabelPerbandingan" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="col-md-1">\</th>
									<th>Kriteria-1</th>								
									<th>Kriteria-2</th>								
									<th>Kriteria-3</th>								
									<th>etc</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>Kriteria-1</th>
									<td class="form-group col-md-2"><input class="form-control" readonly type="number" id="1to2" name="1to2" value="1"/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
								</tr>
								<tr>
									<th>Kriteria-2</th>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" readonly type="number" id="1to2" name="1to2" value="1"/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
								</tr>
								<tr>
									<th>Kriteria-3</th>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" readonly type="number" id="1to2" name="1to2" value="1"/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
								</tr>
								<tr>
									<th>etc</th>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" type="number" id="1to2" name="1to2" min="1" max="9" required/></td>
									<td class="form-group col-md-2"><input class="form-control" readonly type="number" id="1to2" name="1to2" value="1"/></td>
								</tr>
							</tbody>
						</table>
						<div class="col-md-10">
						</div>
						<div class="form-group col-md-1">
							<button type="submit" class="btn btn-primary" name="submitNilaiTabel" value="submitNilaiTabel">Submit</button>
						</div>
						<div class="form-group col-md-1">
							<button type="submit" class="btn btn-default" name="submitNilaiTabel" value="submitNilaiTabel">Reset</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection