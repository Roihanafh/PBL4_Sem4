@empty($lowongan)
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Kesalahan</h5></div>
        <div class="modal-body">
          <div class="alert alert-danger">Data tidak ditemukan.</div>
        </div>
      </div>
    </div>
@else
<form action="{{ url('/lowongan/' . $lowongan->lowongan_id . '/update_ajax') }}" method="POST" id="form-edit-lowongan">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title">Edit Lowongan Magang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="judul">Judul</label>
            <input type="text" class="form-control" id="judul" name="judul"
                   value="{{ $lowongan->judul }}" required>
            <div class="text-danger" id="error-judul"></div>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required>{{ $lowongan->deskripsi }}</textarea>
            <div class="text-danger" id="error-deskripsi"></div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="tanggal_mulai_magang">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai_magang" name="tanggal_mulai_magang"
                       value="{{ $lowongan->tanggal_mulai_magang->format('Y-m-d') }}" required>
                <div class="text-danger" id="error-tanggal_mulai_magang"></div>
            </div>
            <div class="form-group col-md-6">
                <label for="deadline_lowongan">Deadline</label>
                <input type="date" class="form-control" id="deadline_lowongan" name="deadline_lowongan"
                       value="{{ $lowongan->deadline_lowongan->format('Y-m-d') }}" required>
                <div class="text-danger" id="error-deadline_lowongan"></div>
            </div>
        </div>

        <div class="form-group">
            <label for="lokasi">Lokasi</label>
            <input type="text" class="form-control" id="lokasi" name="lokasi"
                   value="{{ $lowongan->lokasi }}" required>
            <div class="text-danger" id="error-lokasi"></div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="perusahaan_id">Perusahaan</label>
                <select class="form-control" id="perusahaan_id" name="perusahaan_id" required>
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach($perusahaan as $p)
                        <option value="{{ $p->perusahaan_id }}"
                          {{ $lowongan->perusahaan_id == $p->perusahaan_id ? 'selected' : '' }}>
                          {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
                <div class="text-danger" id="error-perusahaan_id"></div>
            </div>
            <div class="form-group col-md-6">
                <label for="periode_id">Periode</label>
                <select class="form-control" id="periode_id" name="periode_id" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periode as $per)
                        <option value="{{ $per->periode_id }}"
                          {{ $lowongan->periode_id == $per->periode_id ? 'selected' : '' }}>
                          {{ $per->nama_periode }}
                        </option>
                    @endforeach
                </select>
                <div class="text-danger" id="error-periode_id"></div>
            </div>
        </div>

        <div class="form-group">
            <label for="sylabus_path">Link Sylabus (opsional)</label>
            <input type="url" class="form-control" id="sylabus_path" name="sylabus_path"
                   value="{{ $lowongan->sylabus_path }}">
            <div class="text-danger" id="error-sylabus_path"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-edit-lowongan").validate({
        rules: {
            judul: { required: true, maxlength: 255 },
            deskripsi: { required: true },
            tanggal_mulai_magang: { required: true, date: true },
            deadline_lowongan: { required: true, date: true },
            lokasi: { required: true },
            perusahaan_id: { required: true },
            periode_id: { required: true },
            sylabus_path: { url: true }
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                        if ($.fn.DataTable.isDataTable('#lowongan-table')) {
                            $('#lowongan-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        $.each(response.msgField, function(field, msgs) {
                            $('#error-' + field).text(msgs[0]);
                        });
                        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan server.' });
                }
            });
            return false;
        }
    });
});
</script>
@endempty
