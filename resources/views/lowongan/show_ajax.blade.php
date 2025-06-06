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
    <h5 class="modal-title">Detail Lowongan</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"></button>
  </div>
  <div class="modal-body">
    <table class="table table-sm table-bordered">
      <tr><th>Judul</th>
          <td>{{ $lowongan->judul }}</td>
      </tr>
      <tr><th>Deskripsi</th>
          <td>{{ $lowongan->deskripsi }}</td>
      </tr>
      <tr><th>Tanggal Mulai</th>
          <td>{{ $lowongan->tanggal_mulai_magang->format('d-m-Y') }}</td>
      </tr>
      <tr><th>Deadline</th>
          <td>{{ $lowongan->deadline_lowongan->format('d-m-Y') }}</td>
      </tr>
      <tr><th>Lokasi</th>
          <td>{{ $lowongan->lokasi }}</td>
      </tr>
      <tr><th>Perusahaan</th>
          <td>{{ $lowongan->perusahaan->nama ?? '-' }}</td>
      </tr>
      <tr><th>Periode</th>
          <td>{{ $lowongan->periode->periode ?? '-' }}</td>
      </tr>
      <tr><th>Link Sylabus</th>
          <td>
            @if($lowongan->sylabus_path)
              <a href="{{ $lowongan->sylabus_path }}" target="_blank">Lihat Sylabus</a>
            @else
              -
            @endif
          </td>
      </tr>
      <tr><th>Status</th>
          <td>{{ ucfirst($lowongan->status) }}</td>
      </tr>
      <tr><th>Kuota</th>
          <td>{{ $lowongan->kuota }} mahasiswa</td>
      </tr>
      <tr><th>Durasi</th>
          <td>{{ $lowongan->durasi }}</td>
      </tr>
      <tr><th>Tipe Bekerja</th>
          <td>{{ $lowongan->tipe_bekerja }}</td>
      </tr>
    </table>
  </div>
    <div class="modal-footer">
        <button onclick="modalAction('{{ url('/lowongan/'.$lowongan->lowongan_id.'/edit_ajax') }}')" class="btn btn-warning btn-sm">Edit</button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
    </div>
@endempty
