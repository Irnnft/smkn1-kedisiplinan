@extends('layouts.app')

@section('title', 'Data Kelas - Monitoring')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Data Kelas</h3>
            <p class="text-muted">Overview kelas dengan statistik pembinaan</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelasList as $k)
                        {{-- PERFORMANCE: No calculations in loop! --}}
                        <tr>
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td>
                                <span class="badge badge-primary">{{ $k->jurusan?->nama_jurusan ?? '-' }}</span>
                            </td>
                            <td>{{ $k->waliKelas?->nama ?? '-' }}</td>
                            <td>{{ $k->siswa->count() }} siswa</td>
                            <td>
                                <a href="{{ route('kepala-sekolah.data.kelas.show', $k) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-chart-bar"></i> Detail & Statistik
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada data kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
