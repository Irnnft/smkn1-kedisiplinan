@extends('layouts.app')

@section('title', 'Detail Jurusan - ' . $jurusan->nama_jurusan)

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3>{{ $jurusan->nama_jurusan }}</h3>
            <p class="text-muted">Statistik dan monitoring jurusan</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('kepala-sekolah.data.jurusan') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $jurusan->kelas->count() }}</h3>
                    <p>Total Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalSiswa }}</h3>
                    <p>Total Siswa</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalPelanggaran }}</h3>
                    <p>Total Pelanggaran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $siswaPerluPembinaan }}</h3>
                    <p>Siswa Perlu Pembinaan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Jurusan --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Jurusan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th width="150">Kode Jurusan:</th>
                            <td><span class="badge badge-secondary">{{ $jurusan->kode_jurusan ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <th>Kaprodi:</th>
                            <td>{{ $jurusan->kaprodi?->nama ?? 'Belum ditentukan' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Kelas --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Kelas di {{ $jurusan->nama_jurusan }}</h5>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Pelanggaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusan->kelas as $kelas)
                        {{-- PERFORMANCE: Use withCount, not relationship! --}}
                        <tr>
                            <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                            <td>{{ $kelas->waliKelas?->nama ?? '-' }}</td>
                            <td>{{ $kelas->siswa_count }} siswa</td>
                            <td>
                                @if($kelas->pelanggaran_count > 0)
                                    <span class="badge badge-warning">{{ $kelas->pelanggaran_count }} kasus</span>
                                @else
                                    <span class="badge badge-success">Bersih</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kepala-sekolah.data.kelas.show', $kelas) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-chart-bar"></i> Detail & Statistik
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada kelas di jurusan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
