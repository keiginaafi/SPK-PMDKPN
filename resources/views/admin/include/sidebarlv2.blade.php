<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="@if(url('/data_pendaftar') == request()->url()) active @else '' @endif treeview">
          <a href="{{{ URL::to('data_pendaftar') }}}">
            <i class="fa fa-th"></i>
            <span>Lihat Data Pendaftar</span>
          </a>
        </li>
        <li class="@if(url('/saran_penerimaan') == request()->url()) active @else '' @endif treeview">
          <a href="{{{ URL::to('saran_penerimaan') }}}">
            <i class="fa fa-th"></i>
            <span>Saran Penerimaan</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
</aside>
