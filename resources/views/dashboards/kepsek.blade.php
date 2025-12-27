@extends('layouts.app')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<script>
    function toggleFilterKepsek() {
        const content = document.getElementById('filterContentKepsek');
        content.classList.toggle('hidden');
    }
</script>

<div class="page-wrap">

    <div class="relative rounded-2xl bg-gradient-to-r from-slate-800 to-blue-900 p-6 shadow-lg mb-8 text-white flex flex-col md:flex-row items-center justify-between gap-4 border border-blue-800/50 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 opacity-10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-cyan-400 opacity-10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>
        <div class="relative z-10 w-full md:w-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-medium text-blue-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Kepala Sekolah Panel
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">Halo, {{ auth()->user()->username }}! 👋</h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">Dashboard khusus untuk monitoring dan persetujuan kasus surat panggilan seluruh sekolah.</p>
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

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-colors" onclick="toggleFilterKepsek()">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider m-0 flex items-center gap-2">
                <span class="p-1.5 bg-blue-50 border border-blue-100 rounded-lg text-blue-600"><i class="fas fa-calendar-alt"></i></span>
                Filter Periode Laporan
            </h3>
            <i class="fas fa-chevron-down text-slate-400"></i>
        </div>
        <div id="filterContentKepsek" class="p-6 transition-all">
            <form action="{{ route('dashboard.kepsek') }}" method="GET" class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none transition-all">
                    </div>
                    <div class="md:col-span-1 flex gap-3">
                        <button type="submit" class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase rounded-xl transition-all h-[42px] border-none cursor-pointer">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Populasi</span>
                <div class="text-emerald-500 bg-emerald-50 p-2 rounded-lg group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalSiswa }}</h3>
            <p class="text-xs text-slate-500">Total Siswa Sekolah</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>
        <div class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kasus Surat</span>
                <div class="text-rose-500 bg-rose-50 p-2 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalKasus }}</h3>
            <p class="text-xs text-slate-500">Yang Melibatkan Saya</p>
        </div>
        <div class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu</span>
                <div class="text-amber-500 bg-amber-50 p-2 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalKasusMenunggu }}</h3>
            <p class="text-xs text-slate-500">Perlu Persetujuan</p>
        </div>
        <div class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pelanggaran</span>
                <div class="text-blue-500 bg-blue-50 p-2 rounded-lg group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalPelanggaran }}</h3>
            <p class="text-xs text-slate-500">Total Periode Ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        
        <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0">📋 Kasus Terbaru</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left table-fixed">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="w-1/3 px-4 py-3">Siswa</th>
                            <th class="w-1/2 px-4 py-3">Pemicu</th>
                            <th class="w-24 px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($kasusBaru as $kasus)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-xs font-bold text-slate-800 truncate">{{ $kasus->siswa->nama_siswa }}</div>
                                <div class="text-[10px] text-slate-500">{{ $kasus->siswa->kelas->nama_kelas ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-[11px] text-slate-600 leading-snug line-clamp-2 m-0 italic">
                                    {{ $kasus->pemicu }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right text-xs">
                                <a href="{{ route('kasus.edit', $kasus->id) }}" class="text-blue-600 font-bold hover:underline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-10 text-slate-400 text-xs text-italic">Kosong</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase mb-4">📊 Pelanggaran Populer</h3>
                <div class="h-40"><canvas id="chartPelanggaran"></canvas></div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase mb-4">🏢 Per Jurusan</h3>
                <div class="h-40"><canvas id="chartJurusan"></canvas></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/30">
            <h3 class="text-sm font-bold text-slate-700 m-0">📈 Trend Pelanggaran (6 Bulan Terakhir)</h3>
        </div>
        <div class="p-6">
            <div class="relative w-full h-[180px]"> <canvas id="chartTrend"></canvas>
            </div>
        </div>
    </div>

</div>

<script>
// Chart 1: Pelanggaran Populer
new Chart(document.getElementById('chartPelanggaran'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            data: {!! json_encode($chartData) !!},
            backgroundColor: '#3b82f6',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { 
            y: { beginAtZero: true, ticks: { display: false }, grid: { display: false } },
            x: { ticks: { font: { size: 9 } } }
        }
    }
});

// Chart 2: Per Jurusan
new Chart(document.getElementById('chartJurusan'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartJurusanLabels) !!},
        datasets: [{
            data: {!! json_encode($chartJurusanData) !!},
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#a855f7'],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'right', labels: { font: { size: 9 }, boxWidth: 10 } } }
    }
});

// Chart 3: Trend (SLIM SETTINGS)
new Chart(document.getElementById('chartTrend'), {
    type: 'line',
    data: {
        labels: {!! json_encode($chartTrendLabels) !!},
        datasets: [{
            label: 'Pelanggaran',
            data: {!! json_encode($chartTrendData) !!},
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // WAJIB false agar ikut tinggi 180px
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { font: { size: 10 } } },
            x: { ticks: { font: { size: 10 } } }
        }
    }
});
</script>

<style>
    .page-wrap { background: #f8fafc; min-height: 100vh; padding: 1.5rem; font-family: 'Inter', sans-serif; }
    .hover-lift { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-3px); }
    /* Mengatasi pemicu agar tidak berantakan tapi tetap kebaca */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>

@endsection