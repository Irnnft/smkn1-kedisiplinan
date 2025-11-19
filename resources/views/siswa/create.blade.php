<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tambah Siswa Baru</h4>
        </div>
        <div class="card-body">
            
            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>NISN</label>
                    <input type="text" name="nisn" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_siswa" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kelas</label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nomor HP Orang Tua (WA)</label>
                    <input type="text" name="nomor_hp_ortu" class="form-control">
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Siswa</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>