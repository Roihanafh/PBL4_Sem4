@empty($mahasiswa)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/update_mhs') }}" method="POST" id="form-edit" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-header" style="background-color: #1a2e4f; color: white;">
            <h5 class="modal-title">Edit Data mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            {{-- Foto Profil Bulat --}}
            <div class="mb-2  text-center">
                @if ($mahasiswa->profile_picture)
                    <img id="preview-img" src="{{ asset('storage/' . $mahasiswa->profile_picture) }}" 
                         alt="Foto Profil" 
                         class="img-thumbnail rounded-circle" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img id="preview-img" src="{{ asset('img/user.png') }}" 
                         alt="Foto Profil Default" 
                         class="img-thumbnail rounded-circle" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @endif
            </div>

            {{-- Input file tersembunyi --}}
            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*">

            {{-- Tombol Edit dan Hapus --}}
            <div class="mb-4 text-center">
                <button type="button" id="btn-edit-profile" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button type="button" id="btn-delete-profile" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i> Hapus Profile
                </button>
            </div>


            {{-- Form input lain --}}
             <div class="form-group">
                <label>NIM</label>
                <input type="text" name="mhs_nim" id="mhs_nim" class="form-control" 
                       value="{{ $mahasiswa->mhs_nim }}" readonly>
                <small class="form-text text-muted">NIM tidak dapat diubah.</small>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" id="full_name" class="form-control" 
                       value="{{ $mahasiswa->full_name }}" required>
                <small id="error-full_name" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control">{{ $mahasiswa->alamat }}</textarea>
                <small id="error-alamat" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telp" id="telp" class="form-control" 
                       value="{{ $mahasiswa->telp }}">
                <small id="error-telp" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Status Magang</label>
                <input type="text" class="form-control" value="{{ ucfirst($mahasiswa->status_magang) }}" readonly>
            </div>

            <div class="form-group">
                <label>Prodi</label>
                <input type="text" class="form-control" 
                       value="{{ $mahasiswa->prodi->nama_prodi ?? '-' }}" readonly>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input value="{{ $mahasiswa->user->username }}" type="text" name="username" id="username" class="form-control" required>
                <small id="error-username" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input value="" type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">Abaikan jika tidak ingin ubah password</small>
                <small id="error-password" class="error-text form-text text-danger"></small>
            </div>


        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Batal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
    

<script>
$(document).ready(function () {

    // Klik tombol Edit Profile untuk trigger input file
    $('#btn-edit-profile').click(function () {
        $('#profile_picture').click();
    });

    // Preview gambar saat pilih file baru
    $('#profile_picture').change(function () {
        const input = this;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#preview-img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    });

    // Tombol hapus foto profil dengan Ajax DELETE
    $('#btn-delete-profile').click(function () {
        Swal.fire({
            title: 'Yakin ingin menghapus foto profil?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('mhs.hapus_foto', $mahasiswa->mhs_nim) }}",
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.status) {
                            // Reset preview gambar ke default
                            $('#preview-img').attr('src', "{{ asset('img/user.png') }}");
                            // Kosongkan input file
                            $('#profile_picture').val('');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal menghapus foto profil.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server.'
                        });
                    }
                });
            }
        });
    });

    // Validasi dan submit ajax sama seperti sebelumnya...
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'Ukuran file maksimal {0} byte.');

    $("#form-edit").validate({
        rules: {
            full_name: { required: true, maxlength: 100 },
            alamat: { maxlength: 255 },
            telp: { maxlength: 20 },
            prodi_id: { digits: true },
            username: { required: true, maxlength: 20 },
            password: { minlength: 5, maxlength: 20 },
            profile_picture: { extension: "jpg|jpeg|png|webp", filesize: 2048000 } // max 2 MB
        },
        messages: {
            profile_picture: {
                extension: "Format file harus jpg, jpeg, png, atau webp.",
                filesize: "Ukuran file maksimal 2 MB."
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        if ($.fn.DataTable.isDataTable('#mahasiswa-table')) {
                            $('#mahasiswa-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        $('.text-danger').text('');
                        if(response.msgField){
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message || 'Mohon cek kembali inputan anda.'
                        });
                    }
                },
                error: function() {
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
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>
@endempty
