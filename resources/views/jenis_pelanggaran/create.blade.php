<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Aturan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Tambah Jenis Pelanggaran</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('jenis-pelanggaran.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Nama Pelanggaran</label>
                    <input type="text" name="nama_pelanggaran" class="form-control" required placeholder="Misal: Tidur di kelas">
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        @foreach($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Bobot Poin</label>
                    <input type="number" name="poin" class="form-control" required min="0">
                </div>
                <button class="btn btn-primary w-100">Simpan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>