@extends('layouts.template_mhs')

@section('content')
<div class="container mt-4">

  {{-- Filter form --}}
  <form method="GET" action="{{ url()->current() }}" class="card mb-4 p-3 bg-dark text-white">
    <div class="row g-3">
      {{-- Posisi / Jabatan --}}
      <div class="col-md-2">
        <input
          type="text"
          name="posisi"
          class="form-control"
          placeholder="Posisi / Jabatan"
          value="{{ request('posisi') }}"
        >
      </div>

      {{-- Skill yang cocok --}}
      <div class="col-md-2">
        <input
          type="text"
          name="skill"
          class="form-control"
          placeholder="Skill yang cocok"
          value="{{ request('skill') }}"
        >
      </div>

      {{-- Lokasi --}}
      <div class="col-md-2">
        <input
          type="text"
          name="lokasi"
          class="form-control"
          placeholder="Lokasi"
          value="{{ request('lokasi') }}"
        >
      </div>

      {{-- Gaji Minimum --}}
      <div class="col-md-2">
        <input
          type="number"
          name="gaji"
          class="form-control"
          placeholder="Gaji Minimum"
          value="{{ request('gaji') }}"
        >
      </div>

      {{-- Durasi (bulan) --}}
      <div class="col-md-2">
        <select name="durasi" class="form-select">
          <option value="">Durasi (bulan)</option>
          <option value="3" {{ request('durasi') == '3' ? 'selected' : '' }}>3</option>
          <option value="6" {{ request('durasi') == '6' ? 'selected' : '' }}>6</option>
        </select>
      </div>

      {{-- Submit --}}
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
          <i class="fas fa-search"></i> Cari
        </button>
      </div>
    </div>
  </form>

  {{-- Daftar hasil --}}
  <div class="row" id="rekomendasi-list">
    @forelse($lowongan as $l)
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow">
          <div class="card-body">
            <h6 class="text-muted">
              {{ $l->perusahaan->nama ?? '-' }}
            </h6>
            <h5 class="card-title">
              {{ $l->judul }}
            </h5>
            <p class="text-secondary">
              {{ $l->lokasi }}
            </p>
            <p class="small mb-1">
              <span class="badge bg-success">Umum</span>
              <span class="badge bg-secondary">
                {{ $l->periode->durasi ?? '-' }} bulan
              </span>
              <span class="badge bg-dark">Onsite</span>
            </p>
            <p class="text-danger small mb-2">
              Penutupan: {{ $l->deadline_lowongan->format('d M Y') }}
            </p>
            <a 
              href="{{ url('/lowongan/'.$l->lowongan_id.'/show_ajax') }}" 
              class="btn btn-outline-primary w-100"
            >
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
  </div>
</div>
@endsection
