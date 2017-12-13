@foreach($data_pendaftar as $data)
  <tbody>
    <td>{{ $data->no_pendaftar }}</td>
    <td>{{ $data->nisn }}</td>
    <td>{{ $data->nama }}</td>
    <td>{{ $data->jenis_kelamin }}</td>
    <td>{{ $data->agama }}</td>
    <td>{{ $data->tgl_lahir }}</td>
    <td>{{ $data->kota }}</td>
    <td>{{ $data->tipe_sekolah }}</td>
    <td>{{ $data->jenis_sekolah }}</td>
    <td>{{ $data->akreditasi_sekolah }}</td>
    <td>{{ $data->jurusan_asal }}</td>
    <td>{{ $data->pilihan_ke }}</td>
    <td>
      <a class="btn btn-primary btn-flat btn-sm" href="{{{ URL::to('data_pendaftar/'.$data->pilihan_ke.'/details') }}}">
      <i class="fa fa-list"> Detail </i>
    </td>
  </tbody>
@endforeach

{{ $data_pendaftar->links() }}
