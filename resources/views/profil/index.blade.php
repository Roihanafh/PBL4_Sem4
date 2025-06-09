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
                            <p class="card-title mb-0">DOKUMEN WAJIB</p>
                        </div>
                        <hr>
                        <div class="template-demo col-md-6">
                            @foreach($data->getDokumenWajibAttribute() as $dokumen)
                                <div class="alert alert-fill-primary mb-2 mr-2">
                                    {{ $dokumen->nama }}
                                </div>

                                <div class="alert alert-danger d-none" id="error_dokumen_{{$dokumen->id}}">
                                    Format file tidak didukung! Gunakan format (.jpg, .jpeg, .png, .doc, .docx, .pdf)
                                </div>

                                <div class="mb-2 " style="cursor: pointer;">
                                    <a href="{{ $dokumen->getDokumenIdMhs($data->mhs_nim) ? route('dokumen.download-dokumen-mhs', $dokumen->getDokumenIdMhs($data->mhs_nim)) : '#' }}">
                                    <img id="preview_dokumen_{{$dokumen->id}}"
                                         src="{{$dokumen->getDokumenPathFromMhs($data->mhs_nim) ?? "#"}}"
                                         alt="Upload File"
                                         width="150" height="150">
                                    </a>
                                </div>
                                <h5 class="mt-4">Dokumen yang Telah Diupload</h5>

@forelse($dokumenMahasiswa as $dokumen) 
    @include('components.dokumen-item', ['dokumen' => $dokumen])
@empty
    <p class="text-muted">Belum ada dokumen.</p>
@endforelse
@if(isset($dokumen))
    <a href="{{ asset('storage/' . $dokumen->path . $dokumen->nama) }}" target="_blank" class="btn btn-sm btn-info mt-2">
        Lihat / Unduh Dokumen
    </a>
@endif

                                <form
                                    action="{{ $dokumen->getDokumenIdMhs($data->mhs_nim) !== null ? route('dokumen.update-dokumen-mhs', $dokumen->getDokumenIdMhs($data->mhs_nim)) : route('dokumen.upload-dokumen-mhs') }}"
                                    enctype="multipart/form-data"
                                    method="POST">
                                    @csrf
                                    @if($dokumen->getDokumenIdMhs($data->mhs_nim) !== null)
                                        @method('PUT')
                                    @endif
                                    <div class="input-group">
                                            <input type="file" class="form-control" id="file"
                                                   onchange="previewImage(this, {{$dokumen->id}});"
                                                   name="file">
                                            <input type="hidden" name="default" value="1">
                                            <input type="hidden" name="jenis_dokumen_id" value="{{$dokumen->id}}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                                <small>
                                    Ukuran (Max: 5000Kb) Ekstensi (.jpg,.jpeg,.png,.doc,.docx,.pdf)
                                </small>
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


