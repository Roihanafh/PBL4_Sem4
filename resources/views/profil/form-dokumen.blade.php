<div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{$title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="form-tambah" enctype="multipart/form-data" action="{{ isset($dokumen) ? route('profil.dokumen.update', $dokumen->id) : route('profil.dokumen.store') }}"
                  method="POST">
                @csrf
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
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var previewId = 'preview_dokumen';
            var errorId = 'error_dokumen';
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
