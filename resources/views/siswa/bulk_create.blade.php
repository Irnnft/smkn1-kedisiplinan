@extends('layouts.app')

@section('title', 'Tambah Banyak Siswa')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Tambah Banyak Siswa Sekaligus</h3>
            <p class="text-muted small">Gunakan form ini untuk menambahkan banyak siswa ke satu kelas sekaligus.
            Format per baris: <code>NISN;Nama Lengkap;NomorHPWali (opsional)</code>.
            Contoh: <code>1234567890;Budi Santoso;081234567890</code></p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('siswa.bulk-store') }}" method="POST" enctype="multipart/form-data" id="bulkCreateForm">
                @csrf

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('bulk_errors'))
                    <div class="alert alert-warning">
                        <strong>Beberapa baris bermasalah:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('bulk_errors') as $be)
                                <li>{{ $be }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label class="font-weight-bold">Kelas tujuan <span class="text-danger">*</span></label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach(App\Models\Kelas::orderBy('nama_kelas')->get() as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Daftar siswa (tabel dapat di-copy/paste dari spreadsheet)</label>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="bulkTable">
                            <thead>
                                <tr>
                                    <th style="width:160px">NISN</th>
                                    <th>Nama Lengkap</th>
                                    <th style="width:180px">Nomor HP Wali (opsional)</th>
                                    <th style="width:80px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $initial = old('bulk_rows') ?? 5; @endphp
                                @for($i = 0; $i < $initial; $i++)
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm bulk-nisn" value=""></td>
                                    <td><input type="text" class="form-control form-control-sm bulk-nama" value=""></td>
                                    <td><input type="text" class="form-control form-control-sm bulk-hp" value=""></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-2">
                        <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-primary">Tambah Baris</button>
                        <button type="button" id="pasteHintBtn" class="btn btn-sm btn-outline-secondary">Paste dari Spreadsheet (Ctrl+V di sel pertama)</button>
                    </div>
                    <small class="text-muted d-block mt-1">Anda bisa menyalin baris dari Excel/Sheets dan menempelkannya ke kolom NISN (sel pertama). Sistem akan mencoba mem-parse NISN, Nama, dan Nomor HP dari data yang ditempel.</small>
                    <textarea name="bulk_data" id="bulk_data" class="d-none">{{ old('bulk_data') }}</textarea>
                </div>

                <div class="form-group mt-2">
                    <label class="font-weight-bold">Atau: Unggah file (CSV / XLSX)</label>
                    <input type="file" name="bulk_file" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="form-control-file">
                    <small class="text-muted d-block mt-1">File CSV atau XLSX dengan kolom: NISN, Nama Lengkap, NomorHP (kolom ketiga opsional). Jika ada file, konten textarea akan diabaikan. Format Excel akan otomatis dikonversi.</small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="create_wali_all" name="create_wali_all" value="1">
                        <label class="custom-control-label" for="create_wali_all">Buat akun Wali Murid untuk setiap siswa (jika belum ada)</label>
                    </div>
                    <small class="text-muted d-block mt-1">Jika dicentang, sistem akan mencoba membuat akun `wali.{NISN}` untuk setiap baris yang memiliki NISN. Kredensial akan ditampilkan setelah proses selesai.</small>
                </div>

                <div id="bulk-preview" class="mb-3 d-none">
                    <strong>Preview (contoh username):</strong>
                    <div class="mt-2"><code id="bulk-preview-sample">-</code></div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('siswa.index') }}" class="btn btn-default mr-2">Batal</a>
                    <button class="btn btn-primary" id="bulkSubmitBtn">Proses Tambah Banyak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/siswa/bulk_create.js') }}"></script>
@endpush
