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
    {{-- Sidebar ringkasan lowongan --}}
    <div class="col-md-4 mb-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body text-center">
          @if($lowongan->perusahaan->logo_path)
            <img src="{{ asset('uploads/logos/'.$lowongan->perusahaan->logo_path) }}"
                 alt="Logo {{ $lowongan->perusahaan->nama }}"
                 class="img-fluid mb-3" style="max-height:80px">
          @endif

          <h6 class="text-muted">{{ $lowongan->perusahaan->nama }}</h6>
          <h5 class="card-title">{{ $lowongan->judul }}</h5>
          <p class="text-secondary"><i class="fas fa-map-marker-alt"></i> {{ $lowongan->lokasi }}</p>

          <p class="small mb-1">
            <span class="badge bg-success">Umum</span>
            <span class="badge bg-secondary">{{ $lowongan->periode->durasi }} bulan</span>
            <span class="badge bg-dark">Onsite</span>
          </p>
          <p class="text-danger small mb-2">
            Penutupan: {{ $lowongan->deadline_lowongan->format('d M Y') }}
          </p>
          <div class="text-muted small">
            Dibuat {{ $lowongan->tanggal_mulai_magang->diffForHumans() }}
          </div>
        </div>
      </div>
    </div>

    {{-- Konten detail --}}
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
            {{-- tombol dummy --}}
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
              <a class="nav-link active" data-bs-toggle="tab" href="#deskripsi">
                Deskripsi Lowongan
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#perusahaan">
                Perusahaan
              </a>
            </li>
          </ul>

          <div class="tab-content">
            {{-- Tab Deskripsi --}}
            <div class="tab-pane fade show active" id="deskripsi">
              {{-- Rincian Deskripsi --}}
              <h5><i class="fas fa-info-circle"></i> Rincian Lowongan</h5>
              <div class="mb-4">
                {!! nl2br(e($lowongan->deskripsi)) !!}
              </div>

              {{-- Silabus --}}
              <h5><i class="fas fa-file-pdf"></i> Silabus</h5>
              @if($lowongan->sylabus_path)
                <a href="{{ asset($lowongan->sylabus_path) }}"
                   target="_blank"
                   class="btn btn-outline-primary mb-4">
                  <i class="fas fa-download"></i> Unduh Silabus
                </a>
              @else
                <p class="text-muted">Tidak ada silabus tersedia.</p>
              @endif

              {{-- Tanggal Penting --}}
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
