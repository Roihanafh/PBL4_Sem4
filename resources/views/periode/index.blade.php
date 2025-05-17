@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <button onclick="modalAction('{{ url('/periode/import_ajax') }}')" class="btn btn-info">
          Import Periode
      </button>
      <a href="{{ url('/periode/export_excel') }}" class="btn btn-primary">
          <i class="fa fa-file-excel"></i> Export Periode
      </a>
      <a href="{{ url('/periode/export_pdf') }}" class="btn btn-warning">
          <i class="fa fa-file-pdf"></i> Export Periode
      </a>
      <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/periode/create_ajax') }}')">
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
      {{-- isi modal --}}
    </div>
    <div class="card-body">
      <table
        id="periode-table"
        class="display table table-striped table-hover"
        style="width: 100%"
      >
        <thead>
          <tr>
            <th>No. </th>
            <th>Periode</th>
            <th>Keterangan</th>
            <th style="width: 10%">Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>No. </th>
            <th>Periode</th>
            <th>Keterangan</th>
            <th>Action</th>
          </tr>
        </tfoot>
      </table>
      <div class="table-responsive">
    </div>
  </div>
</div>
<!-- Hidden import form template for Periode -->
<div id="importFormTemplatePeriode" style="display:none;">
  <form action="{{ url('/periode/import_ajax') }}" method="POST" id="form-import-periode" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Data Periode</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Download Template</label>
          <a href="{{ asset('template_periode.xlsx') }}" class="btn btn-info btn-sm" download>
            <i class="fa fa-file-excel"></i> Download
          </a>
          <small id="error-user_id" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
          <label>Pilih File</label>
          <input type="file" name="file_periode" id="file_periode" class="form-control" required>
          <small id="error-file_periode" class="error-text form-text text-danger"></small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Batal">Batal</button>
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </div>
  </form>
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

        
    });
    $(document).ready(function() {
        tableLevel = $('#periode-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('periode/list') }}",
                type: "POST"
            },
            columns: [
                { data: 'DT_RowIndex',  className: "text-center", orderable: false, searchable: false, width: "5%" },
                { data: 'periode' },
                { data: 'keterangan' },
                { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "20%" }
            ]
        });
    });
    function modalAction(url = '') {
    if (url.includes('/periode/import_ajax')) {
      $('#myModal .modal-content').html($('#importFormTemplatePeriode').html());
      $('#myModal').modal('show');
      initImportFormValidationPeriode();
    } else {
      $('#myModal .modal-content').load(url, function () {
        $('#myModal').modal('show');
      });
    }
  }
  function initImportFormValidationPeriode() {
  $("#form-import-periode").validate({
    rules: {
      file_periode: { required: true, extension: "xlsx" },
    },
    submitHandler: function (form) {
      var formData = new FormData(form);
      $.ajax({
        url: form.action,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.status) {
            $('#myModal').modal('hide');
            Swal.fire({
              icon: 'success',
              title: 'Import Berhasil',
              text: response.message,
            }).then(() => {
              $('#periode-table').DataTable().ajax.reload();
            });
          } else {
            $('.error-text').text('');
            if (response.msgField) {
              $.each(response.msgField, function (prefix, val) {
                $('#error-' + prefix).text(val[0]);
              });
            }
            Swal.fire({
              icon: 'error',
              title: 'Terjadi Kesalahan',
              text: response.message
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan pada server.'
          });
        }
      });
      return false;
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element) {
      $(element).removeClass('is-invalid');
    }
  });
}

</script>
@endpush