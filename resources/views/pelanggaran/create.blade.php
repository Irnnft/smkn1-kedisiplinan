@extends('layouts.app')

@section('content')

<!-- Tailwind Setup -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { primary: '#4f46e5', slate: { 800: '#1e293b', 900: '#0f172a' } },
                screens: { 'xs': '375px' }
            }
        },
        corePlugins: { preflight: false } // Avoid conflict with Bootstrap
    }
</script>
<style>
    /* Custom Scrollbar for lists */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* Helpers for JS selectors */
    .selected { background-color: #eff6ff !important; border-color: #3b82f6 !important; }
    /* Checkbox click area fix */
    .checkbox-wrapper { pointer-events: auto; }
</style>

<div class="page-wrap bg-slate-50 min-h-screen p-6 font-sans">
    
    <!-- Toast/Alert Area (Maintains old logic keys) -->
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            <button type="button" class="text-emerald-400 hover:text-emerald-600" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Terjadi Kesalahan
            </div>
            <ul class="list-disc list-inside text-sm space-y-1 ml-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <div class="relative group">
    <div class="flex items-center gap-5">
        <div class="relative flex-shrink-0">
            <div class="absolute -inset-1 bg-indigo-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-300"></div>
            <div class="relative bg-white border border-slate-200 p-3.5 rounded-2xl shadow-sm text-indigo-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
        </div>

        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight m-0">
                    Input Pelanggaran
                </h1>
                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200 uppercase tracking-wider">
                    Sistem Poin
                </span>
            </div>
            <p class="text-slate-500 text-sm font-medium mt-0.5">
                Manajemen kedisiplinan siswa melalui pencatatan poin pelanggaran secara real-time.
            </p>
        </div>
    </div>
</div>
        
        @php
            $role = auth()->user()->effectiveRoleName() ?? auth()->user()->role?->nama_role;
            $backRoute = match($role) {
                'Wali Kelas' => route('dashboard.walikelas'),
                'Kaprodi' => route('dashboard.kaprodi'),
                'Kepala Sekolah' => route('dashboard.kepsek'),
                default => route('dashboard.admin'),
            };
        @endphp
        <a href="{{ $backRoute }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Main Form -->
    <form action="{{ route('riwayat.store') }}" method="POST" enctype="multipart/form-data" id="formPelanggaran" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @csrf
        
        <!-- Kolom Kiri: Siswa -->
        <div class="lg:col-span-5 flex flex-col gap-0">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-[600px]">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs">1</span>
                        Pilih Siswa
                    </h3>
                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full border border-blue-100" id="countSiswa">{{ count($daftarSiswa) }} Siswa</span>
                </div>
                
                <div class="p-4 flex-1 flex flex-col gap-3 min-h-0">
                    <!-- Filters -->
                    <div class="grid grid-cols-3 gap-2">
                        <select id="filterTingkat" class="w-full bg-slate-50 border border-slate-200 text-xs font-medium rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Tingkat...</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                        <select id="filterJurusan" class="w-full bg-slate-50 border border-slate-200 text-xs font-medium rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Jurusan...</option>
                             @foreach($jurusan as $j) <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option> @endforeach
                        </select>
                        <select id="filterKelas" class="w-full bg-slate-50 border border-slate-200 text-xs font-medium rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Kelas...</option>
                             @foreach($kelas as $k) <option value="{{ $k->id }}" data-jurusan="{{ $k->jurusan_id }}">{{ $k->nama_kelas }}</option> @endforeach
                        </select>
                    </div>
                    
                    <!-- Search -->
                     <div class="relative">
                        <input type="text" id="searchSiswa" class="w-full pl-9 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Cari nama atau NISN...">
                        <svg class="absolute left-3 top-2.5 text-slate-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <button type="button" 
                                onclick="resetFilters()" 
                                class="absolute right-2 top-1.5 p-1.5 bg-slate-100 text-slate-500 rounded-lg hover:bg-rose-100 hover:text-rose-600 transition-all duration-200 group/reset border-none cursor-pointer flex items-center justify-center shadow-sm active:scale-90" 
                                title="Reset Filter">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- List Area -->
                    <div class="custom-scrollbar overflow-y-auto flex-1 pr-1 space-y-2" id="studentListContainer">
                         @foreach($daftarSiswa as $siswa)
                            @php
                                $tingkat = $siswa->tingkat ?? explode(' ', $siswa->nama_kelas ?? '')[0] ?? '';
                                $jurusanId = $siswa->jurusan_id ?? '';
                                $searchText = strtolower($siswa->nama_siswa . ' ' . $siswa->nisn);
                                $initial = strtoupper(substr($siswa->nama_siswa, 0, 1));
                            @endphp
                            
                            <!-- Student Item (Maintain classes for JS) -->
                            <div class="student-item group flex items-center gap-3 p-3 rounded-xl border border-transparent hover:bg-slate-50 hover:border-slate-200 cursor-pointer transition-all"
                                 data-tingkat="{{ $tingkat }}"
                                 data-jurusan="{{ $jurusanId }}"
                                 data-kelas="{{ $siswa->kelas_id }}"
                                 data-search="{{ $searchText }}"
                                 onclick="selectStudent(this)">
                                
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-slate-800 text-sm truncate font-weight-bold">{{ $siswa->nama_siswa }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200">{{ $siswa->nama_kelas ?? '-' }}</span>
                                        <span class="text-[10px] text-slate-400">#{{ $siswa->nisn }}</span>
                                    </div>
                                </div>
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" name="siswa_id[]" value="{{ $siswa->id }}" class="siswa-checkbox w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                            </div>
                        @endforeach
                        
                        <div id="noResultMsg" class="hidden flex flex-col items-center justify-center py-10 text-slate-400">
                            <svg class="w-8 h-8 opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <p class="text-sm">Siswa tidak ditemukan.</p>
                        </div>
                    </div>
                     @error('siswa_id') <div class="text-rose-500 text-xs mt-1 font-bold flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pelanggaran + Form -->
        <div class="lg:col-span-7 flex flex-col gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                         <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs">2</span>
                        Data Pelanggaran
                    </h3>
                </div>

                <div class="p-6">
                    <!-- Filter Pills (Maintain .btn class for JS) --> 
                    <!-- JS uses querySelectorAll('.btn') inside .filter-pills and toggles .active -->
                    <div class="filter-pills flex flex-wrap gap-2 mb-6" data-toggle="buttons">
    {{-- Button Semua --}}
    <label class="btn active group cursor-pointer px-5 py-2 rounded-xl border border-slate-200 bg-white transition-all hover:bg-slate-50 [&.active]:bg-slate-900 [&.active]:border-slate-900 shadow-sm" onclick="setFilterTopic('all', this)">
        <input type="radio" class="hidden" checked>
        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 group-[.active]:text-white transition-colors">Semua</span>
    </label>

    @php $topics = [
        ['id' => 'atribut', 'label' => 'Atribut'],
        ['id' => 'kehadiran', 'label' => 'Absensi'],
        ['id' => 'kerapian', 'label' => 'Kerapian'],
        ['id' => 'ibadah', 'label' => 'Ibadah']
    ]; @endphp

    @foreach($topics as $topic)
    {{-- Button Kategori Standar --}}
    <label class="btn group cursor-pointer px-5 py-2 rounded-xl border border-slate-200 bg-white transition-all hover:border-blue-400 hover:bg-blue-50/50 [&.active]:bg-blue-600 [&.active]:border-blue-600 shadow-sm" onclick="setFilterTopic('{{ $topic['id'] }}', this)">
        <input type="radio" class="hidden">
        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 group-[.active]:text-white transition-colors">{{ $topic['label'] }}</span>
    </label>
    @endforeach

    {{-- Button Berat --}}
    <label class="btn group cursor-pointer px-5 py-2 rounded-xl border border-rose-200 bg-rose-50/30 transition-all hover:bg-rose-100/50 [&.active]:bg-rose-600 [&.active]:border-rose-600 shadow-sm" onclick="setFilterTopic('berat', this)">
        <input type="radio" class="hidden">
        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-500 group-[.active]:text-white transition-colors">BERAT</span>
    </label>
</div>

<style>
    /* Reset & Interaction */
    .filter-pills .btn {
        outline: none !important;
        border-style: solid;
    }
    .filter-pills .btn:active {
        transform: scale(0.96);
    }
    /* Memastikan teks tetap di tengah */
    .filter-pills label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 80px;
    }
