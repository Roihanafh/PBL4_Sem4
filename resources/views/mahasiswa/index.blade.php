@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="card-tools">
      <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Data</button>
      <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Data</a>
      <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Data</a>
      <button onclick="modalAction('{{ url('/mahasiswa/create_ajax') }}')" class="btn btn-success">Tambah Data</button>
    </div>
  </div>

  <div class="card-body">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Modal -->
    <div
      class="modal fade"
      id="myModal"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>

    <table
      id="mahasiswa-table"
      class="display table table-striped table-hover"
      style="width: 100%"
    >
      <thead>
        <tr>
          <th>No.</th>
          <th>NIM</th>
          <th>Nama</th>
          <th>Program Studi</th>
          <th style="width: 10%;">Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection


@push('js')
<script>
  $(function () {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('#mahasiswa-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('mahasiswa/list') }}",
        type: "POST"
      },
      columns: [
        { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false, width: "5%" },
        { data: 'nim' },
        { data: 'nama' },
        { data: 'prodi' },
        { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "10%" }
      ]
    });
  });

  function modalAction(url = '') {
    $('#myModal .modal-content').load(url, function() {
      $('#myModal').modal('show');
    });
  }
</script>
@endpush
