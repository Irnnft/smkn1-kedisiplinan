<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin / Waka</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* GRID KARTU STATISTIK */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stat-title { font-size: 0.9rem; color: #777; margin-bottom: 5px; }
        .stat-value { font-size: 2rem; font-weight: bold; color: #333; }
        .text-danger { color: #c62828; }
        
        /* LAYOUT UTAMA */
        .main-content { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        th { background-color: #f8f9fa; }
        
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; color: white; }
        .bg-baru { background-color: #ff9800; }
        .bg-approval { background-color: #e53e3e; }
        .bg-selesai { background-color: #28a745; }

        .btn-logout { background-color: #333; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

    <div class="card" style="margin-bottom: 20px; padding: 15px;">
        <h4 style="margin-top: 0;">🔧 Manajemen Data Induk</h4>
        <div style="display: flex; gap: 10px;">
            
            <a href="{{ route('siswa.index') }}" class="btn-logout" style="background: #007bff; text-decoration:none;">
                🎓 Data Siswa
            </a>

           <a href="{{ route('users.index') }}" class="btn-logout" style="background: #17a2b8; text-decoration:none;">
                👥 Data Pengguna
            </a>

            <a href="{{ route('jenis-pelanggaran.index') }}" class="btn-logout" style="background: #dc3545; text-decoration:none;">
                ⚠️ Aturan & Poin
            </a>

            <div style="display: flex; gap: 10px;">
            <a href="{{ route('pelanggaran.create') }}" class="btn-logout" style="background: #6f42c1; text-decoration:none;">
                📝 Catat Pelanggaran
            </a>
            
        </div>

            <button class="btn-logout" style="background: #6c757d; cursor: not-allowed;" disabled>🏫 Data Kelas</button>
        </div>
    </div>

<div class="container">
    <div class="header">
        <div>
            <h1>Dashboard Waka Kesiswaan</h1>
            <small>Selamat Datang, {{ Auth::user()->nama }}</small>
        </div>
        <form action="{{ route('logout') }}" method="POST"> @csrf <button class="btn-logout">Logout</button> </form>
    </div>

    <div class="card" style="margin-bottom: 20px; padding: 15px; background: #e3f2fd;">
        <form action="{{ route('dashboard.admin') }}" method="GET" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
            
            <div>
                <label style="font-size: 0.8rem; font-weight: bold;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" style="padding: 5px;">
            </div>

            <div>
                <label style="font-size: 0.8rem; font-weight: bold;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" style="padding: 5px;">
            </div>

            <div>
                <label style="font-size: 0.8rem; font-weight: bold;">Jurusan</label>
                <select name="jurusan_id" style="padding: 7px; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="">-- Semua Jurusan --</option>
                    @foreach($allJurusan as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 0.8rem; font-weight: bold;">Kelas</label>
                <select name="kelas_id" style="padding: 7px; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($allKelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" style="padding: 7px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                🔍 Filter Data
            </button>
            
            <a href="{{ route('dashboard.admin') }}" style="padding: 7px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">
                Reset
            </a>
        </form>
    </div>
    <div class="stats-grid">
        <div class="card">
            <div class="stat-title">Pelanggaran (Terfilter)</div> 
            <div class="stat-value">{{ $pelanggaranFiltered }}</div>
        </div>

    <div class="main-content">
        <div class="card">
            <h3>🚨 Aktivitas Kasus Terbaru</h3>
            <table>
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
                    @foreach($daftarKasus as $kasus)
                    <tr>
                        <td>{{ $kasus->created_at->format('d/m/y') }}</td>
                        <td>
                            <strong>{{ $kasus->siswa->nama_siswa }}</strong><br>
                            <small style="color:#777">{{ $kasus->siswa->kelas->nama_kelas }}</small>
                        </td>
                        <td>{{ $kasus->pemicu }}</td>
                        <td>
                            @if($kasus->status == 'Baru')
                                <span class="badge bg-baru">Baru</span>
                            @elseif($kasus->status == 'Menunggu Persetujuan')
                                <span class="badge bg-approval">Butuh Acc</span>
                            @else
                                <span class="badge bg-selesai">{{ $kasus->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('kasus.edit', $kasus->id) }}" style="color:blue">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>📊 Tren Bulan Ini</h3>
            <canvas id="myChart"></canvas>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('myChart');
    
    // Data dari Controller Laravel
    const labels = {!! json_encode($chartLabels) !!};
    const data = {!! json_encode($chartData) !!};

    new Chart(ctx, {
        type: 'doughnut', // Bisa diganti 'bar' atau 'pie'
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pelanggaran',
                data: data,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>