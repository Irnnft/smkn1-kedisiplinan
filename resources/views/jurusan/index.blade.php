@extends('layouts.app')

@section('content')

{{-- 1. TAILWIND CONFIG & SETUP --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Konfigurasi warna dasar agar seragam
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#0f172a', // Slate 900
                    accent: '#3b82f6',  // Blue 500
                    rose: { 500: '#f43f5e' }, 
                    amber: { 500: '#f59e0b' },
                    indigo: { 600: '#4f46e5' }
                },
                boxShadow: { 'soft': '0 4px 10px rgba(0,0,0,0.05)' }
            }
        },
        corePlugins: { preflight: false }
    }
</script>


<div class="page-wrap bg-gray-50 min-h-screen p-6">
    
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola Data Jurusan</h1>
                <p class="text-sm text-gray-500 mt-1">Daftar program studi dan data Kaprodi.</p>
            </div>
            
            @if(auth()->user()->hasRole('Operator Sekolah'))
            <a href="{{ route('jurusan.create') }}" class="px-5 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform active:scale-95 flex items-center gap-2 no-underline">
                <i class="fas fa-plus"></i> Tambah Jurusan
            </a>
            @endif
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm flex justify-between items-center">
                <div class="flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            </div>
        @endif
        @if(session('kaprodi_created'))
            @php $c = session('kaprodi_created'); @endphp
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-4 text-sm shadow-sm">
                <i class="fas fa-user-check mr-1"></i> Akun Kaprodi **{{ $c['username'] }}** telah dibuat. (Pass: **{{ $c['password'] }}**).
            </div>
        @endif
        
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                 <h3 class="text-base font-bold text-slate-700 m-0">Daftar Program Studi Aktif</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto">
                    <thead class="bg-gray-100 text-slate-600 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Nama Jurusan</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Kepala Program (Kaprodi)</th>
                            <th class="px-6 py-3 text-center">Kelas</th>
                            <th class="px-6 py-3 text-center">Siswa</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($jurusanList as $j)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 font-semibold text-slate-800">{{ $j->nama_jurusan }}</td>
                                <td class="px-6 py-3 text-sm text-slate-600">{{ $j->kode_jurusan ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    @if($j->kaprodi)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                            <i class="fas fa-user-tie mr-1"></i> {{ $j->kaprodi->username }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">- Belum Ditugaskan -</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">{{ $j->kelas_count }}</span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $j->siswa_count }}</span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('jurusan.show', $j) }}" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition" title="Lihat Detail"><i class="fas fa-eye w-4 h-4"></i></a>
                                        @if(auth()->user()->hasRole('Operator Sekolah'))
                                        <a href="{{ route('jurusan.edit', $j) }}" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition" title="Edit"><i class="fas fa-edit w-4 h-4"></i></a>
                                        <form action="{{ route('jurusan.destroy', $j) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin menghapus jurusan {{ $j->nama_jurusan }}? Semua kelas dan data terkait akan terpengaruh.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition" title="Hapus"><i class="fas fa-trash w-4 h-4"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400 text-sm">
                                    <div class="flex flex-col items-center opacity-60">
                                        <i class="fas fa-database text-3xl mb-2 text-slate-300"></i>
                                        <span class="font-semibold">Belum ada data program studi yang dimasukkan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

@section('styles')
<style>
    .page-wrap { font-family: 'Inter', sans-serif; }
    
    /* Ensure table rows look clean */
    .table-auto td, .table-auto th {
        vertical-align: middle;
    }
</style>
@endsection