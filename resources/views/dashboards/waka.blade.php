@extends('layouts.app')

@section('title', 'Dashboard Waka Kesiswaan')

@section('content')

    <!-- FILTER DATA (CARD) -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data Lanjutan</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="background-color: #f4f6f9;">
            <form action="{{ route('dashboard.admin') }}" method="GET">
                <!-- Input hidden untuk menjaga jenis chart saat filter disubmit -->
                <input type="hidden" name="chart_type" value="{{ $chartType }}">

                <div class="row">
                    <!-- 1. TANGGAL -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                        </div>
                    </div>

                    <!-- 2. TINGKAT (BARU) -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tingkat</label>
                            <select name="angkatan" class="form-control">
                                <option value="">Semua</option>
                                <option value="X" {{ request('angkatan') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                <option value="XI" {{ request('angkatan') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                <option value="XII" {{ request('angkatan') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. JURUSAN -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Jurusan</label>
                            <select name="jurusan_id" class="form-control">
                                <option value="">Semua</option>
                                @foreach($allJurusan as $j)
                                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                        {{ $j->nama_jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 4. KELAS -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas_id" class="form-control">
                                <option value="">Semua</option>
                                @foreach($allKelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- STATISTIK RINGKAS (INFO BOX) -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pelanggaran</span>
                    <span class="info-box-number">{{ $pelanggaranFiltered }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Siswa</span>
                    <span class="info-box-number">{{ $totalSiswa }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kasus Aktif</span>
                    <span class="info-box-number">{{ $kasusAktif }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-signature"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Butuh ACC</span>
                    <span class="info-box-number">{{ $butuhPersetujuan }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KONTEN UTAMA (TABEL & GRAFIK) -->
    <div class="row">
        <!-- TABEL KASUS -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Kasus Terbaru</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0 table-hover">
                            <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Masalah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($daftarKasus as $kasus)
                            <tr>
                                <td>{{ $kasus->created_at->format('d/m/y') }}</td>
                                <td>
                                    <b>{{ $kasus->siswa->nama_siswa }}</b><br>
                                    <small class="text-muted">{{ $kasus->siswa->kelas->nama_kelas }}</small>
                                </td>
                                <td>
                                    <span title="{{ $kasus->pemicu }}">{{ Str::limit($kasus->pemicu, 25) }}</span>
                                </td>
                                <td>
                                    @if($kasus->status == 'Baru')
                                        <span class="badge badge-warning">Baru</span>
                                    @elseif($kasus->status == 'Menunggu Persetujuan')
                                        <span class="badge badge-danger">Butuh ACC</span>
                                    @elseif($kasus->status == 'Disetujui')
                                        <span class="badge badge-primary">Disetujui</span>
                                    @else
                                        <span class="badge badge-success">{{ $kasus->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kasus.edit', $kasus->id) }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">Tidak ada kasus aktif pada filter ini.</td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('riwayat.index') }}" class="btn btn-sm btn-secondary float-right">Lihat Semua Riwayat</a>
                </div>
            </div>
        </div>

        <!-- GRAFIK (DENGAN OPSI GANTI TIPE) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tren Pelanggaran</h3>
                    <div class="card-tools">
                        <!-- SELECTOR TIPE GRAFIK -->
                        <select id="chartTypeSelector" class="custom-select custom-select-sm" onchange="changeChartType(this.value)">
                            <option value="doughnut" {{ $chartType == 'doughnut' ? 'selected' : '' }}>Lingkaran (Donut)</option>
                            <option value="pie" {{ $chartType == 'pie' ? 'selected' : '' }}>Pie Chart</option>
                            <option value="bar" {{ $chartType == 'bar' ? 'selected' : '' }}>Batang (Bar)</option>
                            <option value="line" {{ $chartType == 'line' ? 'selected' : '' }}>Garis (Line)</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px; width:100%">
                        <canvas id="wakaChart"></canvas>
                    </div>
                </div>
                <div class="card-footer text-center text-muted small">
                    Grafik menampilkan 5 jenis pelanggaran tertinggi sesuai filter.
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT CHART -->
    <script>
        // Fungsi Ganti Chart (Reload Halaman dengan parameter chart_type)
        function changeChartType(type) {
            let url = new URL(window.location.href);
            url.searchParams.set('chart_type', type);
            window.location.href = url.toString();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('wakaChart');
            
            if (ctx) {
                const labels = {!! json_encode($chartLabels) !!};
                const data = {!! json_encode($chartData) !!};
                const type = "{{ $chartType }}"; // Ambil dari controller

                // Cek jika data kosong
                if (labels.length === 0) {
                    const context = ctx.getContext('2d');
                    context.font = "14px Arial";
                    context.textAlign = "center";
                    context.fillText("Tidak ada data pelanggaran", ctx.width/2, ctx.height/2);
                    context.fillText("untuk filter yang dipilih.", ctx.width/2, ctx.height/2 + 20);
                    return;
                }

                new Chart(ctx, {
                    type: type, 
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Pelanggaran',
                            data: data,
                            backgroundColor: [
                                '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { 
                                display: (type === 'bar' || type === 'line') ? false : true,
                                position: 'bottom' 
                            }
                        },
                        scales: {
                            // Tampilkan sumbu Y hanya jika tipe Bar/Line
                            y: {
                                display: (type === 'bar' || type === 'line'),
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            },
                            x: {
                                display: (type === 'bar' || type === 'line')
                            }
                        }
                    }
                });
            }
        });
    </script>

@endsection