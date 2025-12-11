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

    <div class="relative rounded-2xl bg-gradient-to-r from-slate-800 to-blue-900 p-6 shadow-lg mb-8 text-white flex flex-col md:flex-row items-center justify-between gap-4 border border-blue-800/50 overflow-hidden">
        
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 opacity-10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-cyan-400 opacity-10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>

        <div class="relative z-10 w-full md:w-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-medium text-blue-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Kaprodi Panel
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">
                Halo, Kaprodi {{ $jurusan->nama_jurusan ?? 'Jurusan' }}! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">
                Pantau kedisiplinan siswa di program studi Anda.
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
        
        <div class="stat-card hover-lift border-l-4 border-l-blue-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Populasi</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalSiswa }}</h3>
            <p class="text-xs text-slate-500">Siswa Jurusan Ini</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-rose-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bulan Ini</span>
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $pelanggaranBulanIni }}</h3>
            <p class="text-xs text-slate-500">Kasus Baru</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-rose-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-amber-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">On Progress</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $kasusAktif }}</h3>
            <p class="text-xs text-slate-500">Kasus Belum Selesai</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
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
            <form action="{{ route('dashboard.kaprodi') }}" method="GET">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="filter-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="filter-input">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Kelas (Jurusan Ini)</label>
                        <select name="kelas_id" class="filter-input cursor-pointer">
                            <option value="">- Semua Kelas -</option>
                            @foreach($kelasJurusan as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-slate-200">
                    <a href="{{ route('dashboard.kaprodi') }}" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-lg transition shadow-sm no-underline">
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
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Riwayat Pelanggaran (Jurusan Ini)
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="float-table text-left w-full">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3 pl-8">Tanggal</th>
                            <th class="px-6 py-3">Siswa</th>
                            <th class="px-6 py-3">Kelas</th>
                            <th class="px-6 py-3">Pelanggaran</th>
                            <th class="px-6 py-3 text-center">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($riwayatTerbaru as $r)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-3 pl-8 text-xs text-slate-500 font-mono">
                                {{ $r->tanggal_kejadian->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-sm font-bold text-slate-800">{{ $r->siswa->nama_siswa }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600 border border-slate-200 font-medium">
                                    {{ $r->siswa->kelas->nama_kelas }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-xs text-slate-700 font-medium">{{ $r->jenisPelanggaran->nama_pelanggaran }}</div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                    +{{ $r->jenisPelanggaran->poin }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400 text-xs">
                                <div class="flex flex-col items-center opacity-60">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Belum ada data pelanggaran di jurusan ini.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden h-full">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0">Statistik per Kelas</h3>
            </div>

            <div class="p-4 flex-1 flex items-center justify-center">
                <div class="relative w-full h-64">
                    <canvas id="kelasChart"></canvas>
                </div>
            </div>
            
            <div class="p-3 border-t border-slate-100 text-center text-[10px] text-slate-400">
                Grafik total pelanggaran berdasarkan filter tanggal.
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('kelasChart');
        if (ctx) {
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Pelanggaran',
                        data: data,
                        backgroundColor: '#3b82f6', // Blue-500
                        borderColor: '#2563eb',     // Blue-600
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' Kasus';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [2, 4], color: '#f1f5f9' },
                            ticks: { stepSize: 1, color: '#94a3b8' } 
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: {size: 10} }
                        }
                    }
                }
            });
        }
    });
</script>

<style>
    .page-wrap { background: #f8fafc; min-height: 100vh; padding: 1.5rem; font-family: 'Inter', sans-serif; }
    
    /* Hover Lift Effect */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }

    /* Stat Card */
    .stat-card { background: white; border-radius: 1rem; padding: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    
    /* Tabel Floating Clean */
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