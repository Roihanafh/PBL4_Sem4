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
      <th>No.</th>
      <th>Judul</th>
      <th>Deskripsi</th>
      <th>Perusahaan</th>
      <th>Lokasi</th>
      <th>Mulai</th>
      <th>Deadline</th>
      <th>Periode</th>
      <th>Status</th>
      <th>Kuota</th>
      <th>Durasi</th>
      <th>Tipe Bekerja</th>
      <th>Aksi</th>
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
      { data: 'judul', name: 'judul' },
      { data: 'deskripsi', name: 'deskripsi' },
      { data: 'perusahaan', name: 'perusahaan', orderable: false, searchable: false },
      { data: 'lokasi', name: 'lokasi' },
      {
        data: 'tanggal_mulai_magang',
        name: 'tanggal_mulai_magang',
        render: function(data, type, row) {
          if (!data) return '';
          const datePart = data.split('T')[0];
          const parts = datePart.split('-'); // [YYYY,MM,DD]
          const formatted = parts[2] + '-' + parts[1] + '-' + parts[0];
          return (type === 'display') ? formatted : datePart;
        }
      },
      {
        data: 'deadline_lowongan',
        name: 'deadline_lowongan',
        render: function(data, type, row) {
          if (!data) return '';
          const datePart = data.split('T')[0];
          const parts = datePart.split('-');
          const formatted = parts[2] + '-' + parts[1] + '-' + parts[0];
          return (type === 'display') ? formatted : datePart;
        }
      },
      { data: 'periode', name: 'periode', orderable: false, searchable: false },
      { data: 'status', name: 'status' },
      { data: 'kuota', name: 'kuota' },
      { data: 'durasi', name: 'durasi' },
      { data: 'tipe_bekerja', name: 'tipe_bekerja' },
      { data: 'aksi', orderable: false, searchable: false }
    ]
  });
});

function modalAction(url = '') {
  $('#myModal .modal-content').load(url, function(){
    $('#myModal').modal('show');
  });
}

// base URL Lowongan
const lowonganBase = "{{ url('lowongan') }}";

function deleteLowongan(id) {
  if (!id) return;

  $.ajax({
    url: `${lowonganBase}/${id}/delete_ajax`,  // => correct full path
    type: 'DELETE',
    success(res) {
      if (res.status) {
        $('#myModal').modal('hide');
        $('#lowongan-table').DataTable().ajax.reload();
      } else {
        alert(res.message || 'Gagal menghapus data.');
      }
    },
    error(xhr) {
      console.error('DELETE error:', xhr);
      alert('Terjadi kesalahan pada server.');
    }
  });
}
</script>
@endpush
