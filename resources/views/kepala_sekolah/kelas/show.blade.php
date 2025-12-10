@extends('layouts.app')

@section('title', 'Detail Kelas - ' . $kelas->nama_kelas)

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3>{{ $kelas->nama_kelas }}</h3>
            <p class="text-muted">{{ $kelas->jurusan?->nama_jurusan ?? '-' }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('kepala-sekolah.data.kelas') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
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
                    <p>Perlu Pembinaan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($avgPoin, 1) }}</h3>
                    <p>Rata-rata Poin</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Kelas --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Kelas</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th width="150">Jurusan:</th>
                            <td><span class="badge badge-primary">{{ $kelas->jurusan?->nama_jurusan ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <th>Wali Kelas:</th>
                            <td>{{ $kelas->waliKelas?->nama ?? 'Belum ditentukan' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Siswa dengan Poin --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Siswa {{ $kelas->nama_kelas }}</h5>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Total Poin</th>
                        <th>Status Pembinaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $index => $siswa)
                        {{-- PERFORMANCE: total_poin already calculated in controller! --}}
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td><strong>{{ $siswa->nama_siswa }}</strong></td>
                            <td>
                                @if($siswa->total_poin > 0)
                                    <span class="badge badge-danger">{{ $siswa->total_poin }} poin</span>
                                @else
                                    <span class="badge badge-success">0 poin</span>
                                @endif
                            </td>
                            <td>
                                @if($siswa->total_poin >= 55)
                                    <span class="badge badge-danger">Perlu Pembinaan</span>
                                @elseif($siswa->total_poin > 0)
                                    <span class="badge badge-warning">Monitoring</span>
                                @else
                                    <span class="badge badge-success">Baik</span>
                                @endif
                            </td>
                            <td>
                                {{-- FIXED: Use ID instead of stdClass object --}}
                                <a href="{{ route('siswa.show', $siswa->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada siswa di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
