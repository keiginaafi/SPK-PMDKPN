<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="@if(url('/') == request()->url()) active @else '' @endif treeview">
          <a href="{{route('admin')}}">
            <i class="fa fa-dashboard"></i> <span>Halaman Utama</span>
          </a>
        </li>
        <li class="@if(url('/data_pendaftar') == request()->url() or url('/input_data') == request()->url() ) active @else '' @endif treeview">
          <a href="#">
            <i class="fa fa-th"></i>
            <span>Data Pendaftar</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="input_data"><i class="fa @if(url('/input_data') == request()->url()) fa-dot-circle-o @else fa-circle-o @endif"></i> Input Data </a></li>
            <li><a href="data_pendaftar"><i class="fa @if(url('/data_pendaftar') == request()->url()) fa-dot-circle-o @else fa-circle-o @endif"></i> Olah Data </a></li>
          </ul>
        </li>
		    <li class="@if(url('/kelola_prodi') == request()->url()) active @else '' @endif treeview">
          <a href="kelola_prodi">
            <i class="fa fa-th"></i> <span>Program Studi</span>
          </a>
        </li>
        <li class="@if(url('/kelola_kriteria') == request()->url() or url('/kelola_tabel') == request()->url() ) active @else '' @endif treeview">
          <a href="#">
            <i class="fa fa-th"></i> <span>Kriteria</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
		      <ul class="treeview-menu">
            <li><a href="kelola_kriteria"><i class="fa @if(url('/kelola_kriteria') == request()->url()) fa-dot-circle-o @else fa-circle-o @endif"></i> Kelola Kriteria </a></li>
            <li><a href="kelola_tabel"><i class="fa @if(url('/kelola_tabel') == request()->url()) fa-dot-circle-o @else fa-circle-o @endif"></i> Kelola Bobot </a></li>
          </ul>
        </li>
        <li class="@if(url('/saran_penerimaan') == request()->url()) active @else '' @endif treeview">
          <a href="saran_penerimaan">
            <i class="fa fa-th"></i>
            <span>Saran Penerimaan</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
</aside>
