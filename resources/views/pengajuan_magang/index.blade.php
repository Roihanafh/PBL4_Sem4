@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
      {{-- <button onclick="modalAction('{{ url('/pengajuanMagang/import') }}')" class="btn btn-info">
          Import Pengajuan Magang
      </button> --}}
      <a href="{{ url('/pengajuanMagang/export_excel') }}" class="btn btn-primary">
          <i class="fa fa-file-excel"></i> Export Pengajuan Magang
      </a>
      <a href="{{ url('/pengajuanMagang/export_pdf') }}" class="btn btn-warning">
          <i class="fa fa-file-pdf"></i> Export Pengajuan Magang
      </a>
      <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/pengajuanMagang/create_ajax') }}')">
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
  </div>
  <div class="table-responsive">
    <div class="card-body">
      <table
        id="pengajuan-magang-table"
        class="display table table-striped table-hover"
        style="width: 100%"
      >
        <thead class="thead-dark">
          <tr>
            <th>No. </th>
            <th>Nama Mahasiswa</th>
            <th>NIM</th>
            <th>Dosen Pembimbing</th>
            <th>Tanggal Lamaran</th>
            <th>Status</th>
            <th style="width: 10%">Action</th>
          </tr>
        </thead>
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

        
    });
    $(document).ready(function() {
        tablePengajuanMagang = $('#pengajuan-magang-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('pengajuan-magang/list') }}",
                type: "POST"
            },
            columns: [
                { data: 'DT_RowIndex',  className: "text-center", orderable: false, searchable: false, width: "5%" },
                { data: 'mahasiswa_nama' },
                { data: 'mhs_nim' },
                {
                    data: 'dosen_nama',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (data === '-') {
                            if (row.status === 'pending') {
                                return '<span class="badge badge-warning text-center"><i class="fa fa-clock"></i> Pending</span>';
                            }else if(row.status === 'ditolak'){
                                return '<span class="badge badge-danger"><i class="fa fa-times"></i> Ditolak</span>';   
                            }
                        }
                        return '<span>' + data + '</span>';
                    }
                },
                { data: 'tanggal_lamaran' },
                { data: 'status',
                  className: "text-center",
                  render: function(data, type, row) {
                      if (data === 'diterima') {
                          return '<span class="badge badge-success"><i class="fa fa-check"></i> Diterima</span>';
                      } else if (data === 'ditolak') {
                          return '<span class="badge badge-danger"><i class="fa fa-times"></i> Ditolak</span>';
                      } else if (data === 'pending') {
                          return '<span class="badge badge-warning"><i class="fa fa-clock"></i> pending</span>';
                      } else {
                          return '<span class="badge badge-secondary">' + data + '</span>';
                      }
                  } 
                },
                { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "20%" }
            ],
            columnDefs: [
            {
                targets: [1, 3], // Kolom mahasiswa_nama dan dosen_nama
                render: function(data, type, row) {
                    return data.length > 30 ? '<span title="' + data + '">' + data.substring(0, 30) + '...' + '</span>' : data;
                }
            }
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