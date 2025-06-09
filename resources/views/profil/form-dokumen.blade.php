<div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{$title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
<form id="form-tambah"
      enctype="multipart/form-data"
      action="{{ isset($dokumen) ? route('dokumen.update-dokumen-mhs', $dokumen->id) : route('dokumen.upload-dokumen-mhs') }}"
      method="POST">
    @csrf
    @if(isset($dokumen))
        @method('PUT')
    @endif

                <div class="form-group">
                    <label for="label">Label Dokumen</label>
                    <input type="text" name="label" id="label" class="form-control"
                            value="{{ old('label', $dokumen->label ?? '') }}" required>

                </div>

                <div class="form-group">
                    <label for="jenis_dokumen_id">Jenis Dokumen</label>
                    <select name="jenis_dokumen_id" id="jenis_dokumen_id" class="form-control" required>
                        <option value="">Pilih Jenis Dokumen</option>
                        @foreach($dokumenTambahan as $d)
                            <option value="{{ $d->id }}"
                                    {{ (isset($dokumen) && $dokumen->jenis_dokumen_id == $d->id) ? 'selected' : '' }}>
                                {{ $d->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_dokumen_id')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="alert alert-danger d-none" id="error_dokumen">
                    Format file tidak didukung! Gunakan format (.jpg, .jpeg, .png, .doc, .docx, .pdf)
                </div>

                <div class="form-group">
                    <label for="file">File Dokumen</label>
                    <div class="mb-2" style="cursor: pointer;">
                        <img id="preview_dokumen" src="{{ isset($dokumen) ? asset($dokumen->getDokumenPath()) : asset('images/placeholder.png') }}"
                             width="150" height="150">
                    </div>

                    @if(isset($dokumen))
    <a href="{{ route('dokumen.download-dokumen-mhs', $dokumen->id) }}" class="btn btn-sm btn-info" target="_blank">
        Lihat / Unduh Dokumen
    </a>
@endif

                    <input type="file" class="form-control" id="file" name="file"
                           onchange="previewDokumen(this);"
                           accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                           required>
                    <small>
                        Ukuran (Max: 5000Kb) Ekstensi (.jpg,.jpeg,.png,.doc,.docx,.pdf)
                    </small>
                    @error('file')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewDokumen(input) {
        if (!input.files || input.files.length === 0) {
            document.getElementById('preview_dokumen').src = "{{ asset('images/placeholder.png') }}";
            return;
        }

        var reader = new FileReader();
        var preview = document.getElementById('preview_dokumen');
        var errorBox = document.getElementById('error_dokumen');
        var extension = input.files[0].name.split('.').pop().toLowerCase();
        var allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'docx'];

        errorBox.classList.add('d-none');

        if (!allowed.includes(extension)) {
            errorBox.classList.remove('d-none');
            input.value = '';
            preview.src = "{{ asset('images/placeholder.png') }}";
            return;
        }

        reader.onload = function (e) {
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(extension)) {
                preview.src = e.target.result;
            } else if (extension === 'pdf') {
                preview.src = "{{ asset('images/pdf_file_icon.svg') }}";
            } else {
                preview.src = "{{ asset('images/doc_file_icon.svg') }}";
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
</script>

<div class="card mb-3 shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            @php
                $ext = pathinfo($dokumen->nama, PATHINFO_EXTENSION);
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                $iconPath = match($ext) {
                    'pdf' => asset('images/pdf_file_icon.svg'),
                    'doc', 'docx' => asset('images/doc_file_icon.svg'),
                    default => asset('images/file_icon.svg'),
                };
            @endphp

            <div class="me-3">
                @if($isImage)
                    <img src="{{ asset('storage/' . $dokumen->path . $dokumen->nama) }}" width="70" height="70" style="object-fit: cover;" alt="Preview">
                @else
                    <img src="{{ $iconPath }}" width="70" alt="File">
                @endif
            </div>
            <div>
                <strong>{{ $dokumen->label ?? '-' }}</strong><br>
                <small>{{ $dokumen->jenisDokumen->nama ?? '-' }}</small>
            </div>
        </div>

        <div>
            <a href="{{ route('dokumen.download-dokumen-mhs', $dokumen->id) }}" class="btn btn-sm btn-success me-2">Download</a>
            <form action="{{ route('dokumen.delete-dokumen-mhs', $dokumen->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Hapus dokumen ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
            </form>
        </div>
    </div>
</div>


