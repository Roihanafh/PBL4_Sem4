@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-tools d-flex justify-content-end flex-wrap gap-2">
            <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info btn-sm mb-1">
                Import Data
            </button>

            <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary btn-sm mb-1">
                <i class="fa fa-file-excel"></i> Export Data (Excel)
            </a>

            <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning btn-sm mb-1">
                <i class="fa fa-file-pdf"></i> Export Data (PDF)
            </a>

            <button onclick="modalAction('{{ url('/mahasiswa/create_ajax') }}')" class="btn btn-success btn-sm mb-1">
                Tambah Data 
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="mahasiswa-table" style="width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">NIM</th>
                    <th class="text-center">Nama</th>
                    <th class="text-center">Program Studi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
 <div id="modal-crud" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content"></div>
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
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false },
                { data: 'nim', name: 'nim' },
                { data: 'nama', name: 'nama' },
                { data: 'prodi', name: 'prodi' },
                { data: 'aksi', name: 'aksi', className: "text-center", orderable: false, searchable: false }
            ]
        });
    });

    function modalAction(url) {
    $.get(url, function(data) {
        $('#modal-crud .modal-content').html(data);
        $('#modal-crud').modal('show');
    });
}

</script>
@endpush
