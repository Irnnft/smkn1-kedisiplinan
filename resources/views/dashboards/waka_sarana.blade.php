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
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                Sarana & Prasarana
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">
                Halo, Waka Sarana! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">
                Panel monitoring fasilitas dan aset sekolah.
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
        
        <div class="stat-card hover-lift border-l-4 border-l-amber-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Kasus</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalPelanggaranFasilitas ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Kerusakan / Pelanggaran Aset</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-rose-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bulan Ini</span>
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $pelanggaranBulanIni ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Kasus Baru</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-rose-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-blue-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">My Entries</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $riwayatSaya->count() ?? 0 }}</h3>
            <p class="text-xs text-slate-500">Total Saya Catat</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

    </div>

    <h3 class="text-xs font-bold text-slate-500 mb-4 uppercase tracking-widest px-1">Aksi Cepat</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        
        <a href="{{ route('pelanggaran.create') }}" class="group hover-lift flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100 hover:border-rose-300 transition-all no-underline">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-800 group-hover:text-rose-600 transition-colors">Catat Pelanggaran Aset</h4>
                <p class="text-xs text-slate-500 mt-0.5">Laporkan kerusakan atau penyalahgunaan fasilitas.</p>
            </div>
            <div class="ml-auto text-slate-300 group-hover:text-rose-500 group-hover:translate-x-1 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </div>
        </a>

        <a href="{{ route('my-riwayat.index') }}" class="group hover-lift flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100 hover:border-blue-300 transition-all no-underline">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17"/><path d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 1.2 2 1.2 0 0 0-2.7-2.5l-2 2.5"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">Riwayat Laporan Saya</h4>
                <p class="text-xs text-slate-500 mt-0.5">Lihat dan kelola laporan yang Anda buat.</p>
            </div>
            <div class="ml-auto text-slate-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </div>
        </a>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    Pelanggaran Fasilitas Terbaru
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="float-table text-left w-full">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3 pl-8">Tanggal</th>
                            <th class="px-6 py-3">Siswa</th>
                            <th class="px-6 py-3">Pelanggaran</th>
                            <th class="px-6 py-3 text-center">Poin</th>
                            <th class="px-6 py-3 text-right pr-8">Pencatat</th>
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
                                <div class="text-[10px] text-slate-500">{{ $r->siswa->kelas->nama_kelas }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="text-xs text-slate-700 font-medium">{{ $r->jenisPelanggaran->nama_pelanggaran }}</div>
                                @if($r->keterangan)
                                    <div class="text-[10px] text-slate-400 italic mt-0.5 truncate max-w-[150px]">
                                        "{{ Str::limit($r->keterangan, 30) }}"
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                    +{{ $r->poin_diberikan }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right pr-8 text-[10px] text-slate-400">
                                {{ $r->guruPencatat->nama ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-slate-400 text-xs">
                                <div class="flex flex-col items-center opacity-60">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Fasilitas sekolah aman terkendali.
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
                <h3 class="text-sm font-bold text-slate-700 m-0">Riwayat Saya</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Laporan yang Anda input.</p>
            </div>

            <div class="flex-1 overflow-y-auto max-h-[400px] p-2">
                @forelse($riwayatSaya as $r)
                    <div class="group flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors border border-transparent hover:border-slate-100 mb-1">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ substr($r->siswa->nama_siswa, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-700 truncate">{{ $r->siswa->nama_siswa }}</h4>
                                <p class="text-[10px] text-slate-500 truncate">{{ $r->jenisPelanggaran->nama_pelanggaran }}</p>
                            </div>
                        </div>
                        <a href="{{ route('my-riwayat.edit', $r->id) }}" class="text-slate-300 hover:text-blue-600 p-1.5 rounded hover:bg-white hover:shadow-sm transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs px-4">
                        Anda belum mencatat pelanggaran apapun.
                    </div>
                @endforelse
            </div>
            
            <div class="p-3 border-t border-slate-100 text-center">
                <a href="{{ route('my-riwayat.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 no-underline hover:underline">
                    Lihat Semua Laporan Saya
                </a>
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