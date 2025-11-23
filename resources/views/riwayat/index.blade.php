@extends('layouts.app')

@section('title', 'Riwayat Pelanggaran')

@section('styles')
    <!-- Panggil CSS Eksternal -->
    <link rel="stylesheet" href="{{ asset('css/pages/riwayat-index.css') }}">
@endsection

@section('content')
<div class="container-fluid">

    <!-- HEADER HALAMAN -->
    <div class="row mb-3 pt-2 align-items-center">
        <div class="col-sm-6">
            <h4 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-history text-primary mr-2"></i> Log Riwayat
            </h4>
        </div>
        <div class="col-sm-6 text-right">
             @php
                $role = auth()->user()->role->nama_role;
                $backRoute = match($role) {
                    'Wali Kelas' => route('dashboard.walikelas'),
                    'Kaprodi' => route('dashboard.kaprodi'),
                    'Kepala Sekolah' => route('dashboard.kepsek'),
                    default => route('dashboard.admin'),
                };
            @endphp
            <div class="btn-group">
                <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm border rounded mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Dashboard
                </a>
                <span class="btn btn-light btn-sm border rounded disabled text-dark font-weight-bold">
                    Total: {{ $riwayat->total() }} Data
                </span>
            </div>
        </div>
    </div>

    <!-- FILTER SECTION (STICKY) -->
    <!-- ID stickyFilter digunakan oleh JS untuk efek melayang -->
    <div id="stickyFilter" class="card card-outline card-primary shadow-sm border-0">
        
        <div class="card-body bg-white py-3" style="border-radius: 8px;">
            <form id="filterForm" action="{{ route('riwayat.index') }}" method="GET">
                <div class="row align-items-end">
                    
                    <!-- Filter Tanggal -->
                    <div class="col-md-3 mb-2">
                        <label class="filter-label">Rentang Waktu</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-clean" onchange="this.form.submit()">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text border-left-0 border-right-0 bg-white"><i class="fas fa-arrow-right text-muted small"></i></span>
                            </div>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-clean" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Filter Kelas (Admin Only) -->
                    @if(Auth::user()->role->nama_role != 'Wali Kelas')
                    <div class="col-md-2 mb-2">
                        <label class="filter-label">Kelas</label>
                        <select name="kelas_id" class="form-control form-control-sm form-control-clean" onchange="this.form.submit()">
                            <option value="">- Semua -</option>
                            @foreach($allKelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Filter Jenis Pelanggaran -->
                    <div class="col-md-4 mb-2">
                        <label class="filter-label">Jenis Pelanggaran</label>
                        <select name="jenis_pelanggaran_id" class="form-control form-control-sm form-control-clean" onchange="this.form.submit()">
                            <option value="">- Semua Jenis -</option>
                            @foreach($allPelanggaran as $jp)
                                <option value="{{ $jp->id }}" {{ request('jenis_pelanggaran_id') == $jp->id ? 'selected' : '' }}>
                                    [{{ $jp->kategoriPelanggaran->nama_kategori }}] {{ $jp->nama_pelanggaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Live Search -->
                    <div class="col-md-3 mb-2">
                        <label class="filter-label">Cari Siswa</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="liveSearch" name="cari_siswa" class="form-control form-control-clean" 
                                   placeholder="Ketik nama..." value="{{ request('cari_siswa') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Reset (Hanya muncul jika filter aktif) -->
                @if(request()->has('cari_siswa') || request()->has('start_date') || request()->has('jenis_pelanggaran_id') || request()->has('kelas_id') || request()->has('pencatat_id'))
                <div class="row mt-1 pt-2 border-top">
                    <div class="col-12 text-right">
                         <a href="{{ route('riwayat.index') }}" class="btn btn-default btn-xs shadow-sm text-danger font-weight-bold">
                            <i class="fas fa-times-circle mr-1"></i> Hapus Filter
                        </a>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- TABEL DATA (SCROLLABLE) -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-wrapper">
                <table class="table table-hover table-premium w-100">
                    <thead>
                        <tr>
                            <th style="padding-left: 25px;">Waktu</th>
                            <th>Identitas Siswa</th>
                            <th>Detail Pelanggaran</th>
                            <th class="text-center">Poin</th>
                            <th>Dicatat Oleh</th>
                            <th class="text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr>
                            <!-- 1. WAKTU -->
                            <td class="pl-4 align-top pt-3">
                                <div class="font-weight-bold text-dark">
                                    <a href="{{ route('riwayat.index', array_merge(request()->all(), ['start_date' => $r->tanggal_kejadian->format('Y-m-d'), 'end_date' => $r->tanggal_kejadian->format('Y-m-d')])) }}" 
                                       class="smart-link" title="Filter tanggal ini">
                                        {{ $r->tanggal_kejadian->format('d M Y') }}
                                    </a>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-clock mr-1"></i> {{ $r->tanggal_kejadian->format('H:i') }} WIB
                                </div>
                            </td>

                            <!-- 2. SISWA -->
                            <td>
                                <div class="student-profile">
                                    @php $initial = strtoupper(substr($r->siswa->nama_siswa, 0, 1)); @endphp
                                    <div class="avatar-circle">{{ $initial }}</div>
                                    
                                    <div>
                                        <a href="{{ route('riwayat.index', ['cari_siswa' => $r->siswa->nama_siswa]) }}" class="text-primary font-weight-bold smart-link" title="Lihat riwayat siswa ini">
                                            {{ $r->siswa->nama_siswa }}
                                        </a>
                                        
                                        <div class="student-meta">
                                            <a href="{{ route('riwayat.index', ['kelas_id' => $r->siswa->kelas_id]) }}" class="badge-class text-decoration-none" title="Filter kelas">
                                                {{ $r->siswa->kelas->nama_kelas }}
                                            </a>
                                            
                                            @php
                                                $totalPoinSiswa = $r->siswa->riwayatPelanggaran->sum(fn($rp) => $rp->jenisPelanggaran->poin);
                                                $bgTotal = $totalPoinSiswa >= 100 ? 'bg-danger' : ($totalPoinSiswa >= 50 ? 'bg-warning' : 'bg-secondary');
                                            @endphp
                                            <span class="badge-poin-total {{ $bgTotal }}" title="Total Akumulasi Poin Siswa Ini">
                                                Total: {{ $totalPoinSiswa }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 3. PELANGGARAN -->
                            <td style="white-space: normal; min-width: 280px;">
                                <div class="font-weight-bold text-dark">{{ $r->jenisPelanggaran->nama_pelanggaran }}</div>
                                <div class="small text-muted text-uppercase mt-1" style="font-weight: 600; font-size: 0.7rem;">
                                    {{ $r->jenisPelanggaran->kategoriPelanggaran->nama_kategori }}
                                </div>
                                @if($r->keterangan)
                                    <div class="text-muted font-italic small mt-1 pl-2 border-left" style="border-color: #dee2e6;">
                                        "{{ Str::limit($r->keterangan, 50) }}"
                                    </div>
                                @endif
                            </td>

                            <!-- 4. POIN -->
                            <td class="text-center">
                                <span class="badge badge-danger px-3 py-1 shadow-sm" style="font-size: 0.85rem; border-radius: 15px;">
                                    +{{ $r->jenisPelanggaran->poin }}
                                </span>
                            </td>

                            <!-- 5. PELAPOR -->
                            <td>
                                @if($r->guruPencatat)
                                    <a href="{{ route('riwayat.index', ['pencatat_id' => $r->guru_pencatat_user_id]) }}" class="d-flex align-items-center text-dark text-decoration-none group-hover" title="Lihat riwayat pelapor ini">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center mr-2 border" style="width:30px; height:30px;">
                                            <i class="fas fa-user-tie text-secondary small"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-sm">{{ $r->guruPencatat->nama }}</div>
                                            <div class="text-xs text-muted">Pelapor</div>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-muted small"><i class="fas fa-robot mr-1"></i> Sistem</span>
                                @endif
                            </td>

                            <!-- 6. BUKTI -->
                            <td class="text-center">
                                @if($r->bukti_foto_path)
                                    <a href="{{ asset('storage/' . $r->bukti_foto_path) }}" target="_blank" class="btn btn-light btn-sm border rounded-circle shadow-sm" style="width: 35px; height: 35px; padding: 0; line-height: 33px;" title="Lihat Foto">
                                        <i class="fas fa-image text-info"></i>
                                    </a>
                                @else
                                    <span class="text-muted text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-search fa-3x text-gray-200 mb-3"></i>
                                    <h6 class="text-muted font-weight-normal">Data tidak ditemukan.</h6>
                                    <p class="text-muted small">Coba sesuaikan filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan <strong>{{ $riwayat->firstItem() ?? 0 }} - {{ $riwayat->lastItem() ?? 0 }}</strong> dari <strong>{{ $riwayat->total() }}</strong> data
                </small>
                <div>
                    {{ $riwayat->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Load Logic Eksternal -->
    <script src="{{ asset('js/pages/riwayat-index.js') }}"></script>
@endpush