</style>
                    <!-- Search Pelanggaran -->
                    <div class="relative mb-4">
                         <input type="text" id="searchPelanggaran" class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 outline-none transition-all" placeholder="Cari masalah (contoh: Bolos, Rokok, Telat)...">
                        <svg class="absolute left-3 top-3 text-rose-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Violation List -->
                    <div class="custom-scrollbar overflow-y-auto h-64 border border-slate-100 rounded-xl p-2 mb-6 bg-slate-50/50">
                        @foreach($daftarPelanggaran as $jp)
                            @php
                                $kategoriLower = strtolower($jp->kategoriPelanggaran->nama_kategori);
                                $namaLower = strtolower($jp->nama_pelanggaran);
                            @endphp

                            <div class="violation-item group flex items-start gap-3 p-3 mb-1 rounded-lg bg-white border border-slate-100 hover:border-rose-300 cursor-pointer transition-all shadow-sm" 
                                 data-nama="{{ $namaLower }}"
                                 data-kategori="{{ $kategoriLower }}"
                                 data-keywords="{{ strtolower($jp->keywords ?? '') }}"
                                 data-filter-category="{{ $jp->filter_category ?? '' }}"
                                 onclick="selectViolation(this)">
                                
                                <div class="flex-1">
                                    <div class="font-bold text-slate-700 text-sm font-weight-bold">{{ $jp->nama_pelanggaran }}</div>
                                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 mt-0.5">
                                        {{ $jp->kategoriPelanggaran->nama_kategori }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="bg-rose-50 text-rose-600 text-[10px] font-bold px-2 py-1 rounded border border-rose-100">{{ $jp->getDisplayPoin() }} @if(!$jp->usesFrequencyRules()) Poin @endif</span>
                                    <input type="checkbox" name="jenis_pelanggaran_id[]" value="{{ $jp->id }}" class="pelanggaran-checkbox w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                </div>
                            </div>
                        @endforeach
                        <div id="noViolationMsg" class="hidden text-center py-8 text-slate-400 text-sm">Pelanggaran tidak ditemukan.</div>
                    </div>
                     @error('jenis_pelanggaran_id') <div class="text-rose-500 text-xs mb-4 font-bold flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> {{ $message }}</div> @enderror

                    <!-- Form Inputs Area -->
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Kejadian</label>
                                <div class="flex gap-2">
                                    <input type="date" name="tanggal_kejadian" class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none" value="{{ date('Y-m-d') }}" required>
                                    <input type="time" id="jamKejadian" name="jam_kejadian" class="w-24 bg-white border border-slate-200 text-slate-700 text-sm rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none" value="{{ old('jam_kejadian', date('H:i')) }}" data-has-old="{{ old('jam_kejadian') ? '1' : '0' }}">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bukti Foto (Wajib)</label>
                                <div class="relative">
                                     <input type="file" class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-lg file:border-0
                                        file:text-xs file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        file:cursor-pointer hover:file:bg-blue-100
                                      " name="bukti_foto" id="customFile" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan / Kronologi</label>
                            <textarea name="keterangan" class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none" rows="2" placeholder="Opsional..."></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" id="btnPreview" class="flex justify-center items-center gap-2 py-3 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> Preview Dampak
                            </button>
                            <button type="submit" id="btnSubmitPreview" class="flex justify-center items-center gap-2 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md shadow-indigo-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg> Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Modal Preview (Keep Bootstrap structure for JS compatibility but style it) -->
    
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-2xl shadow-2xl border-0 overflow-hidden">
                <div class="modal-header bg-indigo-600 text-white border-0 p-4">
                    <h5 class="modal-title font-bold text-lg flex items-center gap-2" id="previewModalLabel">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> Preview Dampak Pelanggaran
                    </h5>
                    <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-slate-50 p-0" id="previewModalContent">
                    <!-- AJAX Content -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content rounded-2xl shadow-2xl border-0">
                <div class="modal-header border-b border-slate-100 p-5">
                    <h5 class="modal-title font-bold text-slate-800 text-lg">Konfirmasi Pencatatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6 text-sm text-slate-600">
                    <div class="mb-4">
                        <div class="font-bold text-slate-400 text-xs uppercase tracking-wider mb-1">Siswa Terpilih</div>
                        <ul id="confirmStudents" class="list-disc list-inside font-semibold text-slate-800"></ul>
                    </div>
                     <div class="mb-4">
                        <div class="font-bold text-slate-400 text-xs uppercase tracking-wider mb-1">Pelanggaran</div>
                        <ul id="confirmViolations" class="list-disc list-inside font-semibold text-rose-600"></ul>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="font-bold text-slate-400 text-xs uppercase tracking-wider mb-1">Waktu</div>
                            <div id="confirmTime" class="font-medium"></div>
                        </div>
                        <div>
                             <div class="font-bold text-slate-400 text-xs uppercase tracking-wider mb-1">Keterangan</div>
                            <div id="confirmKeterangan" class="font-medium italic"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 p-4 bg-slate-50 rounded-b-2xl">
                    <button type="button" class="px-4 py-2 rounded-lg text-slate-500 font-bold hover:bg-slate-200 transition-colors" data-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmSubmit" class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">Konfirmasi & Simpan</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <!-- jQuery and BS Custom File Input (kept as requested) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script src="{{ asset('js/pages/pelanggaran/create.js') }}"></script>
@endpush