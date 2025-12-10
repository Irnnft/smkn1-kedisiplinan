@extends('layouts.app')

@section('title', 'Kelola Jurusan')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Kelola Jurusan</h3>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(auth()->user()->hasRole('Operator Sekolah'))
    <div class="mb-3">
        <a href="{{ route('jurusan.create') }}" class="btn btn-primary">Tambah Jurusan</a>
    </div>
    @endif

    @if(session('kaprodi_created'))
        @php $c = session('kaprodi_created'); @endphp
        <div class="alert alert-info">
            Akun Kaprodi otomatis telah dibuat: <strong>{{ $c['username'] }}</strong>
            <br>Password (sampel): <strong>{{ $c['password'] }}</strong>
            <br>Pastikan untuk menyampaikan kredensial ini kepada pemegang akun dan menyarankan perubahan password setelah login.
        </div>
    @endif

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
                        <tr>
                            <td>{{ $j->nama_jurusan }}</td>
                            <td>{{ $j->kode_jurusan ?? '-' }}</td>
                            <td>{{ $j->kaprodi?->username ?? '-' }}</td>
                            <td>{{ $j->kelas_count }}</td>
                            <td>{{ $j->siswa_count }}</td>
                            <td>
                                <a href="{{ route('jurusan.show', $j) }}" class="btn btn-sm btn-info">Lihat</a>
                                @if(auth()->user()->hasRole('Operator Sekolah'))
                                <a href="{{ route('jurusan.edit', $j) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('jurusan.destroy', $j) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus jurusan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                                @endif
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
