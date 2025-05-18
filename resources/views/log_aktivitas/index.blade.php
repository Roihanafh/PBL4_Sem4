@extends('layouts.template')

@section('content')
<div class="card">
  

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label for="prodi_id" class="form-label">Filter Prodi:</label>
        <select id="prodi_id" name="prodi_id" class="form-control">
          <option value="">- Semua Prodi -</option>
          @foreach($prodis as $prodi)
            <option value="{{ $prodi->prodi_id }}">{{ $prodi->nama_prodi }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <table id="log-table" class="display table table-striped" style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Mahasiswa</th>
          <th>Prodi</th>
          <th>Keterangan</th>
          <th>Waktu</th>
          <th>Aksi</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection

@push('js')
<script>
  $(function () {
    // Setup CSRF token untuk semua AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Inisialisasi DataTables
    var table = $('#log-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('/log-aktivitas/list') }}", // Pastikan route ini menerima POST
        type: "POST", // WAJIB POST, karena route-nya hanya mendukung POST
        data: function (d) {
          d.prodi_id = $('#prodi_id').val(); // Filter berdasarkan prodi_id
        }
      },
      columns: [
        { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
        { data: 'nama' },
        { data: 'prodi' },
        { data: 'keterangan' },
        { data: 'waktu' },
        { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
      ],
    });

    // Reload table saat filter prodi berubah
    $('#prodi_id').on('change', function () {
      table.ajax.reload();
    });
  });

  // Fungsi untuk membuka modal dan load konten dari URL
  function modalAction(url = '') {
    $('#myModal .modal-content').html('<div class="text-center p-3">Memuat...</div>'); // Placeholder saat loading
    $('#myModal .modal-content').load(url, function () {
      $('#myModal').modal('show');
    });
  }
</script>
@endpush
