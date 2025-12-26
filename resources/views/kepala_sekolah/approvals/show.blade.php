@extends('layouts.app')

@section('content')

{{-- 1. TAILWIND CONFIG & SETUP --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#0f172a',
                    accent: '#3b82f6',
                    success: '#10b981',
                    danger: '#f43f5e',
                    indigo: { 600: '#4f46e5', 50: '#eef2ff', 100: '#e0e7ff', 700: '#4338ca' },
                    blue: { 50: '#eff6ff', 100: '#dbeafe', 600: '#2563eb' }
                },
                boxShadow: { 'soft': '0 4px 10px rgba(0,0,0,0.05)' }
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<div class="page-wrap-custom min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- HEADER SECTION --}}
        <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 m-0 tracking-tight flex items-center gap-3">
                    <i class="fas fa-file-signature text-indigo-600"></i> Tinjauan Kasus
                </h1>
                <p class="text-slate-500 text-sm mt-1">Evaluasi detail kejadian dan berikan keputusan validasi.</p>
            </div>
            
            <a href="{{ route('tindak-lanjut.pending-approval') }}" class="btn-clean-action no-underline">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ALERT INFORMASI SISWA --}}
        <div class="mb-6">
            <div class="flex items-center justify-between p-4 rounded-xl bg-blue-50 border border-blue-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shadow-inner">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="block font-bold text-blue-800 text-sm">Validasi Kasus: {{ $kasus->siswa->nama_siswa }}</span>
                        <span class="text-blue-600 text-xs font-medium uppercase tracking-wider">NISN: {{ $kasus->siswa->nisn }} • {{ $kasus->siswa->kelas->nama_kelas }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- KOLOM KIRI: DATA KONTEN --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- Identitas & Kronologi --}}
                <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 m-0">Detail Pelanggaran</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-5 pb-6 border-b border-slate-50">
                            <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl font-black shadow-lg">
                                {{ substr($kasus->siswa->nama_siswa, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $kasus->siswa->nama_siswa }}</h2>
                                <span class="custom-badge-base bg-blue-50 text-blue-600 border border-blue-100">
                                    {{ $kasus->siswa->kelas->nama_kelas }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="form-label-modern">Kronologi / Pemicu</label>
                            <div class="p-5 bg-slate-50 rounded-xl border border-slate-100 italic text-slate-600 text-sm leading-relaxed font-medium">
                                "{{ $kasus->pemicu }}"
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-rose-50 border border-rose-100">
                                <span class="form-label-modern !text-rose-400 !mb-1">Rekomendasi Sanksi</span>
                                <span class="text-sm font-black text-rose-700 leading-snug">{{ $kasus->sanksi_deskripsi ?? 'N/A' }}</span>
                            </div>
                            <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                                <span class="form-label-modern !text-indigo-400 !mb-1">Dilaporkan Oleh</span>
                                <span class="text-sm font-black text-indigo-700 leading-snug">{{ $kasus->user->nama ?? 'Sistem' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Administrasi Surat --}}
                @if($kasus->suratPanggilan)
                <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-emerald-50/50 border-b border-emerald-100 flex justify-between items-center">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-emerald-600 m-0 italic">Dokumen Surat Panggilan</h3>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest">{{ $kasus->suratPanggilan->tipe_surat }}</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <span class="form-label-modern">Nomor Surat</span>
                                <span class="font-mono text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-200">{{ $kasus->suratPanggilan->nomor_surat }}</span>
                            </div>
                            <div>
                                <span class="form-label-modern">Pertemuan</span>
                                <span class="text-xs font-bold text-slate-600 block">
                                    <i class="far fa-calendar-alt text-blue-500 mr-2"></i> {{ \Carbon\Carbon::parse($kasus->suratPanggilan->tanggal_pertemuan)->format('d M Y') }}
                                    <span class="mx-2 text-slate-300">|</span>
                                    <i class="far fa-clock text-blue-500 mr-1"></i> {{ $kasus->suratPanggilan->waktu_pertemuan }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 pt-4 border-t border-slate-50">
                            <a href="{{ route('tindak-lanjut.preview-surat', $kasus->id) }}" class="btn-action-outline no-underline flex-1">
                                <i class="fas fa-eye mr-2"></i> Preview
                            </a>
                            <a href="{{ route('tindak-lanjut.cetak-surat', $kasus->id) }}" target="_blank" class="btn-action-outline bg-slate-800 text-white border-slate-800 hover:bg-slate-900 no-underline flex-1">
                                <i class="fas fa-print mr-2 text-blue-400"></i> Cetak
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- KOLOM KANAN: PANEL KEPUTUSAN --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-6 bg-slate-900 text-white">
                        <h3 class="text-base font-bold m-0 flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-400"></i> Keputusan Akhir
                        </h3>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-widest">Panel Validasi Kepala Sekolah</p>
                    </div>

                    <form id="approvalForm" method="POST" class="p-6 space-y-6">
                        @csrf
                        
                        <div>
                            <label class="form-label-modern">Status Validasi</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="action_type" value="approve" checked onclick="updateAction('approve')" class="peer sr-only">
                                    <div class="p-3 text-center rounded-xl border border-slate-200 bg-slate-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all shadow-inner">
                                        <i class="fas fa-check text-slate-300 peer-checked:text-emerald-600 mb-1"></i>
                                        <span class="block text-[10px] font-black uppercase text-slate-400 peer-checked:text-emerald-700">Setuju</span>
                                    </div>
                                </label>
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="action_type" value="reject" onclick="updateAction('reject')" class="peer sr-only">
                                    <div class="p-3 text-center rounded-xl border border-slate-200 bg-slate-50 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition-all shadow-inner">
                                        <i class="fas fa-times text-slate-300 peer-checked:text-rose-600 mb-1"></i>
                                        <span class="block text-[10px] font-black uppercase text-slate-400 peer-checked:text-rose-700">Tolak</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="form-label-modern">Catatan / Alasan</label>
                            <textarea name="reason" id="reason" rows="4" 
                                class="form-input-modern w-full focus:border-indigo-500"
                                placeholder="Opsional: Tambahkan alasan atau pesan khusus..."></textarea>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                            Kirim Keputusan <i class="fas fa-paper-plane opacity-50"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function updateAction(action) {
        const form = document.getElementById('approvalForm');
        const id = "{{ $kasus->id }}";
        if (action === 'approve') {
            form.action = "{{ url('tindak-lanjut') }}/" + id + "/approve";
        } else {
            form.action = "{{ url('tindak-lanjut') }}/" + id + "/reject";
        }
    }
    window.onload = function() { updateAction('approve'); };
</script>

@endsection

@section('styles')
<style>
/* CSS CORE (KONSISTEN DENGAN TEMA AUDIT & LIST) */
.page-wrap-custom { background: #f8fafc; font-family: 'Inter', sans-serif; }

.btn-clean-action {
    padding: 0.5rem 1rem; border-radius: 0.75rem; background-color: #f1f5f9; 
    color: #475569; font-size: 0.75rem; font-weight: 700; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 0.5rem; border: 1px solid #e2e8f0;
}
.btn-clean-action:hover { background-color: #e2e8f0; color: #1e293b; }

.form-label-modern {
    display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    color: #94a3b8; margin-bottom: 0.5rem; letter-spacing: 0.05em;
}

.form-input-modern {
    display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #1e293b;
    background-color: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; transition: 0.2s;
}

.custom-badge-base {
    display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; 
    border-radius: 0.5rem; font-size: 0.7rem; font-weight: 700;
}

.btn-action-outline {
    padding: 8px 16px; border-radius: 10px; font-size: 10px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.05em; transition: 0.2s;
    display: flex; align-items: center; justify-content: center; 
    border: 1px solid #f1f5f9; background: #fff; color: #64748b;
}
</style>
@endsection