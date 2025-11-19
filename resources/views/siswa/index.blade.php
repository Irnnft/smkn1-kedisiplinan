@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Manajemen Data Siswa</h4>
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Tambah Siswa Baru
        </a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filter Data</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <form action="{{ route('siswa.index') }}" method="GET">
                <div class="row">
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cari Nama / NISN</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="cari" class="form-control" placeholder="Ketik disini..." value="{{ request('cari') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jurusan</label>
                            <select name="jurusan_id" class="form-control">
                                <option value="">- Semua Jurusan -</option>
                                @foreach($allJurusan as $j)
                                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                        {{ $j->nama_jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tingkat</label>
                            <select name="tingkat" class="form-control">
                                <option value="">- Semua -</option>
                                <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas_id" class="form-control">
                                <option value="">- Semua -</option>
                                @foreach($allKelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
                
                @if(request()->has('cari') || request()->has('jurusan_id'))
                    <div class="row">
                        <div class="col-12 text-right">
                            <a href="{{ route('siswa.index') }}" class="text-secondary small">
                                <i class="fas fa-undo"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <h3 class="card-title">Total: <strong>{{ $siswa->total() }}</strong> Siswa Ditemukan</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap table-striped">
                <thead>
                    <tr class="bg-dark">
                        <th style="width: 10px">#</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Wali Murid</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $key => $s)
                    <tr>
                        <td>{{ $siswa->firstItem() + $key }}</td>
                        <td>{{ $s->nisn }}</td>
                        <td><strong>{{ $s->nama_siswa }}</strong></td>
                        <td><span class="badge badge-info">{{ $s->kelas->nama_kelas }}</span></td>
                        <td>{{ $s->kelas->jurusan->nama_jurusan }}</td> <td>
                            @if($s->orangTua)
                                <i class="fas fa-check-circle text-success" title="Akun Terhubung"></i> {{ $s->orangTua->nama }}
                                <br><small class="text-muted">{{ $s->nomor_hp_ortu ?? '-' }}</small>
                            @else
                                <span class="text-danger text-sm"><i class="fas fa-times-circle"></i> Belum Akun</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-warning" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form onsubmit="return confirm('Yakin ingin menghapus siswa ini? Data pelanggaran terkait juga akan terhapus!');" action="{{ route('siswa.destroy', $s->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus Siswa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3"></i><br>
                            Tidak ada data siswa yang cocok dengan pencarian Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $siswa->links('pagination::bootstrap-4') }}
            </div>
            <div class="float-left pt-2 text-muted text-sm">
                Menampilkan {{ $siswa->firstItem() }} sampai {{ $siswa->lastItem() }} dari {{ $siswa->total() }} data.
            </div>
        </div>
    </div>

@endsection