@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
    <button onclick="modalAction('{{ url('/user/import') }}')" class="btn btn-info">
        Import user
    </button>
    <a href="{{ url('/user/export_excel') }}" class="btn btn-primary">
        <i class="fa fa-file-excel"></i> Export user
    </a>
    <a href="{{ url('/user/export_pdf') }}" class="btn btn-warning">
        <i class="fa fa-file-pdf"></i> Export user
    </a>
    <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/periode/create_ajax') }}')">
        <i class="fa fa-plus"></i> Add Row
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
    function modalAction(url = ''){
        $('#myModal .modal-content').load(url,function(){
            $('#myModal').modal('show');
        });
    }

</script>
@endpush