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
                Wali Kelas Panel
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">
                Halo, Wali Kelas {{ $kelas->nama_kelas }}! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">
                Pantau perkembangan dan kedisiplinan siswa kelas Anda disini.
            </p>
        </div>

        <div class="hidden xs:flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 shadow-inner min-w-[140px] relative z-10">
            <div class="bg-blue-500/20 p-2 rounded-lg text-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <span class="block text-2xl font-bold leading-none tracking-tight">{{ $kelas->siswa->count() }}</span>
                <span class="block text-[10px] uppercase tracking-wider text-blue-200">Total Siswa</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        
        <div class="stat-card hover-lift border-l-4 border-l-blue-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Populasi</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $kelas->siswa->count() }}</h3>
            <p class="text-xs text-slate-500">Siswa di Kelas {{ $kelas->nama_kelas }}</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-rose-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Action Needed</span>
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300 {{ $kasusBaru->count() > 0 ? 'animate-pulse' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $kasusBaru->count() }}</h3>
            <p class="text-xs text-slate-500">Kasus Perlu Penanganan</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-rose-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-amber-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $riwayatTerbaru->count() }}</h3>
            <p class="text-xs text-slate-500">Total Riwayat Terbaru</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0 flex items-center gap-2">
                    @if($kasusBaru->count() > 0)
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        Perlu Tindakan Anda
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Status Kelas: Aman
                    @endif
                </h3>
                @if($kasusBaru->count() > 0)
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded border border-rose-100">
                        {{ $kasusBaru->count() }} Pending
                    </span>
                @endif
            </div>
            
            <div class="overflow-x-auto">
                <table class="float-table text-left w-full">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3 pl-8">Siswa</th>
                            <th class="px-6 py-3">Pelanggaran</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right pr-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($kasusBaru as $kasus)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-3 pl-8">
                                <div class="text-sm font-bold text-slate-800">{{ $kasus->siswa->nama_siswa }}</div>
                                <div class="text-[10px] text-slate-500">NIS: {{ $kasus->siswa->nis }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-xs text-slate-600 truncate max-w-[200px]" title="{{ $kasus->pemicu }}">
                                    {{ Str::limit($kasus->pemicu, 35) }}
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($kasus->status == 'Menunggu Persetujuan')
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-1 rounded">Menunggu</span>
                                @elseif($kasus->status == 'Baru')
                                    <span class="text-[10px] font-bold bg-rose-100 text-rose-700 px-2 py-1 rounded animate-pulse">Baru</span>
                                @else
                                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $kasus->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right pr-8">
                                <a href="{{ route('kasus.edit', $kasus->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-white bg-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-700 shadow-sm transition no-underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Proses
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-slate-400 text-xs">
                                <div class="flex flex-col items-center opacity-60">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Tidak ada kasus aktif. Kelas aman.
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
                <h3 class="text-sm font-bold text-slate-700 m-0">📜 Riwayat Terbaru</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Aktivitas terakhir di kelas ini.</p>
            </div>

            <div class="flex-1 overflow-y-auto max-h-[400px] p-2">
                @forelse($riwayatTerbaru as $r)
                    <div class="group flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-colors border border-transparent hover:border-slate-100 mb-1">
                        <div class="flex flex-col items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-500 shrink-0 border border-slate-200">
                            <span class="text-[10px] font-bold uppercase">{{ $r->tanggal_kejadian->format('M') }}</span>
                            <span class="text-sm font-bold leading-none">{{ $r->tanggal_kejadian->format('d') }}</span>
                        </div>
                        
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-xs font-bold text-slate-700 truncate">{{ $r->siswa->nama_siswa }}</h4>
                                <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">+{{ $r->poin_diberikan }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-snug line-clamp-2">
                                {{ $r->jenisPelanggaran->nama_pelanggaran }}
                            </p>
                            <div class="text-[9px] text-slate-400 mt-1 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $r->guruPencatat->nama ?? 'Sistem' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-400 text-xs px-4">
                        Belum ada riwayat pelanggaran tercatat.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

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
</style>

@endsection