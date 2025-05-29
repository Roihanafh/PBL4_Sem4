@extends('layouts.template_mhs')

@section('content')
<div class="container mt-4">
  {{-- Statistik ringkas --}}
  <div class="d-flex mb-4">
    <div class="me-4"><strong>Total Posisi:</strong> {{ $totalPositions }}</div>
    <div class="me-4"><strong>Total Perusahaan:</strong> {{ $totalCompanies }}</div>
    <div><strong>Total Job:</strong> {{ $totalJobs }}</div>
  </div>

  <div class="row">
  {{-- ======================  STYLE UNTUK KARTU BESAR  ====================== --}}
  <style>
    .sidebar-card         { min-height: 200px; }            /* tinggi minimum */
    .sidebar-card strong  { font-size: 1.15rem; }           /* judul lebih besar */
    .sidebar-card h6      { font-size: .95rem; }            /* nama perusahaan */
  </style>

  <div class="row">
    {{-- ======================  SIDEBAR KIRI  ====================== --}}
    <div class="col-md-4 mb-4">
      <h6 class="mb-2">Lowongan Lainnya</h6>

      <div class="overflow-auto" style="max-height:calc(100vh - 220px);">
        @foreach($lowonganList as $l)
          <a href="{{ route('rekomendasi.show', $l->lowongan_id) }}" class="text-decoration-none">
            <div class="card shadow-sm mb-4 sidebar-card
                {{ $l->lowongan_id == $lowongan->lowongan_id ? 'border-primary' : '' }}">
              <div class="card-body text-center p-4"><!-- p-4 = padding besar -->
                @if($l->perusahaan->logo_path)
                  <img src="{{ asset('uploads/logos/'.$l->perusahaan->logo_path) }}"
                       alt="Logo {{ $l->perusahaan->nama }}"
                       class="img-fluid mb-3" style="max-height:90px">
                @endif

                <h6 class="text-muted mb-1 text-truncate">{{ $l->perusahaan->nama }}</h6>
                <strong class="d-block mb-1 text-truncate">{{ $l->judul }}</strong>

                <small class="text-secondary d-block mb-1">
                  <i class="fas fa-map-marker-alt"></i> {{ $l->lokasi }}
                </small>

                <div class="small mb-2">
                  <span class="badge bg-success">Umum</span>
                  <span class="badge bg-secondary">{{ $l->periode->durasi }} bln</span>
                  <span class="badge bg-dark">Onsite</span>
                </div>

                <small class="text-danger d-block">
                  Penutupan: {{ $l->deadline_lowongan->format('d M Y') }}
                </small>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>


    {{-- ======================  DETAIL KANAN  ====================== --}}
    <div class="col-md-8">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body">
          {{-- Ringkasan posisi & pelamar --}}
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <span class="badge bg-success">Umum</span>
              <small class="ms-2">
                {{ $lowongan->kuota }} Posisi &bull; {{ $lowongan->lamaran->count() }} Pelamar
              </small>
            </div>
            <a href="#"
               onclick="alert('Fitur lamaran akan menyusul!')"
               class="btn btn-primary">
              Daftar Sekarang
            </a>
          </div>

          {{-- Judul & perusahaan --}}
          <h4>{{ $lowongan->judul }}</h4>
          <h6 class="text-muted">{{ $lowongan->perusahaan->nama }}</h6>
          <p class="text-secondary mb-4">
            <i class="fas fa-map-marker-alt"></i> {{ $lowongan->lokasi }}
            &nbsp;&bull;&nbsp;
            <i class="fas fa-briefcase"></i> Onsite
          </p>

          {{-- Tabs Deskripsi / Perusahaan --}}
          <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#deskripsi">Deskripsi Lowongan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#perusahaan">Perusahaan</a>
            </li>
          </ul>

          <div class="tab-content">
            {{-- Tab Deskripsi --}}
            <div class="tab-pane fade show active" id="deskripsi">
              <h5><i class="fas fa-info-circle"></i> Rincian Lowongan</h5>
              <p>{!! nl2br(e($lowongan->deskripsi)) !!}</p>

              <h5 class="mt-4"><i class="fas fa-file-pdf"></i> Silabus</h5>
              @if($lowongan->sylabus_path)
                <a href="{{ asset($lowongan->sylabus_path) }}"
                   target="_blank"
                   class="btn btn-outline-primary mb-4">
                  <i class="fas fa-download"></i> Unduh Silabus
                </a>
              @else
                <p class="text-muted">Tidak ada silabus tersedia.</p>
              @endif

              <h5 class="mt-4"><i class="fas fa-calendar-alt"></i> Tanggal Penting</h5>
              <ul>
                <li>Durasi: {{ $lowongan->periode->durasi }} bulan</li>
                <li>Penutupan lamaran: {{ $lowongan->deadline_lowongan->format('d M Y') }}</li>
                <li>Pengumuman: {{ optional($lowongan->pengumuman)->format('d M Y') ?? '-' }}</li>
              </ul>
            </div>

            {{-- Tab Perusahaan --}}
            <div class="tab-pane fade" id="perusahaan">
              <h5><i class="fas fa-building"></i> {{ $lowongan->perusahaan->nama }}</h5>
              <p>{{ $lowongan->perusahaan->deskripsi ?? '—' }}</p>
            </div>
          </div>

          {{-- Share link --}}
          <div class="mt-4">
            <a href="javascript:void(0)" class="text-decoration-none">
              <i class="fas fa-share-alt"></i> Bagikan Lowongan
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
