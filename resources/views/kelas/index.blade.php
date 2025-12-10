@extends('layouts.app')

@section('title', 'Kelola Kelas')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Kelola Kelas</h3>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(auth()->user()->hasRole('Operator Sekolah'))
    <div class="mb-3">
        <a href="{{ route('kelas.create') }}" class="btn btn-primary">Tambah Kelas</a>
    </div>
    @endif

    @if(session('wali_created'))
        @php $w = session('wali_created'); @endphp
        <div class="alert alert-info">
            Akun Wali Kelas otomatis telah dibuat: <strong>{{ $w['username'] }}</strong>
            <br>Password (sampel): <strong>{{ $w['password'] }}</strong>
            <br>Pastikan untuk menyampaikan kredensial ini kepada pemegang akun dan menyarankan perubahan password setelah login.
        </div>
    @endif

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
                        <tr>
                            <td>{{ $k->nama_kelas }}</td>
                            <td>{{ $k->jurusan?->nama_jurusan ?? '-' }}</td>
                            <td>{{ $k->waliKelas?->username ?? '-' }}</td>
                            <td>{{ $k->siswa()->count() }}</td>
                            <td>
                                <a href="{{ route('kelas.show', $k) }}" class="btn btn-sm btn-info">Detail</a>
                                @if(auth()->user()->hasRole('Operator Sekolah'))
                                <a href="{{ route('kelas.edit', $k) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('kelas.destroy', $k) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kelas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Belum ada data kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
