@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header d-flex">
    <h3 class="card-title">Manajemen Lowongan Magang</h3>
    <button class="btn btn-primary ms-auto" onclick="modalAction('{{ url('/lowongan/create_ajax') }}')">
      <i class="fa fa-plus"></i> Tambah
    </button>
  </div>
  <div class="card-body">
    <table id="lowongan-table" class="table table-striped">
      <thead>
        <tr>
          <th>No.</th><th>Judul</th><th>Perusahaan</th><th>Lokasi</th>
          <th>Mulai</th><th>Deadline</th><th>Periode</th><th>Aksi</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg"><div class="modal-content"></div></div>
</div>
@endsection

@push('js')
<script>
$(function(){
  // **Tambah CSRF token header agar POST ke /lowongan/list tidak 419**
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('#lowongan-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ url('/lowongan/list') }}",
      type: "POST"
    },
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'judul',               name: 'judul' },
      { data: 'perusahaan',          name: 'perusahaan' },
      { data: 'lokasi',              name: 'lokasi' },
      { data: 'tanggal_mulai_magang',name: 'tanggal_mulai_magang' },
      { data: 'deadline_lowongan',   name: 'deadline_lowongan' },
      { data: 'periode',             name: 'periode' },
      { data: 'aksi',                orderable: false, searchable: false }
    ]
  });
});

function modalAction(url = '') {
  $('#myModal .modal-content').load(url, function(){
    $('#myModal').modal('show');
  });
}
</script>
@endpush
