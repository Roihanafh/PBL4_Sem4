<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Ajukan Magang Baru</h5>
    <button type="button" class="close" onclick="$('#myModal').modal('hide')" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="formPengajuanMagang" method="POST" action="{{ url('pengajuan-magang-mhs/store') }}">
        @csrf {{-- CSRF token untuk keamanan --}}
        
        <div class="form-group">
            <label for="mhs_nim">NIM Mahasiswa</label>
            <input type="text" class="form-control" id="mhs_nim" name="mhs_nim" value=""required>
            <small class="text-danger" id="mhs_nim_error"></small>
        </div>

        <div class="form-group">
            <label for="lowongan_id">Pilih Lowongan Magang</label>
            <select class="form-control" id="lowongan_id" name="lowongan_id" required>
                <option value="">-- Pilih Lowongan --</option>
                @foreach ($lowongan as $lowongan)
                    <option value="{{ $lowongan->lowongan_id }}">{{ $lowongan->judul }}</option>
                @endforeach
            </select>
            <small class="text-danger" id="lowongan_id_error"></small>
        </div>

        <div class="form-group">
            <label for="tanggal_lamaran">Tanggal Lamaran</label>
            <input type="date" class="form-control" id="tanggal_lamaran" name="tanggal_lamaran" value="{{ date('Y-m-d') }}" required>
            <small class="text-danger" id="tanggal_lamaran_error"></small>
        </div>

        {{-- Status akan otomatis 'pending' di backend, tidak perlu input di sini --}}
        {{-- <input type="hidden" name="status" value="pending"> --}}

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Ajukan Magang</button>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {
        // Reset error messages on modal open (if needed, though .load() usually cleans it)
        $('#formPengajuanMagang').on('submit', function(e) {
            e.preventDefault(); // Mencegah form submit secara default

            // Clear previous error messages
            $('.text-danger').text('');

            $.ajax({
                url: $(this).attr('action'), // URL dari atribut action form
                method: $(this).attr('method'), // Method dari atribut method form (POST)
                data: $(this).serialize(), // Serialize data form
                dataType: 'json', // Harapkan response JSON
                success: function(response) {
                    if (response.status) {
                        // Jika berhasil
                        $('#myModal').modal('hide'); // Tutup modal
                        toastr.success(response.message); // Tampilkan pesan sukses (gunakan toastr jika ada)
                        $('#formPengajuanMagang')[0].reset();
                        // Refresh DataTable setelah data baru ditambahkan
                        if (typeof tablePengajuanMagang !== 'undefined') {
                            tablePengajuanMagang.ajax.reload();
                        } else {
                            location.reload(); // Jika DataTable belum terinisialisasi, refresh halaman
                        }
                    } else {
                        // Jika ada error dari server (misal: validasi kustom)
                        toastr.error(response.message); // Tampilkan pesan error
                    }
                },
                error: function(xhr) {
                    // Tangani error AJAX
                    if (xhr.status === 422) { // Error validasi Laravel
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]); // Tampilkan error di bawah input
                        });
                    } else if (xhr.status === 409) { // Konflik (misal: sudah ada pengajuan)
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Terjadi kesalahan server: ' + (xhr.responseJSON.message || 'Unknown error'));
                        console.error(xhr.responseText);
                    }
                }
            });
        });
    });
</script>
