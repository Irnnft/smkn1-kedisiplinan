@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas - ' . $kelas->nama_kelas)

@section('content')

<div class="callout callout-info">
    <h5><i class="fas fa-info"></i> Info Kelas: {{ $kelas->nama_kelas }}</h5>
    <p>Selamat datang di panel manajemen kelas. Gunakan menu di bawah untuk memantau perkembangan siswa Anda.</p>
</div>

<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exclamation-triangle mr-1"></i> 
            Kasus Perlu Ditangani
            <span class="badge badge-warning right ml-2">{{ $kasusBaru->count() }}</span>
        </h3>
    </div>
    <div class="card-body table-responsive p-0">
        @if($kasusBaru->isEmpty())
            <div class="text-center p-4">
                <h5 class="text-success"><i class="fas fa-check-circle"></i> Aman!</h5>
                <p class="text-muted">Tidak ada kasus aktif yang memerlukan tindakan Anda.</p>
            </div>
        @else
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Pemicu Masalah</th>
                        <th>Sanksi Sistem</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kasusBaru as $kasus)
                    <tr>
                        <td class="text-bold">{{ $kasus->siswa->nama_siswa }}</td>
                        <td>{{ Str::limit($kasus->pemicu, 40) }}</td>
                        <td>{{ $kasus->sanksi_deskripsi }}</td>
                        <td>
                            @if($kasus->status == 'Menunggu Persetujuan')
                                <span class="badge badge-danger">Menunggu ACC Kepsek</span>
                            @elseif($kasus->status == 'Baru')
                                <span class="badge badge-warning">Baru</span>
                            @else
                                <span class="badge badge-info">{{ $kasus->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('kasus.edit', $kasus->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-cog"></i> Kelola
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat Pelanggaran Kelas</h3>
        <div class="card-tools">
            <form action="{{ route('dashboard.walikelas') }}" method="GET" class="d-inline-block form-inline">
                <div class="input-group input-group-sm">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control ml-1">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if($riwayatTerbaru->isEmpty())
             <p class="text-center p-4 text-muted">Tidak ada riwayat pelanggaran pada rentang tanggal ini.</p>
        @else
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Pelanggaran</th>
                        <th>Poin</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatTerbaru as $riwayat)
                    <tr>
                        <td>{{ $riwayat->tanggal_kejadian->format('d M Y') }}</td>
                        <td>{{ $riwayat->siswa->nama_siswa }}</td>
                        <td>{{ $riwayat->jenisPelanggaran->nama_pelanggaran }}</td>
                        <td><span class="badge badge-danger">+{{ $riwayat->jenisPelanggaran->poin }}</span></td>
                        <td>
                            @if($riwayat->bukti_foto_path)
                                <a href="{{ asset('storage/' . $riwayat->bukti_foto_path) }}" target="_blank" class="text-muted"><i class="fas fa-image"></i></a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection