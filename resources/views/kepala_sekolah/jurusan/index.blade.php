@extends('layouts.app')

@section('title', 'Data Jurusan - Monitoring')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Data Jurusan</h3>
            <p class="text-muted">Overview jurusan dengan statistik pembinaan</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama Jurusan</th>
                        <th>Kode</th>
                        <th>Kaprodi</th>
                        <th>Jumlah Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusanList as $j)
                        {{-- PERFORMANCE: No calculations in loop! --}}
                        <tr>
                            <td><strong>{{ $j->nama_jurusan }}</strong></td>
                            <td><span class="badge badge-secondary">{{ $j->kode_jurusan ?? '-' }}</span></td>
                            <td>{{ $j->kaprodi?->nama ?? '-' }}</td>
                            <td>{{ $j->kelas_count }} kelas</td>
                            <td>{{ $j->siswa_count }} siswa</td>
                            <td>
                                <a href="{{ route('kepala-sekolah.data.jurusan.show', $j) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-chart-bar"></i> Detail & Statistik
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada data jurusan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
