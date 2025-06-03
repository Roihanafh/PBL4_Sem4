@extends('layouts.template_mhs')

@push('css')
<style>
  /* ===== Root Theme Colors ===== */
  :root {
    --primary: #6366f1; /* Indigo 500 */
    --secondary: #64748b; /* Slate 500 */
    --success: #10b981; /* Emerald 500 */
    --warning: #f59e0b; /* Amber 500 */
    --danger:  #ef4444; /* Red 500 */
    --bg-light: #f6f8fc;
  }

  /* ===== Base ===== */
  body {
    background: var(--bg-light);
  }

  /* ===== Utility ===== */
  .card-stat {
    transition: transform .25s ease, box-shadow .25s ease;
  }
  .card-stat:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 10px 24px rgba(0,0,0,.08);
  }
  .fade-slide { /* existing rules keep */
    opacity: 0;
    transform: translateY(24px);
  }
  .fade-slide.show {
    opacity: 1;
    transform: translateY(0);
    transition: opacity .8s ease-out, transform .8s ease-out;
  }

  /* ===== Badges ===== */
  .deadline-badge {
    background: var(--danger);
    color: #fff;
    font-size: .75rem;
    padding: .35rem .55rem;
    font-weight: 600;
    border-radius: .35rem;
  }
  /* ===== Hero Banner Background ===== */
  .hero-bg { z-index: 0; border-radius: inherit; }
  .hero-banner > * { position: relative; z-index: 1; }
</style>
@endpush

@section('content')
<div class="container-fluid mt-5 fade-slide">
  <!-- ========= 1. Hero ========== -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="bg-white rounded-4 shadow-sm p-4 position-relative overflow-hidden">
        <div class="hero-bg position-absolute" style="inset:0; background: radial-gradient(circle at 0% 50%, rgba(99,102,241,.25) 0%, rgba(99,102,241,0) 60%), linear-gradient(90deg, rgba(99,102,241,.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <h3 class="fw-bold mb-1 position-relative">
          Selamat Datang, <span class="text-primary">{{ Auth::user()->name }}</span> 👋
        </h3>
        <p class="mb-0 text-secondary position-relative">Ringkasan aktivitas &amp; rekomendasi magang Anda.</p>
      </div>
    </div>
  </div>

  <!-- ========= 2. Metrics ========= -->
  <div class="row g-4 mb-5">
    <!-- Total Recommendations -->
    <div class="col-12 col-md-4">
      <div class="card card-stat shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="fs-2 text-primary"><i class="fas fa-lightbulb"></i></span>
          <div>
            <span class="small text-secondary d-block">Total Recommendations</span>
            <h2 class="fw-semibold mb-0 counter" data-count="{{ $totalRecommendations }}">0</h2>
          </div>
        </div>
      </div>
    </div>
    <!-- In-Progress Applications -->
    <div class="col-12 col-md-4">
      <div class="card card-stat shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="fs-2 text-warning"><i class="fas fa-spinner"></i></span>
          <div>
            <span class="small text-secondary d-block">In Progress</span>
            <h2 class="fw-semibold mb-0 counter" data-count="{{ $inProgressApplications }}">0</h2>
          </div>
        </div>
      </div>
    </div>
    <!-- Placeholder Metric -->
    <div class="col-12 col-md-4">
      <div class="card card-stat shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="fs-2 text-success"><i class="fas fa-clipboard-list"></i></span>
          <div>
            <span class="small text-secondary d-block">Completed</span>
            <h2 class="fw-semibold mb-0 counter" data-count="{{ $inProgressApplications == 0 ? 0 : ($inProgressApplications - 1) }}">0</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========= 3. Deadlines & Recent ========= -->
  <div class="row g-4 fade-slide">
    <!-- Upcoming Deadlines -->
    <div class="col-12 col-lg-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 fw-semibold">
          <i class="fas fa-calendar-alt text-danger me-2"></i>Upcoming Deadlines (<span class="text-danger">≤ 7 hari</span>)
        </div>
        <div class="card-body p-0">
          @if($upcomingDeadlines->isEmpty())
            <div class="text-center text-secondary py-5">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <p class="mb-0">Tidak ada deadline dalam 7 hari ke depan.</p>
            </div>
          @else
            <ul class="list-group list-group-flush">
              @foreach($upcomingDeadlines as $low)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="pe-2">
                    <div class="fw-semibold">{{ $low->judul }}</div>
                    <small class="text-secondary">{{ $low->perusahaan->nama }}</small>
                  </div>
                  <span class="badge deadline-badge">
                    {{ \Carbon\Carbon::parse($low->deadline_lowongan)->format('d M') }}
                  </span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>

    <!-- Recent Applications -->
    <div class="col-12 col-lg-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 fw-semibold">
          <i class="fas fa-briefcase text-info me-2"></i>Recent Applications
        </div>
        <div class="card-body p-0">
          @if($recentApplications->isEmpty())
            <div class="text-center text-secondary py-5">
              <i class="fas fa-folder-open fa-2x mb-2"></i>
              <p class="mb-0">Belum ada aplikasi.</p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">#</th>
                    <th>Judul Lowongan</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentApplications as $idx => $app)
                    <tr class="border-0">
                      <td class="ps-4">{{ $idx + 1 }}</td>
                      <td class="fw-semibold">{{ $app->lowongan->judul }}</td>
                      <td>
                        @switch($app->status)
                          @case('submitted')
                            <span class="badge bg-warning bg-opacity-25 text-warning">Submitted</span>
                          @break
                          @case('under_review')
                            <span class="badge bg-info bg-opacity-25 text-info">Under Review</span>
                          @break
                          @case('diterima')
                            <span class="badge bg-success bg-opacity-25 text-success">Diterima</span>
                          @break
                          @case('ditolak')
                            <span class="badge bg-danger bg-opacity-25 text-danger">Ditolak</span>
                          @break
                          @default
                            <span class="badge bg-secondary bg-opacity-25 text-secondary">{{ ucfirst($app->status) }}</span>
                        @endswitch
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- ========= 4. Modal ========= -->
  <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content"></div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
  // ===== Counter Animation =====
  const animateCounter = (el) => {
    const target = +el.dataset.count;
    const increment = Math.ceil(target / 60); // ~1s
    let current = 0;
    const step = () => {
      current += increment;
      if (current >= target) {
        el.textContent = target;
      } else {
        el.textContent = current;
        requestAnimationFrame(step);
      }
    };
    requestAnimationFrame(step);
  };

  // ===== IntersectionObserver for fade-slide =====
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        entry.target.querySelectorAll('.counter').forEach(animateCounter);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.fade-slide').forEach(el => observer.observe(el));

  // ===== CSRF Setup =====
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // ===== Load detail via AJAX =====
  $(document).on('click', '.detail-link', function(e) {
    e.preventDefault();
    const url = $(this).data('url');
    $('#myModal .modal-content').load(url, () => $('#myModal').modal('show'));
  });
</script>
@endpush
