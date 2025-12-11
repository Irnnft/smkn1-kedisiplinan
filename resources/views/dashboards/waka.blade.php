@extends('layouts.app')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { primary: '#4f46e5', slate: { 800: '#1e293b', 900: '#0f172a' } },
                screens: { 'xs': '375px' }
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<div class="page-wrap">

    <div class="relative rounded-2xl bg-gradient-to-r from-slate-800 to-blue-900 p-5 md:p-6 shadow-lg mb-8 overflow-hidden text-white flex flex-col md:flex-row items-center justify-between gap-4 border border-blue-800/50">
        
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 opacity-10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-cyan-400 opacity-10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>

        <div class="relative z-10 w-full md:w-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-medium text-blue-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                Kesiswaan Panel
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">
                Halo, Waka Kesiswaan! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">
                Selamat bekerja, monitoring kedisiplinan siswa siap dikelola.
            </p>
        </div>

        <div class="hidden xs:flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 shadow-inner min-w-[140px] relative z-10">
            <div class="bg-blue-500/20 p-2 rounded-lg text-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div>
                <span class="block text-2xl font-bold leading-none tracking-tight">{{ date('d') }}</span>
                <span class="block text-[10px] uppercase tracking-wider text-blue-200">{{ date('F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        
        <div class="stat-card group hover-lift relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">On Progress</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $kasusAktif ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Kasus Aktif</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card group hover-lift relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Action Needed</span>
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $butuhPersetujuan ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Menunggu ACC</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-rose-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card group hover-lift relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terfilter</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $pelanggaranFiltered ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Total Poin</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card group hover-lift relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Populasi</span>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalSiswa ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Siswa Aktif</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 overflow-hidden transition-all hover:shadow-md">
        
        <div class="px-6 py-4 bg-white border-b border-slate-100 flex justify-between items-center cursor-pointer hover:bg-slate-50" onclick="document.getElementById('filterBody').classList.toggle('hidden')">
            <div class="flex items-center gap-3">
                <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                </span>
                <h3 class="text-sm font-bold text-slate-700 m-0">Filter Data Lanjutan</h3>
            </div>
            
        </div>
        
        <div id="filterBody" class="hidden p-6 bg-slate-50/50">
            <form action="{{ route('dashboard.admin') }}" method="GET">
                <input type="hidden" name="chart_type" value="{{ $chartType }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="filter-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="filter-input">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Tingkat</label>
                        <select name="angkatan" class="filter-input cursor-pointer">
                            <option value="">- Semua -</option>
                            <option value="X" {{ request('angkatan') == 'X' ? 'selected' : '' }}>Kelas X</option>
                            <option value="XI" {{ request('angkatan') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                            <option value="XII" {{ request('angkatan') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Jurusan</label>
                        <select name="jurusan_id" class="filter-input cursor-pointer">
                            <option value="">- Semua -</option>
                            @if(isset($allJurusan))
                                @foreach($allJurusan as $j)
                                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Kelas</label>
                        <select name="kelas_id" class="filter-input cursor-pointer">
                            <option value="">- Semua -</option>
                            @if(isset($allKelas))
                                @foreach($allKelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-slate-200">
                    <a href="{{ route('dashboard.admin') }}" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-lg transition shadow-sm no-underline">
                        Reset
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col h-full overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0">Aktivitas Terbaru</h3>
                <a href="{{ route('riwayat.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 no-underline bg-blue-50 px-2 py-1 rounded">Lihat Semua →</a>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="float-table text-left w-full">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3 pl-8">Waktu</th>
                            <th class="px-6 py-3">Siswa</th>
                            <th class="px-6 py-3">Kasus</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right pr-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($daftarKasus as $kasus)
                        <tr class="float-row group">
                            <td class="px-6 py-3 pl-8">
                                <div class="text-xs font-bold text-slate-700">{{ $kasus->created_at->format('d M') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $kasus->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-xs font-bold text-slate-800">{{ $kasus->siswa->nama_siswa }}</div>
                                <div class="text-[10px] text-slate-500">{{ $kasus->siswa->kelas->nama_kelas ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-xs text-slate-600 truncate max-w-[150px]" title="{{ $kasus->pemicu }}">
                                    {{ Str::limit($kasus->pemicu, 25) }}
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                @php
                                    $statusClass = match($kasus->status) {
                                        'Baru' => 'bg-amber-100 text-amber-700',
                                        'Menunggu Persetujuan' => 'bg-rose-100 text-rose-700 animate-pulse',
                                        'Selesai' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-blue-100 text-blue-700'
                                    };
                                @endphp
                                <span class="text-[10px] font-bold px-2 py-1 rounded {{ $statusClass }}">
                                    {{ $kasus->status == 'Menunggu Persetujuan' ? 'Butuh ACC' : $kasus->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right pr-8">
                                <a href="{{ route('kasus.edit', $kasus->id) }}" class="text-slate-400 hover:text-blue-600 transition p-1 rounded hover:bg-slate-100 inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400 text-xs">Belum ada data aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-slate-700 m-0">Statistik Pelanggaran</h3>
                <select class="text-[10px] bg-slate-50 border border-slate-200 rounded px-2 py-1 text-slate-600 cursor-pointer focus:outline-none focus:border-blue-500" onchange="changeChartType(this.value)">
                    <option value="doughnut" {{ $chartType == 'doughnut' ? 'selected' : '' }}>Donut Chart</option>
                    <option value="bar" {{ $chartType == 'bar' ? 'selected' : '' }}>Bar Chart</option>
                </select>
            </div>
            <div class="flex-1 flex items-center justify-center relative" style="min-height: 200px;">
                <canvas id="wakaChart"></canvas>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
<script>
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
            const type = "{{ $chartType }}";
            const colors = ['#f43f5e', '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6']; 

            new Chart(ctx, {
                type: type, 
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah',
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, font: {size: 10} } } 
                    },
                    cutout: type === 'doughnut' ? '75%' : 0,
                    scales: type === 'bar' ? {
                        y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                        x: { grid: { display: false } }
                    } : {}
                }
            });
        }
    });
</script>

<style>
    .page-wrap { background: #f8fafc; min-height: 100vh; padding: 1.5rem; font-family: 'Inter', sans-serif; }
    
    /* Hover Lift Effect (Konsisten) */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }

    /* Stat Card (4 Kolom Rapi) */
    .stat-card { background: white; border-radius: 1rem; padding: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    
    /* Tabel Floating */
    .float-table { border-collapse: separate; border-spacing: 0 8px; width: 100%; }
    .float-row { background: white; transition: 0.2s; border: 1px solid #f1f5f9; }
    .float-row td:first-child { border-radius: 8px 0 0 8px; border-left: 1px solid #f1f5f9; }
    .float-row td:last-child { border-radius: 0 8px 8px 0; border-right: 1px solid #f1f5f9; }
    .float-row:hover { transform: translateY(-2px); border-color: #bfdbfe; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }

    /* Input Filter */
    .filter-input { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem; font-size: 0.875rem; width: 100%; color: #334155; }
    .filter-input:focus { outline: none; border-color: #3b82f6; ring: 2px solid rgba(59,130,246,0.1); }
</style>

@endsection