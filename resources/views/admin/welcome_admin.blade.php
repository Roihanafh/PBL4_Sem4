@extends('layouts.template')

@section('content')
    <div class="card">
          <!-- ========= 1. Hero Banner ========== -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-4 position-relative overflow-hidden">
                    <div class="hero-bg position-absolute" style="inset:0;
                        background: radial-gradient(circle at 0% 50%, rgba(99,102,241,.25) 0%, rgba(99,102,241,0) 60%),
                                    linear-gradient(90deg, rgba(99,102,241,.15) 0%, rgba(255,255,255,0) 70%);">
                    </div>
                    <h3 class="fw-bold mb-1 position-relative">
                        Selamat Datang, <span class="text-primary">{{ Auth::user()->name }}</span> 👋
                    </h3>
                    <p class="mb-0 text-secondary position-relative">
                        Dashboard Monitoring & Statistik Magang
                    </p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Statistic Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-stats card-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="numbers">
                                        <p class="card-category">Total Mahasiswa</p>
                                        <h4 class="card-title">{{ $totalMahasiswa }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats card-success">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="numbers">
                                        <p class="card-category">Mahasiswa Magang</p>
                                        <h4 class="card-title">{{ $mahasiswaMagang }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats card-info">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="numbers">
                                        <p class="card-category">Dosen Pembimbing</p>
                                        <h4 class="card-title">{{ $totalDosenPembimbing }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats card-warning">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="numbers">
                                        <p class="card-category">Rasio Dosen:Mhs</p>
                                        <h4 class="card-title">1:{{ $rasioDosenMhs }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mt-4">
                <!-- Bidang Peminatan Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Top Bidang Peminatan</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="bidangChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Prodi Distribution Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Distribusi Mahasiswa Magang per Prodi</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="prodiChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Charts Row -->
            <div class="row mt-4">
                <!-- Rekomendasi Rating -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Evaluasi Sistem Rekomendasi</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-4 text-center">
                                    <h1 class="display-4">{{ number_format($ratingRekomendasi, 1) }}/5</h1>
                                    <p class="text-muted">Rating Rata-rata</p>
                                </div>
                                <div>
                                    <p>Total Feedback: {{ $totalFeedback }}</p>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ ($ratingRekomendasi / 5) * 100 }}%"
                                            aria-valuenow="{{ $ratingRekomendasi }}" aria-valuemin="0" aria-valuemax="5">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tren Pendaftaran -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tren Pendaftaran 6 Bulan Terakhir</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="trenChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            // Bidang Peminatan Chart
            var bidangCtx = document.getElementById('bidangChart').getContext('2d');
            var bidangChart = new Chart(bidangCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($bidangPeminatan->pluck('bidang')) !!},
                    datasets: [{
                        label: 'Jumlah Dosen Pembimbing',
                        data: {!! json_encode($bidangPeminatan->pluck('dosen_count')) !!},
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Prodi Distribution Chart
            var prodiCtx = document.getElementById('prodiChart').getContext('2d');
            var prodiChart = new Chart(prodiCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($statistikProdi->pluck('nama_prodi')) !!},
                    datasets: [{
                        data: {!! json_encode($statistikProdi->pluck('mahasiswa_count')) !!},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });

            // Tren Pendaftaran Chart
            var trenCtx = document.getElementById('trenChart').getContext('2d');
            var trenChart = new Chart(trenCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trenPendaftaran->map(function ($item) {
        return Carbon\Carbon::create($item->year, $item->month)->format('M Y');
    })) !!},
                    datasets: [{
                        label: 'Jumlah Pendaftaran',
                        data: {!! json_encode($trenPendaftaran->pluck('total')) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush