{{-- resources/views/rekomendasi/index.blade.php --}}
@extends('layouts.template_mhs')

@section('content')
<div class="container mt-4">

  {{-- ============================= --}}
  {{-- DEBUG: Tampilkan data mahasiswa  --}}
  {{-- ============================= --}}
  <div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white">
      <strong>DEBUG: Data Mahasiswa (User ID: {{ Auth::id() }})</strong>
    </div>
    <div class="card-body text-dark">
      <p><strong>Nama:</strong> {{ $mhs->full_name ?? '-' }}</p>
      <p><strong>Pref (Bidang):</strong> {{ $mhs->pref ?? '-' }}</p>
      <p><strong>Skill:</strong> {{ $mhs->skill ?? '-' }}</p>
      <p><strong>Lokasi Preferensi:</strong> {{ $mhs->lokasi ?? '-' }}</p>
      <p><strong>Gaji Minimum:</strong> Rp {{ number_format($mhs->gaji_minimum, 0, ',', '.') }}</p>
      <p><strong>Durasi Preferensi:</strong> {{ $mhs->durasi }} bulan</p>
    </div>
  </div>
  {{-- ===================================== --}}

  {{-- Filter form --}}
  <form id="filter-form" class="card mb-4 p-3 bg-dark text-white">
    <div class="row g-3">
      <div class="col-md-2">
        <input
          type="text"
          name="posisi"
          class="form-control"
          placeholder="Posisi / Jabatan"
          value="{{ request('posisi') }}"
        >
      </div>

      <div class="col-md-2">
        <input
          type="text"
          name="skill"
          class="form-control"
          placeholder="Skill yang cocok"
          value="{{ request('skill') }}"
        >
      </div>

      <div class="col-md-2">
        <input
          type="text"
          name="lokasi"
          class="form-control"
          placeholder="Lokasi"
          value="{{ request('lokasi') }}"
        >
      </div>

      <div class="col-md-2">
        <input
          type="number"
          name="gaji"
          class="form-control"
          placeholder="Gaji Minimum"
          value="{{ request('gaji') }}"
        >
      </div>

      <div class="col-md-2">
        <select name="durasi" class="form-select">
          <option value="">Durasi (bulan)</option>
          <option value="3" {{ request('durasi') == '3' ? 'selected' : '' }}>3</option>
          <option value="6" {{ request('durasi') == '6' ? 'selected' : '' }}>6</option>
        </select>
      </div>

      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
          <i class="fas fa-search"></i> Cari
        </button>
      </div>
    </div>
  </form>

  {{-- Daftar hasil (akan di‐inject via AJAX) --}}
  <div class="row" id="rekomendasi-list">
    @include('rekomendasi.partials.list', ['lowongan' => $lowongan, 'mhs' => $mhs])
  </div>
</div>
@endsection

@push('js')
<script>
  // This script runs after jQuery is loaded (the layout defines @stack('js') after jQuery).
  $(function() {
    // 1) Intercept the filter form submit
    $('#filter-form').on('submit', function(e) {
      e.preventDefault();
      const url  = window.location.href.split('?')[0];
      const data = $(this).serialize();

      $.ajax({
        url: url,
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
          $('#rekomendasi-list').html(response.html);

          // Update URL so filters appear in address bar
          const newUrl = url + '?' + data;
          window.history.pushState(null, '', newUrl);
        },
        error: function(err) {
          console.error('Error loading recommendations:', err);
          // If AJAX fails, do a full reload:
          window.location.href = url + '?' + data;
        }
      });
    });

    // 2) Intercept “Lihat Detail” clicks inside the list
    $('#rekomendasi-list').on('click', '.detail-link', function(e) {
      e.preventDefault();

      // Base detail URL, e.g. "/mahasiswa/rekomendasi/4"
      let baseUrl = $(this).data('url');
      // Append ?ajax=1 so the controller returns JSON
      let ajaxUrl = baseUrl + '?ajax=1';

      $.ajax({
        url: ajaxUrl,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          // Swap out the entire <body> with the detail page HTML:
          $('body').html(response.html);

          // Clean the URL (remove ?ajax=1)
          window.history.pushState(null, '', baseUrl);
        },
        error: function(err) {
          console.error('Error loading detail via AJAX:', err);
          // Fallback: full navigation
          window.location.href = baseUrl;
        }
      });
    });
  });
</script>
@endpush
