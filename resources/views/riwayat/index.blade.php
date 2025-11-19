@extends('layouts.app')

@section('title', 'Data Lengkap Riwayat Pelanggaran')

@section('content')

    <div class="card card-outline card-primary collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filter Pencarian Lanjutan</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('riwayat.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" name="cari_siswa" value="{{ request('cari_siswa') }}" class="form-control" placeholder="Nama...">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jurusan</label>
                            <select name="jurusan_id" class="form-control">
                                <option value="">- Semua -</option>
                                @foreach($allJurusan as $j)
                                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tingkat</label>
                            <select name="angkatan" class="form-control">
                                <option value="">- Semua -</option>
                                <option value="X" {{ request('angkatan') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                <option value="XI" {{ request('angkatan') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                <option value="XII" {{ request('angkatan') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas_id" class="form-control">
                                <option value="">- Semua -</option>
                                @foreach($allKelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-12 text-right">
                        <a href="{{ route('riwayat.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Pelanggaran ({{ $riwayat->total() }} Data Ditemukan)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Pencatat</th>
                        <th>Ket</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal_kejadian)->format('d/m/Y H:i') }}</td>
                        <td>{{ $r->siswa->nama_siswa }}</td>
                        <td>{{ $r->siswa->kelas->nama_kelas }}</td>
                        <td>{{ $r->jenisPelanggaran->nama_pelanggaran }}</td>
                        <td>
                            @php $kat = $r->jenisPelanggaran->kategoriPelanggaran->nama_kategori; @endphp
                            <span class="badge {{ $kat == 'BERAT' ? 'bg-danger' : ($kat == 'SEDANG' ? 'bg-warning' : 'bg-success') }}">
                                {{ $kat }}
                            </span>
                        </td>
                        <td style="color:red; font-weight:bold;">+{{ $r->jenisPelanggaran->poin }}</td>
                        <td>{{ $r->guruPencatat->nama }}</td>
                        <td>
                            @if($r->bukti_foto_path)
                                <a href="{{ asset('storage/' . $r->bukti_foto_path) }}" target="_blank" title="Lihat Bukti"><i class="fas fa-image"></i></a>
                            @endif
                            @if($r->keterangan)
                                <i class="fas fa-comment-dots" title="{{ $r->keterangan }}"></i>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data yang cocok dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $riwayat->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection