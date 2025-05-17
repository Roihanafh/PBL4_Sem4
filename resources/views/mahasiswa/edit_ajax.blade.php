@empty($mahasiswa)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
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
    <form action="{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title">Edit Data Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">

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
    $("#form-edit").validate({
        rules: {
            username: {required: true, maxlength: 20},
            password: {
                minlength: 5,
                maxlength: 20
            },
            full_name: {
                required: true,
                maxlength: 100
            },
            alamat: {
                maxlength: 255
            },
            telp: {
                maxlength: 20
            },
            prodi_id: {
                digits: true
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
               success: function(response) {
                    if(response.status) {
                        $('#myModal').modal('hide'); // Tutup modal

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });

                        // Reload DataTable
                        if ($.fn.DataTable.isDataTable('#mahasiswa-table')) {
                            $('#mahasiswa-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        $('.text-danger').text(''); // reset error text
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
            return false; // prevent default submit
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
