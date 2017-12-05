@extends ('admin.layout.master')
@section ('breadcrump')
  <h1>
    dashboard
    <small>Control Panel</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="home"><i class="fa fa-dashboard"></i>Home</a></li>
    <li class="active">Dashboard Admin</li>
  </ol>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Sistem Pendukung Keputusan - Penerimaan Mahasiswa Jalur PMDK-PN
					</h3>
          <div class="box-tools pull-right">
            <button data-original-title="Collapse" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="">
              <i class="fa fa-minus"></i>
            </button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i>
            </button>
          </div>
				</div>
				<div style="display: block" class="box-body">					
				</div>
			</div>
		</div>
	</div>
@endsection
