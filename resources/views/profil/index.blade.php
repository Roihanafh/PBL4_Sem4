@extends('layouts.template_mhs')

@section('content')

<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
    
    </div>
  </div>

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
    {{-- MINAT --}}
    @if($data)
        <div class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="card-title mb-0">MINAT</p>
                        <button onclick="modalAction('{{ route('profil.minat.index') }}')" class="btn btn-outline-secondary btn-sm">
                            <i class="ti-pencil-alt"></i>
                        </button>
                    </div>
                    <hr>
                    <div class="template-demo">
                        @if($data->minat->isEmpty())
    <p class="text-muted">Belum ada minat yang dipilih.</p>
@else
    @foreach($data->minat as $minat)
        <div class="alert alert-fill-primary d-inline-block mb-2 mr-2">
            {{ $minat->nama ?? '-' }}
        </div>
    @endforeach
@endif

                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PREFERENSI LOKASI --}}
    @if($data)
        <div class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="card-title mb-0">PREFRENSI LOKASI</p>
                        <button onclick="modalAction('{{ route('profil.prefrensi-lokasi.index') }}')" class="btn btn-outline-secondary btn-sm">
                            <i class="ti-pencil-alt"></i>
                        </button>
                    </div>
                    <hr>
                    <div class="template-demo">
                        @if($data->prefrensiLokasi->isEmpty())
                            <p class="text-muted">Belum ada prefrensi lokasi yang ditambahkan.</p>
                        @else
                            @foreach($data->prefrensiLokasi as $lokasi)
                                <div class="alert alert-fill-primary d-inline-block mb-2 mr-2">
                                    {{ $lokasi->nama_tampilan }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($data)
            <div class="mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="card-title mb-0">DOKUMEN</p>
                        </div>
                        <hr>
                        <div class="template-demo col-md-6">
                            @foreach($data->getDokumenWajibAttribute() as $jenis)
    @php
        $dokumenMhs = $dokumenMahasiswa->firstWhere('jenis_dokumen_id', $jenis->id);
    @endphp

    <div class="alert alert-fill-primary mb-2">{{ $jenis->nama }}</div>

    @if($dokumenMhs)
        <div class="mb-2">
            <a href="{{ route('dokumen.download-dokumen-mhs', $dokumenMhs->id) }}">
                <img src="{{ asset('storage/' . $dokumenMhs->path . $dokumenMhs->nama) }}"
                     alt="{{ $jenis->nama }}" width="150" height="150">
            </a>
        </div>
    @else
        <p class="text-muted">Belum ada dokumen diunggah.</p>
    @endif

    <form action="{{ $dokumenMhs ? route('dokumen.update-dokumen-mhs', $dokumenMhs->id) : route('dokumen.upload-dokumen-mhs') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if($dokumenMhs) @method('PUT') @endif
        <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">
        <div class="input-group mb-3">
            <input type="file" name="file" class="form-control"
                   onchange="previewImage(this, {{ $jenis->id }})">
            <div class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary">Simpan</button>
            </div>
        </div>
        <small class="form-text text-muted">Maks 5MB. Format: pdf, doc, jpg, png</small>
    </form>
    <hr>
@endforeach

                            <script>
                                function previewImage(input, id) {
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();
                                        var previewId = 'preview_dokumen_' + id;
                                        var errorId = 'error_dokumen_' + id;
                                        var extension = input.files[0].name.split('.').pop().toLowerCase();
                                        var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'docx'];

                                        document.getElementById(errorId).classList.add('d-none');

                                        if (allowedExtensions.includes(extension)) {
                                            reader.onload = function (e) {
                                                var previewElement = document.getElementById(previewId);

                                                if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(extension)) {
                                                    previewElement.src = e.target.result;
                                                } else if (extension === 'pdf') {
                                                    previewElement.src = "{{ asset('images/pdf_file_icon.svg') }}";
                                                } else if (['doc', 'docx'].includes(extension)) {
                                                    previewElement.src = "{{ asset('images/doc_file_icon.svg') }}";
                                                }
                                            };
                                            reader.readAsDataURL(input.files[0]);
                                        } else {
                                            document.getElementById(errorId).classList.remove('d-none');
                                            input.value = '';
                                        }
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        @endif
@endsection
@push('js')
<script>
  $(document).ready(function () {
    // Pastikan meta tag CSRF token ada di layouts.template
    // Contoh: <meta name="csrf-token" content="{{ csrf_token() }}">
    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});
    function modalAction(url = '') {
    $('#myModal .modal-content').load(url, function() {
        $('#myModal').modal('show');
        
        // Re-bind the close button event after content loads
        $(document).off('click', '[data-dismiss="modal"]').on('click', '[data-dismiss="modal"]', function() {
            $('#myModal').modal('hide');
        });
    });
}

</script>
@endpush


