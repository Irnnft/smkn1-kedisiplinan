@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')

<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Siswa</span>
                <span class="info-box-number">{{ $totalSiswa }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pelanggaran (Bulan Ini)</span>
                <span class="info-box-number">{{ $pelanggaranBulanIni }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
            <span class="info-box-icon elevation-1 {{ $listPersetujuan->count() > 0 ? 'bg-danger' : 'bg-success' }}">
                <i class="fas {{ $listPersetujuan->count() > 0 ? 'fa-bell' : 'fa-check' }}"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Menunggu Persetujuan</span>
                <span class="info-box-number">{{ $listPersetujuan->count() }} Dokumen</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-outline {{ $listPersetujuan->count() > 0 ? 'card-danger' : 'card-success' }}">
            <div class="card-header">
                <h3 class="card-title">
                    @if($listPersetujuan->count() > 0)
                        <i class="fas fa-file-signature mr-1"></i> Dokumen Menunggu Tanda Tangan / Persetujuan
                    @else
                        <i class="fas fa-check-circle mr-1"></i> Status Persetujuan
                    @endif
                </h3>
            </div>
            
            <div class="card-body table-responsive p-0">
                @if($listPersetujuan->isEmpty())
                    <div class="text-center p-5">
                        <h2 class="text-success"><i class="fas fa-clipboard-check"></i></h2>
                        <h5>Tidak ada dokumen yang memerlukan persetujuan saat ini.</h5>
                        <p class="text-muted">Semua kasus berat telah ditangani.</p>
                    </div>
                @else
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Pelanggaran Berat</th>
                                <th>Rekomendasi Sanksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listPersetujuan as $kasus)
                            <tr>
                                <td>{{ $kasus->created_at->format('d M Y') }}</td>
                                <td>
                                    <strong>{{ $kasus->siswa->nama_siswa }}</strong><br>
                                    <small class="text-muted">{{ $kasus->siswa->kelas->nama_kelas }}</small>
                                </td>
                                <td>{{ $kasus->pemicu }}</td>
                                <td>
                                    <span class="text-danger font-weight-bold">
                                        {{ $kasus->sanksi_deskripsi }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('kasus.edit', $kasus->id) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-pen-alt"></i> Tinjau & Setujui
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection