@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('styles')
    <!-- Load CSS Eksternal -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="{{ asset('css/pages/siswa-create.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- HEADER -->
    <div class="row mb-3 pt-2">
        <div class="col-sm-6">
            <h4 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-user-plus text-primary mr-2"></i> Tambah Siswa Baru
            </h4>
            <p class="text-muted small mb-0">Pastikan NISN valid dan belum terdaftar sebelumnya.</p>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary btn-sm border rounded">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Siswa
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card card-primary card-outline shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold text-dark">Formulir Data Siswa</h3>
                </div>
                
                <form action="{{ route('siswa.store') }}" method="POST">
                    @csrf
                    <div class="card-body bg-light">
                        
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">NISN <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white"><i class="fas fa-id-card text-secondary"></i></span>
                                        </div>
                                        <input type="text" name="nisn" class="form-control form-control-clean @error('nisn') is-invalid @enderror" 
                                               placeholder="Nomor Induk Siswa Nasional" value="{{ old('nisn') }}" required>
                                    </div>
                                    @error('nisn') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white"><i class="fas fa-user text-secondary"></i></span>
                                        </div>
                                        <input type="text" name="nama_siswa" class="form-control form-control-clean @error('nama_siswa') is-invalid @enderror" 
                                               placeholder="Sesuai Ijazah/Rapor" value="{{ old('nama_siswa') }}" required>
                                    </div>
                                    @error('nama_siswa') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Kelas <span class="text-danger">*</span></label>
                                    <!-- data-placeholder spesifik untuk Kelas -->
                                    <select name="kelas_id" class="form-control select2 @error('kelas_id') is-invalid @enderror" required data-placeholder="-- Pilih Kelas --">
                                        <option value=""></option> <!-- Placeholder kosong -->
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-primary">Nomor HP Wali Murid (WA)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-primary text-success"><i class="fab fa-whatsapp"></i></span>
                                        </div>
                                        <input type="text" name="nomor_hp_ortu" class="form-control form-control-clean border-primary" 
                                               placeholder="Contoh: 081234567890" value="{{ old('nomor_hp_ortu') }}">
                                    </div>
                                    <small class="text-muted font-italic">Wajib diisi untuk fitur notifikasi otomatis.</small>
                                    @error('nomor_hp_ortu') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label class="form-label text-muted">Akun Login Wali Murid (Opsional)</label>
                            
                            <!-- data-placeholder spesifik untuk Wali Murid -->
                            <select name="orang_tua_user_id" class="form-control select2" data-placeholder="-- Cari Nama Wali Murid --">
                                <option value=""></option> <!-- Placeholder kosong -->
                                @foreach($orangTua as $ortu)
                                    <option value="{{ $ortu->id }}" {{ old('orang_tua_user_id') == $ortu->id ? 'selected' : '' }}>
                                        {{ $ortu->nama }} ({{ $ortu->username }})
                                    </option>
                                @endforeach
                            </select>
                            
                            <small class="text-muted font-italic">
                                <i class="fas fa-info-circle"></i> Pilih jika akun Wali Murid sudah dibuat sebelumnya di menu Manajemen User.
                            </small>
                        </div>

                    </div>

                    <div class="card-footer bg-white d-flex justify-content-end py-3">
                        <a href="{{ route('siswa.index') }}" class="btn btn-default mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Load Logic Eksternal -->
    <script src="{{ asset('js/pages/siswa-create.js') }}"></script>
@endpush