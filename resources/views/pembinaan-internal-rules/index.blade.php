@extends('layouts.app')

@section('title', 'Kelola Pembinaan Internal')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-user-check mr-2"></i> Kelola Aturan Pembinaan Internal</h4>
            <p class="text-muted">
                Atur threshold pembinaan internal berdasarkan <strong>akumulasi poin</strong> siswa.
                <br><strong>Catatan Penting:</strong> Pembinaan internal adalah <strong>rekomendasi konseling</strong>, TIDAK trigger surat pemanggilan otomatis.
                <br><strong>Surat pemanggilan</strong> hanya trigger dari pelanggaran dengan sanksi "Panggilan orang tua" (diatur di Frequency Rules).
            </p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Button Tambah Rule -->
    <div class="row mb-3">
        <div class="col-12 text-right">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambahRule">
                <i class="fas fa-plus-circle"></i> Tambah Aturan Baru
            </button>
        </div>
    </div>

    <!-- Table Rules -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Range Poin</th>
                        <th width="30%">Pembina yang Terlibat</th>
                        <th width="35%">Keterangan</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td>{{ $rule->display_order }}</td>
                        <td>
                            <span class="badge badge-primary badge-lg">
                                {{ $rule->getRangeText() }}
                            </span>
                        </td>
                        <td>
                            @foreach($rule->pembina_roles as $role)
                                <span class="badge badge-info">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td>{{ $rule->keterangan }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning" 
                                    data-toggle="modal" 
                                    data-target="#modalEditRule{{ $rule->id }}"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('pembinaan-internal-rules.destroy', $rule->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus aturan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Rule -->
                    <div class="modal fade" id="modalEditRule{{ $rule->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form action="{{ route('pembinaan-internal-rules.update', $rule->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Aturan Pembinaan Internal</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @include('pembinaan-internal-rules.partials.form', ['rule' => $rule])
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada aturan pembinaan internal. Klik "Tambah Aturan Baru" untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Box -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Cara Kerja Pembinaan Internal
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Sistem menghitung <strong>total poin akumulasi</strong> dari semua pelanggaran siswa</li>
                        <li>Berdasarkan total poin, sistem memberikan <strong>rekomendasi pembina</strong> yang perlu terlibat</li>
                        <li>Pembinaan dilakukan secara <strong>internal</strong> (konseling, monitoring, evaluasi)</li>
                        <li><strong>TIDAK</strong> trigger surat pemanggilan otomatis ke orang tua</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle"></i> Perbedaan dengan Frequency Rules
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="50%"><strong>Frequency Rules:</strong></td>
                            <td width="50%"><strong>Pembinaan Internal:</strong></td>
                        </tr>
                        <tr>
                            <td>Berdasarkan <em>frekuensi</em> pelanggaran</td>
                            <td>Berdasarkan <em>akumulasi poin</em></td>
                        </tr>
                        <tr>
                            <td>Trigger surat pemanggilan</td>
                            <td>Rekomendasi konseling</td>
                        </tr>
                        <tr>
                            <td>Melibatkan orang tua</td>
                            <td>Internal sekolah</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Rule -->
<div class="modal fade" id="modalTambahRule" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('pembinaan-internal-rules.store') }}" method="POST" id="formTambahRule">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aturan Pembinaan Internal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Range Poin -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_poin_min">Poin Minimum <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('poin_min') is-invalid @enderror" 
                                       id="new_poin_min" 
                                       name="poin_min" 
                                       value="{{ old('poin_min', $suggestedPoinMin) }}" 
                                       min="0"
                                       required>
                                @error('poin_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-success">
                                    <i class="fas fa-lightbulb"></i> Rekomendasi: {{ $suggestedPoinMin }} (berdasarkan aturan yang ada)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_poin_max">Poin Maximum</label>
                                <input type="number" 
                                       class="form-control @error('poin_max') is-invalid @enderror" 
                                       id="new_poin_max" 
                                       name="poin_max" 
                                       value="{{ old('poin_max') }}" 
                                       min="0">
                                @error('poin_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Batas atas range poin. Kosongkan untuk open-ended</small>
                            </div>
                        </div>
                    </div>

                    <!-- Pembina Roles -->
                    <div class="form-group">
                        <label>Pembina yang Terlibat <span class="text-danger">*</span></label>
                        <div class="border rounded p-3">
                            @php
                                $availableRoles = ['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan', 'Kepala Sekolah'];
                                $selectedRoles = old('pembina_roles', []);
                            @endphp
                            
                            @foreach($availableRoles as $role)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input new-pembina-checkbox" 
                                       id="new_pembina_{{ str_replace(' ', '_', $role) }}" 
                                       name="pembina_roles[]" 
                                       value="{{ $role }}"
                                       {{ in_array($role, $selectedRoles) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="new_pembina_{{ str_replace(' ', '_', $role) }}">
                                    {{ $role }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('pembina_roles')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Pilih minimal 1 pembina yang akan terlibat dalam pembinaan</small>
                    </div>

                    <!-- Keterangan -->
                    <div class="form-group">
                        <label for="new_keterangan">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="new_keterangan" 
                                  name="keterangan" 
                                  rows="3" 
                                  maxlength="500"
                                  required>{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Deskripsi jenis pembinaan (contoh: Pembinaan ringan, konseling)</small>
                    </div>

                    <!-- Display Order -->
                    <div class="form-group">
                        <label for="new_display_order">Urutan Tampilan</label>
                        <input type="number" 
                               class="form-control @error('display_order') is-invalid @enderror" 
                               id="new_display_order" 
                               name="display_order" 
                               value="{{ old('display_order', $suggestedDisplayOrder) }}" 
                               min="1">
                        @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-success">
                            <i class="fas fa-lightbulb"></i> Rekomendasi: {{ $suggestedDisplayOrder }} (urutan berikutnya)
                        </small>
                    </div>

                    <!-- Example Box -->
                    <div class="alert alert-info">
                        <strong><i class="fas fa-lightbulb"></i> Contoh:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>0-50 poin:</strong> Wali Kelas → Pembinaan ringan, konseling</li>
                            <li><strong>55-100 poin:</strong> Wali Kelas + Kaprodi → Pembinaan sedang, monitoring ketat</li>
                            <li><strong>105-300 poin:</strong> Wali Kelas + Kaprodi + Waka → Pembinaan intensif, evaluasi berkala</li>
                            <li><strong>305+ poin:</strong> Semua pembina → Pembinaan kritis, pertemuan dengan orang tua</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-dismiss alert after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Reset form when create modal is closed
    $('#modalTambahRule').on('hidden.bs.modal', function () {
        // Reset form to default values
        $('#formTambahRule')[0].reset();
        
        // Reset to suggested values
        $('#new_poin_min').val('{{ $suggestedPoinMin }}');
        $('#new_display_order').val('{{ $suggestedDisplayOrder }}');
        
        // Uncheck all checkboxes
        $('.new-pembina-checkbox').prop('checked', false);
        
        // Clear textarea
        $('#new_keterangan').val('');
    });
});
</script>
@endpush
@endsection
