{{-- resources/views/rekomendasi/partials/list.blade.php --}}
@forelse($lowongan as $l)
  <div class="col-md-4 mb-4">
    <div class="card h-100 shadow">
      <div class="card-body">
        <p class="text-secondary small">
          <strong>Score:</strong> {{ number_format($l->smart_score, 4) }}
        </p>
        <h6 class="text-muted">{{ $l->perusahaan->nama ?? '-' }}</h6>
        <h5 class="card-title">{{ $l->judul }}</h5>
        <p class="text-secondary">{{ $l->lokasi }}</p>
        <p class="small mb-1">
          <span class="badge bg-success">Umum</span>
          <span class="badge bg-secondary">{{ $l->durasi ?? '-' }} bulan</span>
          <span class="badge bg-dark">Onsite</span>
        </p>
        <p class="text-danger small mb-2">
          Penutupan: {{ $l->deadline_lowongan->format('d M Y') }}
        </p>
        {{-- NOTE: tambahkan class "detail-link" dan data-url untuk AJAX --}}
        <a href="{{ route('rekomendasi.show', $l->lowongan_id) }}"
           class="btn btn-outline-primary w-100 detail-link"
           data-url="{{ route('rekomendasi.show', $l->lowongan_id) }}">
          Lihat Detail
        </a>
      </div>
      <div class="card-footer text-muted small">
        Dibuat {{ $l->tanggal_mulai_magang?->diffForHumans() ?? '-' }}
      </div>
    </div>
  </div>
@empty
  <div class="col-12 text-center text-muted">
    Tidak ada lowongan sesuai kriteria.
  </div>
@endforelse
