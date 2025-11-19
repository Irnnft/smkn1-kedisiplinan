@extends('layouts.app')

@section('title', 'Dashboard Kaprodi - ' . $jurusan->nama_jurusan)

@section('content')

<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Siswa (Jurusan)</span>
                <span class="info-box-number">{{ $totalSiswa }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pelanggaran (Bulan Ini)</span>
                <span class="info-box-number">{{ $pelanggaranBulanIni }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tasks"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Kasus Aktif</span>
                <span class="info-box-number">{{ $kasusAktif }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        
        <div class="card card-primary card-outline collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Filter Lanjutan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.kaprodi') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="kelas_id" class="form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasJurusan as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Terapkan Filter</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📊 Perbandingan Pelanggaran Antar Kelas</h3>
            </div>
            <div class="card-body">
                <canvas id="chartKelas" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📝 Pelanggaran Terkini</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @forelse($riwayatTerbaru as $r)
                    <li class="item">
                        <div class="product-img">
                            <span class="badge badge-danger float-right">+{{ $r->jenisPelanggaran->poin }}</span>
                        </div>
                        <div class="product-info ml-2">
                            <a href="javascript:void(0)" class="product-title">{{ $r->siswa->nama_siswa }}</a>
                            <span class="product-description">
                                {{ $r->jenisPelanggaran->nama_pelanggaran }}
                                <br><small class="text-muted">{{ $r->siswa->kelas->nama_kelas }} • {{ $r->tanggal_kejadian->diffForHumans() }}</small>
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="item text-center p-3 text-muted">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('chartKelas');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Jumlah Pelanggaran',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: '#6f42c1',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    });
</script>

@endsection