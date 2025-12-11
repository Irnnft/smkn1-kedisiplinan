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

<style>
    .dashboard-theme {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
    }
    .dashboard-theme a { text-decoration: none !important; }
    
    /* Efek Hover Gacor (Lift + Shadow) */
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    /* Tabel Bersih */
    .clean-table { border-collapse: separate; border-spacing: 0; width: 100%; }
    .clean-table th { background-color: #f8fafc; font-weight: 700; color: #64748b; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .clean-table td { padding: 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .clean-table tr:last-child td { border-bottom: none; }
</style>

<div class="dashboard-theme p-3 md:p-6">

    <div class="relative rounded-2xl bg-gradient-to-r from-slate-800 to-blue-900 p-5 md:p-6 shadow-lg mb-8 overflow-hidden text-white flex flex-col md:flex-row items-center justify-between gap-4 border border-blue-800/50">
        
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 opacity-10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-cyan-400 opacity-10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>

        <div class="relative z-10 w-full md:w-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-medium text-blue-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Executive Panel
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">
                Selamat Datang, Kepala Sekolah! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">
                Ringkasan eksekutif kedisiplinan & persetujuan dokumen.
            </p>
        </div>

        <div class="hidden xs:flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 shadow-inner min-w-[140px]">
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
        
        <a href="{{ route('siswa.index') }}" class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden block">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Populasi</span>
                <div class="text-blue-500 bg-blue-50 p-2 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalSiswa }}</h3>
            <p class="text-xs text-slate-500">Total Siswa</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </a>

        <div class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bulan Ini</span>
                <div class="text-amber-500 bg-amber-50 p-2 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" x2="12" y1="18" y2="12"/><line x1="9" x2="15" y1="15" y2="15"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $pelanggaranBulanIni }}</h3>
            <p class="text-xs text-slate-500">Kasus Baru</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Semester Ini</span>
                <div class="text-slate-500 bg-slate-100 p-2 rounded-lg group-hover:bg-slate-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $pelanggaranSemesterIni }}</h3>
            <p class="text-xs text-slate-500">Total Akumulasi</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-slate-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <a href="#approval-section" class="group hover-lift bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative overflow-hidden block {{ $listPersetujuan->count() > 0 ? 'border-l-4 border-l-rose-500' : 'border-l-4 border-l-emerald-500' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Approval</span>
                <div class="p-2 rounded-lg transition-colors duration-300 {{ $listPersetujuan->count() > 0 ? 'text-rose-500 bg-rose-50 group-hover:bg-rose-500 group-hover:text-white' : 'text-emerald-500 bg-emerald-50 group-hover:bg-emerald-500 group-hover:text-white' }}">
                    @if($listPersetujuan->count() > 0)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @endif
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $listPersetujuan->count() }}</h3>
            <p class="text-xs text-slate-500">Menunggu Tanda Tangan</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-full">
            <h3 class="text-base font-bold text-slate-700 mb-1">Tren Pelanggaran 7 Hari</h3>
            <p class="text-xs text-slate-400 mb-6">Monitoring harian intensitas kasus.</p>
            
            <div class="relative h-64 w-full flex-1">
                <canvas id="trend-chart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-full">
            <h3 class="text-base font-bold text-slate-700 mb-1">Top 5 Kasus</h3>
            <p class="text-xs text-slate-400 mb-6">Jenis pelanggaran paling sering terjadi.</p>

            <div class="flex flex-col gap-3">
                @forelse($topViolations as $index => $v)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full {{ $index == 0 ? 'bg-rose-100 text-rose-600' : 'bg-white text-slate-500 border border-slate-200' }} text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm font-medium text-slate-700 truncate max-w-[140px]" title="{{ $v->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}">
                                {{ $v->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}
                            </span>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded bg-white border border-slate-200 text-slate-600">
                            {{ $v->jumlah }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-400 py-4">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div id="approval-section" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-700 m-0 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Menunggu Persetujuan
            </h3>
            @if($listPersetujuan->count() > 0)
                <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                    {{ $listPersetujuan->count() }} Dokumen
                </span>
            @else
                <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-xs font-bold">
                    Selesai
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="clean-table text-left">
                <thead>
                    <tr>
                        <th class="pl-6">Tanggal</th>
                        <th>Siswa</th>
                        <th>Pelanggaran</th>
                        <th>Rekomendasi</th>
                        <th class="text-center pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($listPersetujuan as $kasus)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="pl-6">
                            <div class="text-sm font-bold text-slate-700">{{ $kasus->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $kasus->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="text-sm font-bold text-blue-600">{{ $kasus->siswa->nama_siswa }}</div>
                            <div class="text-xs text-slate-500">{{ $kasus->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="text-sm text-slate-600 truncate max-w-[200px]" title="{{ $kasus->pemicu }}">
                                {{ Str::limit($kasus->pemicu, 30) }}
                            </div>
                        </td>
                        <td>
                            <div class="bg-slate-100 p-2 rounded text-xs font-medium text-slate-700 border-l-4 border-slate-400 inline-block">
                                {{ $kasus->sanksi_deskripsi ?? 'Menunggu...' }}
                            </div>
                        </td>
                        <td class="text-center pr-6">
                            <a href="{{ route('kasus.edit', $kasus->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition shadow-sm no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Tinjau
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-400 text-sm">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2 text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Tidak ada dokumen yang perlu ditinjau.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Controller
        const trendLabels = {!! $trendData->pluck('tanggal')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toJson() !!};
        const trendValues = {!! $trendData->pluck('total')->toJson() !!};

        if (trendLabels.length > 0) {
            const ctx = document.getElementById('trend-chart').getContext('2d');
            
            // Gradient Modern
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue start
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // Transparent end

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Pelanggaran',
                        data: trendValues,
                        borderColor: '#3b82f6',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
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
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f1f5f9' }, ticks: { stepSize: 1, color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        }
    });
</script>

@endsection