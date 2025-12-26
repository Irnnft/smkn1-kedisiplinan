@extends('layouts.app')

@section('content')

{{-- 1. TAILWIND CONFIG --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: { 
                colors: { 
                    primary: '#4f46e5', 
                    slate: { 800: '#1e293b', 900: '#0f172a' },
                    rose: { 50: '#fff1f2', 100: '#ffe4e6', 600: '#e11d48', 700: '#be123c' },
                    emerald: { 50: '#ecfdf5', 100: '#d1fae5', 600: '#059669', 700: '#047857' }
                } 
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<div class="page-container p-4">
    
    <div class="mb-6 border-b border-slate-200 pb-3">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <i class="fas fa-file-signature text-indigo-600"></i>
                    Tinjau & Setujui Kasus
                </h1>
                <p class="text-slate-500 text-sm mt-1">Lakukan validasi data pelanggaran dan tentukan keputusan akhir untuk kasus ini.</p>
            </div>
            <a href="{{ route('tindak-lanjut.pending-approval') }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-2 px-4 rounded-xl transition-colors text-sm flex items-center gap-2 no-underline">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-700 m-0 uppercase tracking-wide flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        Identitas Siswa
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl font-black shadow-lg shadow-indigo-100">
                            {{ substr($kasus->siswa->nama_siswa, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $kasus->siswa->nama_siswa }}</h2>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-0.5 rounded">NISN: {{ $kasus->siswa->nisn }}</span>
                                <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-100 uppercase">{{ $kasus->siswa->kelas->nama_kelas }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-700 m-0 uppercase tracking-wide flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Detail Kejadian & Sanksi
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="form-label-modern">Pemicu / Kejadian</label>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 italic text-slate-600 text-sm leading-relaxed">
                            "{{ $kasus->pemicu }}"
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-100">
                            <span class="block text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1">Rekomendasi Sanksi</span>
                            <span class="text-sm font-bold text-rose-700">{{ $kasus->sanksi_deskripsi ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                            <span class="block text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-1">Dilaporkan Oleh</span>
                            <span class="text-sm font-bold text-indigo-700">{{ $kasus->user->nama ?? 'Sistem' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($kasus->suratPanggilan)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-100">
                    <h3 class="text-sm font-bold text-emerald-700 m-0 uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-envelope"></i> Manajemen Surat Panggilan
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label-modern">Nomor Surat</label>
                            <div class="font-mono text-sm text-slate-700 font-bold bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $kasus->suratPanggilan->nomor_surat }}</div>
                        </div>
                        <div>
                            <label class="form-label-modern">Tipe Surat</label>
                            <span class="px-3 py-1 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200">
                                {{ $kasus->suratPanggilan->tipe_surat }}
                            </span>
                        </div>
                        <div>
                            <label class="form-label-modern">Jadwal Pertemuan</label>
                            <div class="text-sm text-slate-700 font-semibold flex items-center gap-2">
                                <i class="far fa-calendar-alt text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($kasus->suratPanggilan->tanggal_pertemuan)->format('d M Y') }}
                                <span class="text-slate-300">|</span>
                                <i class="far fa-clock text-slate-400"></i>
                                {{ $kasus->suratPanggilan->waktu_pertemuan }}
                            </div>
                        </div>
                        <div>
                            <label class="form-label-modern">Status Cetak</label>
                            @if($kasus->suratPanggilan->printLogs->count() > 0)
                                <span class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                                    <i class="fas fa-check-double"></i> {{ $kasus->suratPanggilan->printLogs->count() }}x dicetak
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                    <i class="fas fa-times"></i> Belum dicetak
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('tindak-lanjut.preview-surat', $kasus->id) }}" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all font-bold text-xs no-underline">
                            <i class="fas fa-eye text-blue-500"></i> Preview
                        </a>
                        <a href="{{ route('tindak-lanjut.edit-surat', $kasus->id) }}" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all font-bold text-xs no-underline">
                            <i class="fas fa-edit text-amber-500"></i> Edit Isi
                        </a>
                        <a href="{{ route('tindak-lanjut.cetak-surat', $kasus->id) }}" target="_blank" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all font-bold text-xs no-underline shadow-sm">
                            <i class="fas fa-print"></i> Cetak Surat
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            
            @if($kasus->status->value === 'Baru')
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-lg p-6 mb-6 text-white">
                <h4 class="text-sm font-bold mb-2 flex items-center gap-2">
                    <i class="fas fa-rocket"></i> Mulai Penanganan
                </h4>
                <p class="text-xs text-indigo-100 leading-relaxed mb-4">
                    Kasus ini masih berstatus <strong>Baru</strong>. Silakan ubah status menjadi sedang ditangani sebelum memberikan keputusan.
                </p>
                <form action="{{ route('tindak-lanjut.mulai-tangani', $kasus->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full bg-white text-indigo-600 font-bold py-2.5 rounded-xl text-xs uppercase tracking-wider hover:bg-indigo-50 transition-colors">
                        Mulai Tangani Kasus
                    </button>
                </form>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 sticky top-6 overflow-hidden">
                <div class="p-6 bg-slate-900 text-white">
                    <h3 class="text-base font-bold m-0 flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-400"></i> Keputusan Akhir
                    </h3>
                </div>

                <form id="approvalForm" method="POST" class="p-6 space-y-5">
                    @csrf
                    
                    <div>
                        <label class="form-label-modern">Tindakan Validasi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="action_type" value="approve" checked onclick="updateAction('approve')" class="peer sr-only">
                                <div class="p-3 text-center rounded-xl border border-slate-200 bg-slate-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all">
                                    <span class="block text-xs font-bold uppercase">Setujui</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="action_type" value="reject" onclick="updateAction('reject')" class="peer sr-only">
                                <div class="p-3 text-center rounded-xl border border-slate-200 bg-slate-50 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition-all">
                                    <span class="block text-xs font-bold uppercase">Tolak</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="reason" class="form-label-modern">Catatan / Alasan</label>
                        <textarea name="reason" id="reason" rows="4" class="form-input-modern w-full" placeholder="Tuliskan catatan untuk guru atau orang tua..."></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm uppercase tracking-widest shadow-lg shadow-blue-100 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                            Kirim Keputusan <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
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
    window.onload = function() { updateAction('approve'); };
</script>

@endsection

@section('styles')
<style>
    .form-label-modern {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.5rem;
        letter-spacing: 0.05em;
    }

    .form-input-modern {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #1e293b;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        transition: all 0.2s;
    }

    .form-input-modern:focus {
        border-color: #4f46e5;
        outline: 0;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .page-container {
        max-width: 1400px;
        margin: 0 auto;
    }
</style>
@endsection