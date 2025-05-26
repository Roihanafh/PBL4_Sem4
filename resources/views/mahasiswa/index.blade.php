@extends('layouts.template_mhs')

@section('content')
<div class="card">
  <div class="card-header">
      <div class="d-flex gap-2 align-items-center flex-wrap">
      <button onclick="modalAction('{{ url('/mahasiswa/import') }}')" class="btn btn-info">
          Import Mahasiswa
      </button>
      <a href="{{ url('/mahasiswa/export_excel') }}" class="btn btn-primary">
          <i class="fa fa-file-excel"></i> Export Mahasiswa
      </a>
      <a href="{{ url('/mahasiswa/export_pdf') }}" class="btn btn-warning">
          <i class="fa fa-file-pdf"></i> Export Mahasiswa
      </a>
      <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/mahasiswa/create_ajax') }}')">
          <i class="fa fa-plus"></i> Tambah Data
      </button>
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

    <div class="card-body">
       <div class="row mb-3">
          <div class="col-md-3">
              <label for="prodi_id" class="form-label">Filter:</label>
              <select id="prodi_id" name="prodi_id" class="form-control">
                  <option value="">- Semua Prodi -</option>
                  @foreach($prodis as $prodi)
                      <option value="{{ $prodi->prodi_id }}">{{ $prodi->nama_prodi }}</option>
                  @endforeach
              </select>
          </div>
      </div>
      <table
        id="mahasiswa-table"
        class="display table table-striped table-hover"
        style="width: 100%"
      >
        <thead class="thead-dark">
          <tr>
            <th>No.</th>
            <th>NIM</th>
            <th>Nama Lengkap</th>
            <th>Prodi</th>
            <th style="width: 10%">Action</th>
          </tr>
        </thead>
        <tfoot>
        </tfoot>
      </table>
    </div>
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

      var tableMahasiswa = $('#mahasiswa-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ url('mahasiswa/list') }}",
              type: "POST",
              data: function (d) {
                  d.prodi_id = $('#prodi_id').val(); // Tambahkan nilai filter prodi
              }
          },
          columns: [
              { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false, width: "5%" },
              { data: 'nim' },
              { data: 'nama' },
              { data: 'prodi' },
              { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "10%" }
          ]
      });

      $('#prodi_id').on('change', function () {
          tableMahasiswa.ajax.reload(); // reload data ketika filter prodi berubah
      });
  });
   function modalAction(url = ''){
        $('#myModal .modal-content').load(url,function(){
            $('#myModal').modal('show');
        });
    }

</script>
@endpush
