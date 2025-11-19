<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Master Jenis Pelanggaran</h1>
        <div>
            <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('jenis-pelanggaran.create') }}" class="btn btn-primary">Tambah Aturan Baru</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jenisPelanggaran as $jp)
                    <tr>
                        <td>{{ $jp->nama_pelanggaran }}</td>
                        <td>
                            @if($jp->kategoriPelanggaran->nama_kategori == 'RINGAN')
                                <span class="badge bg-success">RINGAN</span>
                            @elseif($jp->kategoriPelanggaran->nama_kategori == 'SEDANG')
                                <span class="badge bg-warning text-dark">SEDANG</span>
                            @else
                                <span class="badge bg-danger">BERAT</span>
                            @endif
                        </td>
                        <td class="fw-bold text-center">{{ $jp->poin }}</td>
                        <td>
                            <a href="{{ route('jenis-pelanggaran.edit', $jp->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            
                            <form action="{{ route('jenis-pelanggaran.destroy', $jp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aturan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $jenisPelanggaran->links() }}
        </div>
    </div>
</div>

</body>
</html>