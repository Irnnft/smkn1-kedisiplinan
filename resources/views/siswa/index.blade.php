@extends('layouts.app')

@section('title', 'Data Siswa')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/siswa-index.css') }}">
@endsection

@section('content')

    @php
        $userRole = Auth::user()->role->nama_role;
        $isOperator = ($userRole == 'Operator Sekolah');
        $isWaliKelas = ($userRole == 'Wali Kelas');
        $isWaka = ($userRole == 'Waka Kesiswaan');
    @endphp

    <div class="container-fluid">
        
        <!-- HEADER -->
        <div class="row mb-3 pt-2 align-items-center">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-user-graduate text-primary mr-2"></i>
                    @if($isWaliKelas) Siswa Kelas Anda @else Data Induk Siswa @endif
                </h4>
            </div>
            <div class="col-sm-6 text-right">
                <div class="btn-group">
                    @if($isOperator || $isWaka)
                         <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary btn-sm border rounded mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Dashboard
                        </a>
                    @elseif($isWaliKelas)
                        <a href="{{ route('dashboard.walikelas') }}" class="btn btn-outline-secondary btn-sm border rounded mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Dashboard
                        </a>
                    @endif
                   
                    @if($isOperator)
                    <a href="{{ route('siswa.create') }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Siswa
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- FILTER SECTION (CLEAN - TANPA JUDUL) -->
        <div id="stickyFilter" class="card card-outline card-primary shadow-sm mb-4">
            <div class="card-body bg-light py-3">
                <form id="filterForm" action="{{ route('siswa.index') }}" method="GET">
                    <div class="row align-items-end">
                        
                        <!-- FILTER KHUSUS OPERATOR/ADMIN -->
                        @if(!$isWaliKelas)
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold text-muted mb-1">Tingkat</label>
                            <select name="tingkat" class="form-control form-control-sm form-control-clean" onchange="this.form.submit()">
                                <option value="">- Semua -</option>
                                <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold text-muted mb-1">Jurusan</label>
                            <select name="jurusan_id" class="form-control form-control-sm form-control-clean" onchange="this.form.submit()">
                                <option value="">- Semua -</option>
                                @foreach($allJurusan as $j)
                                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold text-muted mb-1">Kelas</label>
                            <select name="kelas_id" class="form-control form-control-sm form-control-clean" onchange="this.form.submit()">
                                <option value="">- Semua -</option>
                                @foreach($allKelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- LIVE SEARCH -->
                        <div class="{{ $isWaliKelas ? 'col-md-10' : 'col-md-3' }} mb-2">
                            <label class="small font-weight-bold text-muted mb-1">Cari Siswa</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="liveSearch" name="cari" class="form-control form-control-clean" 
                                       placeholder="Ketik Nama atau NISN..." value="{{ request('cari') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- RESET BUTTON (Desktop: Sebelah Search, Mobile: Bawah) -->
                         @if($isWaliKelas)
                            <div class="col-md-2 mb-2 text-right">
                                 <a href="{{ route('siswa.index') }}" class="btn btn-default btn-sm btn-block" title="Hapus Pencarian"><i class="fas fa-undo"></i></a>
                            </div>
                        @else
                             <!-- Tombol Reset muncul hanya jika ada filter -->
                            @if(request()->has('cari') || request()->has('kelas_id') || request()->has('tingkat') || request()->has('jurusan_id'))
                                <div class="col-12 mt-2 text-right border-top pt-2">
                                    <a href="{{ route('siswa.index') }}" class="btn btn-xs text-danger font-weight-bold">
                                        <i class="fas fa-times-circle"></i> Reset Filter
                                    </a>
                                </div>
                            @endif
                        @endif

                    </div>
                </form>
            </div>
        </div>

        <!-- TABEL DATA -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped text-nowrap table-valign-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 10px">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            @if(!$isWaliKelas) <th>Kelas</th> @endif
                            <th>Kontak Ortu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $key => $s)
                        <tr>
                            <td>{{ $siswa->firstItem() + $key }}</td>
                            <td><code>{{ $s->nisn }}</code></td>
                            <td>
                                <span class="font-weight-bold text-dark">{{ $s->nama_siswa }}</span>
                                @if($isWaliKelas && !$s->orangTua)
                                    <i class="fas fa-exclamation-circle text-danger ml-1" title="Akun Orang Tua belum dihubungkan oleh Operator"></i>
                                @endif
                            </td>
                            @if(!$isWaliKelas)
                                <td><span class="badge badge-light border">{{ $s->kelas->nama_kelas }}</span></td>
                            @endif
                            <td>
                                @if($s->nomor_hp_ortu)
                                    <a href="https://wa.me/62{{ ltrim($s->nomor_hp_ortu, '0') }}" target="_blank" class="text-success font-weight-bold">
                                        <i class="fab fa-whatsapp"></i> {{ $s->nomor_hp_ortu }}
                                    </a>
                                @else
                                    <span class="text-muted text-sm font-italic">Kosong</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if($isWaliKelas)
                                        <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-info btn-sm shadow-sm" title="Update Kontak">
                                            <i class="fas fa-edit mr-1"></i> Update Kontak
                                        </a>
                                    @elseif($isOperator)
                                        <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-warning" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form onsubmit="return confirm('Yakin ingin menghapus siswa {{ $s->nama_siswa }}?');" action="{{ route('siswa.destroy', $s->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @elseif($isWaka)
                                        <a href="{{ route('riwayat.index', ['cari_siswa' => $s->nama_siswa]) }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                                            <i class="fas fa-history mr-1"></i> Lihat Riwayat
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isWaliKelas ? 5 : 6 }}" class="text-center py-5 text-muted">
                                <i class="fas fa-search-minus fa-3x mb-3 opacity-50"></i><br>
                                Data siswa tidak ditemukan dengan filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix bg-white">
                <div class="float-right">
                    {{ $siswa->links('pagination::bootstrap-4') }}
                </div>
                <div class="float-left pt-2 text-muted text-sm">
                    Total: {{ $siswa->total() }} Data
                </div>
            </div>
        </div>
    </div>
@endsection