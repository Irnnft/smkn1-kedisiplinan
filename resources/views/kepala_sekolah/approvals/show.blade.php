@extends('layouts.app')

@section('content')

{{-- Tailwind Config --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#0f172a',
                    indigo: { 600: '#4f46e5', 50: '#eef2ff', 100: '#e0e7ff', 700: '#4338ca' },
                    rose: { 50: '#fff1f2', 100: '#ffe4e6', 600: '#e11d48', 700: '#be123c' },
                    emerald: { 50: '#ecfdf5', 100: '#d1fae5', 600: '#059669', 700: '#047857' }
                }
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<div class="page-wrap-custom min-h-screen p-6 bg-slate-50">
    <div class="max-w-5xl mx-auto">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">Persetujuan Kasus</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 m-0 tracking-tight flex items-center gap-3">
                    <i class="fas fa-file-signature text-indigo-600"></i> Tinjau & Setujui Kasus
                </h1>
            </div>
            
            <a href="{{ route('tindak-lanjut.pending-approval') }}" class="px-4 py-2 rounded-lg bg-white text-slate-600 text-xs font-bold border border-slate-200 hover:bg-slate-50 no-underline">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <div class="space-y-5">

            {{-- KOTAK 1: INFO SISWA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-indigo-50 to-slate-50 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-indigo-600 m-0 flex items-center gap-2">
                        <i class="fas fa-user"></i> Informasi Siswa
                    </h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-700 text-white flex items-center justify-center text-xl font-black shadow-md">
                            {{ strtoupper(substr($kasus->siswa->nama_siswa, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg font-black text-slate-800 leading-tight mb-1">{{ $kasus->siswa->nama_siswa }}</h2>
                            <div class="flex flex-wrap gap-2 text-[11px] font-bold uppercase tracking-wider">
                                <span class="text-slate-400">NISN: {{ $kasus->siswa->nisn }}</span>
                                <span class="text-indigo-600 px-2 py-0.5 bg-indigo-50 rounded-lg border border-indigo-100">{{ $kasus->siswa->kelas->nama_kelas }}</span>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            @php
                                $statusColors = [
                                    'Baru' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Menunggu Persetujuan' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'Ditangani' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                    'Selesai' => 'bg-slate-100 text-slate-600 border-slate-200',
                                ];
                                $statusColor = $statusColors[$kasus->status->value] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            @endphp
                            <span class="px-4 py-2 rounded-xl {{ $statusColor }} font-black text-xs uppercase tracking-wider border">
                                {{ $kasus->status->value }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOTAK 2: DETAIL KASUS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-rose-50 to-slate-50 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-rose-600 m-0 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Detail Kasus
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Pemicu Kasus</label>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 italic text-sm text-slate-600 leading-relaxed font-medium">
                            "{{ $kasus->pemicu }}"
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-100">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Sanksi Sistem</span>
                            <span class="text-sm font-black text-rose-700">{{ $kasus->sanksi_deskripsi }}</span>
                        </div>
                    </div>

                    @if($kasus->suratPanggilan && $kasus->suratPanggilan->keperluan)
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Keperluan Pemanggilan</label>
                        <div class="p-4 bg-amber-50/50 rounded-xl border border-amber-100 text-sm text-amber-800 leading-relaxed">
                            {{ $kasus->suratPanggilan->keperluan }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- KOTAK 3: SURAT PANGGILAN --}}
            @if($kasus->suratPanggilan)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-emerald-50 to-slate-50 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-emerald-600 m-0 flex items-center gap-2">
                        <i class="fas fa-envelope"></i> Surat Panggilan
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Surat</span>
                            <div class="font-mono text-xs text-slate-700 font-bold break-all">{{ $kasus->suratPanggilan->nomor_surat }}</div>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe Surat</span>
                            <span class="px-3 py-1 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200">
                                {{ $kasus->suratPanggilan->tipe_surat }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Pertemuan</span>
                            <div class="text-xs text-slate-700 font-semibold">
                                {{ \Carbon\Carbon::parse($kasus->suratPanggilan->tanggal_pertemuan)->format('d M Y') }}
                                <span class="text-slate-400 mx-1">•</span>
                                {{ $kasus->suratPanggilan->waktu_pertemuan }}
                            </div>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Cetak</span>
                            @if($kasus->suratPanggilan->printLogs->count() > 0)
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                                    <i class="fas fa-check-double"></i> {{ $kasus->suratPanggilan->printLogs->count() }}x dicetak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                    <i class="fas fa-times"></i> Belum dicetak
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Aksi Surat --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('tindak-lanjut.preview-surat', $kasus->id) }}" 
                           class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-all font-bold text-xs uppercase tracking-wider no-underline">
                            <i class="fas fa-eye"></i>
                            <span>Preview</span>
                        </a>
                        
                        <a href="{{ route('tindak-lanjut.edit-surat', $kasus->id) }}" 
                           class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all font-bold text-xs uppercase tracking-wider no-underline">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </a>

                        <a href="{{ route('tindak-lanjut.cetak-surat', $kasus->id) }}" 
                           onclick="return confirm('Cetak surat untuk {{ $kasus->siswa->nama_siswa }}?')"
                           target="_blank"
                           class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-all font-bold text-xs uppercase tracking-wider no-underline">
                            <i class="fas fa-print"></i>
                            <span>Cetak</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- KOTAK 4: FORM KEPUTUSAN (hanya jika status Menunggu Persetujuan) --}}
            @if($kasus->status->value === 'Menunggu Persetujuan')
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-amber-50 to-slate-50 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-amber-600 m-0 flex items-center gap-2">
                        <i class="fas fa-gavel"></i> Keputusan Persetujuan
                    </h3>
                </div>
                <div class="p-5">
                    <form id="approvalForm" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Pilih Tindakan</label>
                            <div class="grid grid-cols-2 gap-3 max-w-md">
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="action_type" value="approve" checked onclick="updateAction('approve')" class="peer sr-only">
                                    <div class="p-4 text-center rounded-xl border-2 border-slate-100 bg-slate-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                        <i class="fas fa-check text-xl text-slate-300 peer-checked:text-emerald-600 mb-2 block"></i>
                                        <span class="text-xs font-black uppercase text-slate-400 peer-checked:text-emerald-700">Setujui</span>
                                    </div>
                                </label>
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="action_type" value="reject" onclick="updateAction('reject')" class="peer sr-only">
                                    <div class="p-4 text-center rounded-xl border-2 border-slate-100 bg-slate-50 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition-all">
                                        <i class="fas fa-times text-xl text-slate-300 peer-checked:text-rose-600 mb-2 block"></i>
                                        <span class="text-xs font-black uppercase text-slate-400 peer-checked:text-rose-700">Tolak</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Catatan / Alasan (Opsional)</label>
                            <textarea name="reason" id="reason" rows="3" 
                                class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none text-sm transition-all"
                                placeholder="Tuliskan catatan untuk guru/wali murid..."></textarea>
                        </div>

                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg shadow-indigo-200 transition-all">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Keputusan
                        </button>
                    </form>
                </div>
            </div>
            @else
            {{-- Info jika status bukan Menunggu Persetujuan --}}
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 text-center">
                <i class="fas fa-info-circle text-3xl text-slate-300 mb-3"></i>
                <p class="text-sm text-slate-500 font-medium">
                    @if($kasus->status->value === 'Disetujui')
                        Kasus ini telah <span class="font-bold text-emerald-600">disetujui</span>.
                    @elseif($kasus->status->value === 'Ditolak')
                        Kasus ini telah <span class="font-bold text-rose-600">ditolak</span>.
                    @else
                        Status kasus: <span class="font-bold">{{ $kasus->status->value }}</span>
                    @endif
                </p>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
    function updateAction(action) {
        const form = document.getElementById('approvalForm');
        const id = "{{ $kasus->id }}";
        
        if (action === 'approve') {
            form.action = "/tindak-lanjut/" + id + "/approve";
        } else {
            form.action = "/tindak-lanjut/" + id + "/reject";
        }
    }

    // Inisialisasi action saat pertama kali load
    window.onload = function() {
        updateAction('approve');
    };
</script>

<style>
    .page-wrap-custom { font-family: 'Inter', sans-serif; }
</style>
@endsection