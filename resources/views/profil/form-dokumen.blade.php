{{-- Modal Form Upload/Update Dokumen --}}
<div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ $title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="form-tambah"
                  enctype="multipart/form-data"
                  method="POST"
                  action="{{ isset($dokumen) ? route('dokumen.update-dokumen-mhs', $dokumen->id) : route('dokumen.upload-dokumen-mhs') }}">
                @csrf
                @if(isset($dokumen)) @method('PUT') @endif

                {{-- Label Dokumen --}}
                <div class="form-group">
                    <label for="label">Label Dokumen</label>
                    <input type="text" name="label" id="label" class="form-control"
                           value="{{ old('label', $dokumen->label ?? '') }}" required>
                </div>

                {{-- Jenis Dokumen --}}
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

                {{-- Preview Dokumen --}}
                <div class="form-group">
                    <label for="file">File Dokumen</label>

                    <div class="mb-2" style="cursor: pointer;">
                        <img id="preview_dokumen"
                             src="{{ isset($dokumen) ? asset($dokumen->getDokumenPath()) : asset('images/placeholder.png') }}"
                             width="150" height="150">
                    </div>

                    @if(isset($dokumen))
                        <a href="{{ route('dokumen.download-dokumen-mhs', $dokumen->id) }}"
                           class="btn btn-sm btn-info" target="_blank">Lihat / Unduh Dokumen</a>
                    @endif

                    <input type="file" name="file" id="file" class="form-control mt-2"
                           onchange="previewDokumen(this);"
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                           {{ isset($dokumen) ? '' : 'required' }}>

                    <div class="alert alert-danger d-none mt-2" id="error_dokumen">
                        Format file tidak didukung!
                    </div>

                    <small class="form-text text-muted">Ukuran maks: 5MB. Format: jpg, png, doc, pdf</small>
                    @error('file')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Tombol --}}
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
    const preview = document.getElementById('preview_dokumen');
    const errorBox = document.getElementById('error_dokumen');
    const allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

    if (!input.files.length) {
        preview.src = "{{ asset('images/placeholder.png') }}";
        return;
    }

    const file = input.files[0];
    const ext = file.name.split('.').pop().toLowerCase();

    errorBox.classList.add('d-none');

    if (!allowed.includes(ext)) {
        errorBox.classList.remove('d-none');
        input.value = '';
        preview.src = "{{ asset('images/placeholder.png') }}";
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            preview.src = e.target.result;
        } else if (ext === 'pdf') {
            preview.src = "{{ asset('images/pdf_file_icon.svg') }}";
        } else {
            preview.src = "{{ asset('images/doc_file_icon.svg') }}";
        }
    };
    reader.readAsDataURL(file);
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


