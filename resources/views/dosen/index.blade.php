@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
    <button onclick="modalAction('{{ url('/dosen/import_ajax') }}')" class="btn btn-info">
        Import Dosen
    </button>
    <a href="{{ url('/dosen/export_excel') }}" class="btn btn-primary">
        <i class="fa fa-file-excel"></i> Export Dosen
    </a>
    <a href="{{ url('/dosen/export_pdf') }}" class="btn btn-warning">
        <i class="fa fa-file-pdf"></i> Export Dosen
    </a>
    <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/dosen/create_ajax') }}')">
        <i class="fa fa-plus"></i> Tambah Data
    </button>
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
      id="dosen-table"
      class="display table table-striped table-hover"
      style="width: 100%"
    >
      <thead>
        <tr>
          <th class="text-center" style="width:5%">No.</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Telepon</th>
          <th class="text-center" style="width: 15%">Aksi</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Hidden import form template for Dosen -->
<div id="importFormTemplateDosen" style="display:none;">
  <form action="{{ url('/dosen/import_ajax') }}" method="POST" id="form-import-dosen" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Data Dosen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Download Template</label>
          <a href="{{ asset('template_dosen.xlsx') }}" class="btn btn-info btn-sm" download>
            <i class="fa fa-file-excel"></i> Download
          </a>
          <small id="error-user_id" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
          <label>Pilih File</label>
          <input type="file" name="file_admin" id="file_admin_dosen" class="form-control" required>
          <small id="error-file_admin" class="error-text form-text text-danger"></small>
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

    $('#dosen-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('/dosen/list') }}",
        type: "POST"
      },
      columns: [
        { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false, width: "5%" },
        { data: 'nama' },
        { data: 'email' },
        { data: 'telp' },
        { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "15%" }
      ]
    });
  });

  function modalAction(url = '') {
    if (url.includes('/admin/import_ajax')) {
      $('#myModal .modal-content').html($('#importFormTemplate').html());
      $('#myModal').modal('show');
      initImportFormValidationAdmin();
    } else if (url.includes('/dosen/import_ajax')) {
      $('#myModal .modal-content').html($('#importFormTemplateDosen').html());
      $('#myModal').modal('show');
      initImportFormValidationDosen();
    } else {
      $('#myModal .modal-content').load(url, function () {
        $('#myModal').modal('show');
      });
    }
  }

  function initImportFormValidationDosen() {
    $("#form-import-dosen").validate({
      rules: {
        file_admin: { required: true, extension: "xlsx" },
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
                $('#dosen-table').DataTable().ajax.reload();
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
