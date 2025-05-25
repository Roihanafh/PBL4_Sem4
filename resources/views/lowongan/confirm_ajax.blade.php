{{-- resources/views/lowongan/confirm_ajax.blade.php --}}
@php $lowongan = $lowongan ?? null; @endphp

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
    <div class="modal-header">
    <h5 class="modal-title">Non-aktifkan Lowongan</h5>
    <button …></button>
    </div>
    <div class="modal-body">
    Anda yakin ingin menonaktifkan lowongan “{{ $lowongan->judul }}”?
    </div>
    <div class="modal-footer">
    <button onclick="deleteLowongan({{ $lowongan->lowongan_id }})"
            class="btn btn-warning">Ya, Non-aktifkan</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    </div>

@endempty
