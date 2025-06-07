{{-- resources/views/lowongan/create_ajax.blade.php --}}
<form action="{{ url('/lowongan/ajax') }}"
      method="POST"
      id="form-tambah-lowongan"
      autocomplete="off"
      enctype="multipart/form-data">
  @csrf

  {{-- ───── Modal header ───── --}}
  <div class="modal-header">
    <h5 class="modal-title">Tambah Lowongan Magang</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"></button>
  </div>

  {{-- ───── Modal body ───── --}}
  <div class="modal-body">

    {{-- Judul --}}
    <div class="form-group">
      <label for="judul">Judul</label>
      <input type="text" class="form-control" id="judul" name="judul" required>
      <span class="text-danger" id="error-judul"></span>
    </div>

    {{-- Deskripsi --}}
    <div class="form-group">
      <label for="deskripsi">Deskripsi</label>
      <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
      <span class="text-danger" id="error-deskripsi"></span>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="tanggal_mulai_magang">Tanggal Mulai</label>
        <input type="date" class="form-control" id="tanggal_mulai_magang" name="tanggal_mulai_magang" required>
        <span class="text-danger" id="error-tanggal_mulai_magang"></span>
      </div>
      <div class="form-group col-md-6">
        <label for="deadline_lowongan">Deadline</label>
        <input type="date" class="form-control" id="deadline_lowongan" name="deadline_lowongan" required>
        <span class="text-danger" id="error-deadline_lowongan"></span>
      </div>
    </div>

    {{-- Lokasi --}}
    <div class="form-group">
      <label for="lokasi">Lokasi</label>
      <input type="text" class="form-control" id="lokasi" name="lokasi" required>
      <span class="text-danger" id="error-lokasi"></span>
    </div>

    <div class="form-row">
      {{-- Perusahaan --}}
      <div class="form-group col-md-6">
        <label for="perusahaan_id">Perusahaan</label>
        <select class="form-control" id="perusahaan_id" name="perusahaan_id" required>
          <option value="">-- Pilih Perusahaan --</option>
          @foreach($perusahaan as $p)
            <option value="{{ $p->perusahaan_id }}">{{ $p->nama }}</option>
          @endforeach
        </select>
        <span class="text-danger" id="error-perusahaan_id"></span>
      </div>

      {{-- Periode --}}
      <div class="form-group col-md-6">
        <label for="periode_id">Periode</label>
        <select class="form-control" id="periode_id" name="periode_id" required>
          <option value="">-- Pilih Periode --</option>
          @foreach($periode as $per)
            <option value="{{ $per->periode_id }}">{{ $per->nama_periode }}</option>
          @endforeach
        </select>
        <span class="text-danger" id="error-periode_id"></span>
      </div>
    </div>

    {{-- Sylabus (pilih salah satu) --}}
    <div class="form-group">
      <label>Sylabus (pilih salah satu)</label>

      {{-- URL --}}
      <input type="url" class="form-control mb-2"
             id="sylabus_path"
             name="sylabus_path"
             placeholder="https://… (jika ingin pakai link)">
      <span class="text-danger" id="error-sylabus_path"></span>

      {{-- PDF file --}}
      <input type="file" class="form-control"
             id="sylabus_file"
             name="sylabus_file"
             accept="application/pdf">
      <span class="text-danger" id="error-sylabus_file"></span>
    </div>

  </div>

  {{-- ───── Modal footer ───── --}}
  <div class="modal-footer">
    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </div>
</form>

{{-- ───── Script ───── --}}
<script>
$(function () {
  $('#form-tambah-lowongan').validate({
    rules: {
      judul:               {required: true, maxlength: 255},
      deskripsi:           {required: true},
      tanggal_mulai_magang:{required: true, date: true},
      deadline_lowongan:   {required: true, date: true},
      lokasi:              {required: true},
      perusahaan_id:       {required: true},
      periode_id:          {required: true},
      sylabus_path: {
        url: true,
        required: function () { return $('#sylabus_file').val() === ''; }
      },
      sylabus_file: {
        extension:'pdf',
        required: function () {
          return $('#sylabus_path').val().trim() === '';
        }
      }
    },
    errorElement:'span',
    errorPlacement:function (err, el) {
      err.addClass('invalid-feedback');
      el.closest('.form-group, .form-row').append(err);
    },
    highlight:function (el) { $(el).addClass('is-invalid'); },
    unhighlight:function (el) { $(el).removeClass('is-invalid'); },

    submitHandler:function (form) {
      const fd = new FormData(form);

      $.ajax({
        url: form.action,
        type: form.method,
        data: fd,
        contentType:false,
        processData:false,
        success(res) {
          if (res.status) {
            $('#myModal').modal('hide');
            Swal.fire({icon:'success', title:'Berhasil', text:res.message});
            if ($.fn.DataTable.isDataTable('#lowongan-table')) {
              $('#lowongan-table').DataTable().ajax.reload(null, false);
            }
          } else {
            $.each(res.msgField, (f, m) => $('#error-'+f).text(m[0]));
            Swal.fire({icon:'error', title:'Gagal', text:res.message});
          }
        },
        error(xhr) {
          // log details to console to help debugging client-side issues
          console.error('AJAX error', xhr.responseText || xhr.statusText);
          Swal.fire({icon:'error', title:'Error', text:'Kesalahan server.'});
        }
      });
      return false;
    }
  });
});
</script>
