@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">⚙️ Pengaturan Rules Engine</h1>
            <p class="text-muted mb-0">Kelola threshold poin dan frekuensi pelanggaran</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#helpModal">
                <i class="bi bi-question-circle"></i> Bantuan
            </button>
            <button type="button" class="btn btn-warning" onclick="confirmResetAll()">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Semua
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form id="settingsForm" method="POST" action="{{ route('rules-engine-settings.update') }}">
        @csrf

        <div class="row">
            <!-- Threshold Poin Surat -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Threshold Poin Surat</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Atur batas poin untuk memicu surat pemanggilan berdasarkan tingkat pelanggaran</p>
                        
                        @foreach($settings['threshold_poin'] ?? [] as $setting)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $setting['label'] }}</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error($setting['key']) is-invalid @enderror" 
                                       name="{{ $setting['key'] }}" 
                                       value="{{ old($setting['key'], $setting['value']) }}"
                                       min="1" 
                                       max="10000"
                                       required>
                                <span class="input-group-text">poin</span>
                                <button type="button" class="btn btn-outline-secondary" onclick="showHistory('{{ $setting['key'] }}')">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            </div>
                            <small class="text-muted">{{ $setting['description'] }}</small>
                            @error($setting['key'])
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Threshold Akumulasi -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-stack"></i> Threshold Akumulasi Poin</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Atur batas total akumulasi poin untuk eskalasi otomatis</p>
                        
                        @foreach($settings['threshold_akumulasi'] ?? [] as $setting)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $setting['label'] }}</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error($setting['key']) is-invalid @enderror" 
                                       name="{{ $setting['key'] }}" 
                                       value="{{ old($setting['key'], $setting['value']) }}"
                                       min="1" 
                                       max="10000"
                                       required>
                                <span class="input-group-text">poin</span>
                                <button type="button" class="btn btn-outline-secondary" onclick="showHistory('{{ $setting['key'] }}')">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            </div>
                            <small class="text-muted">{{ $setting['description'] }}</small>
                            @error($setting['key'])
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Threshold Frekuensi -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Threshold Frekuensi Pelanggaran</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Atur jumlah pelanggaran berulang yang memicu surat pemanggilan</p>
                        
                        <div class="row">
                            @foreach($settings['threshold_frekuensi'] ?? [] as $setting)
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ $setting['label'] }}</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error($setting['key']) is-invalid @enderror" 
                                           name="{{ $setting['key'] }}" 
                                           value="{{ old($setting['key'], $setting['value']) }}"
                                           min="1" 
                                           max="100"
                                           required>
                                    <span class="input-group-text">kali</span>
                                    <button type="button" class="btn btn-outline-secondary" onclick="showHistory('{{ $setting['key'] }}')">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                </div>
                                <small class="text-muted">{{ $setting['description'] }}</small>
                                @error($setting['key'])
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-info" onclick="previewChanges()">
                            <i class="bi bi-eye"></i> Preview Perubahan
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Perubahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Perubahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Lanjutkan Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-question-circle"></i> Panduan Pengaturan Rules Engine</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold">📋 Threshold Poin Surat</h6>
                <ul>
                    <li><strong>Surat 2 Min/Max:</strong> Range poin untuk pelanggaran berat (Surat 2)</li>
                    <li><strong>Surat 3 Min:</strong> Poin minimum untuk pelanggaran sangat berat (Surat 3)</li>
                </ul>

                <h6 class="fw-bold mt-3">📊 Threshold Akumulasi</h6>
                <ul>
                    <li><strong>Akumulasi Sedang:</strong> Total poin yang memicu eskalasi ke Surat 2</li>
                    <li><strong>Akumulasi Kritis:</strong> Total poin yang memicu Surat 3 (perlu persetujuan)</li>
                </ul>

                <h6 class="fw-bold mt-3">🔄 Threshold Frekuensi</h6>
                <ul>
                    <li><strong>Atribut:</strong> Jumlah pelanggaran atribut berulang yang memicu Surat 1</li>
                    <li><strong>Alfa:</strong> Jumlah pelanggaran alfa berulang yang memicu Surat 1</li>
                </ul>

                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i> <strong>Perhatian:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pastikan nilai minimum selalu lebih kecil dari maksimum</li>
                        <li>Perubahan akan langsung mempengaruhi evaluasi pelanggaran baru</li>
                        <li>Gunakan tombol "Preview" untuk melihat dampak perubahan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showHistory(key) {
    const modal = new bootstrap.Modal(document.getElementById('historyModal'));
    modal.show();
    
    fetch(`/rules-engine-settings/${key}/history`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Waktu</th><th>Nilai Lama</th><th>Nilai Baru</th><th>Diubah Oleh</th></tr></thead><tbody>';
                
                data.data.forEach(item => {
                    const date = new Date(item.created_at).toLocaleString('id-ID');
                    const user = item.user ? item.user.username : 'System';
                    html += `<tr>
                        <td>${date}</td>
                        <td><span class="badge bg-secondary">${item.old_value}</span></td>
                        <td><span class="badge bg-primary">${item.new_value}</span></td>
                        <td>${user}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                document.getElementById('historyContent').innerHTML = html;
            } else {
                document.getElementById('historyContent').innerHTML = 
                    '<div class="alert alert-info">Belum ada riwayat perubahan</div>';
            }
        })
        .catch(error => {
            document.getElementById('historyContent').innerHTML = 
                '<div class="alert alert-danger">Gagal memuat riwayat</div>';
        });
}

function previewChanges() {
    const formData = new FormData(document.getElementById('settingsForm'));
    const data = Object.fromEntries(formData.entries());
    
    fetch('/rules-engine-settings/preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            let html = '';
            
            if (result.hasErrors) {
                html += '<div class="alert alert-danger"><strong>⚠️ Terdapat Error Validasi:</strong><ul class="mb-0 mt-2">';
                Object.values(result.errors).forEach(error => {
                    html += `<li>${error}</li>`;
                });
                html += '</ul></div>';
            }
            
            html += '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Setting</th><th>Nilai Saat Ini</th><th>Nilai Baru</th><th>Status</th></tr></thead><tbody>';
            
            result.comparison.forEach(item => {
                const badge = item.changed ? 
                    '<span class="badge bg-warning">Berubah</span>' : 
                    '<span class="badge bg-secondary">Tidak Berubah</span>';
                    
                html += `<tr class="${item.changed ? 'table-warning' : ''}">
                    <td>${item.label}</td>
                    <td><strong>${item.current}</strong></td>
                    <td><strong>${item.proposed}</strong></td>
                    <td>${badge}</td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            
            document.getElementById('previewContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
    })
    .catch(error => {
        alert('Gagal memuat preview');
    });
}

function submitForm() {
    document.getElementById('settingsForm').submit();
}

function confirmResetAll() {
    if (confirm('Apakah Anda yakin ingin mereset SEMUA pengaturan ke nilai default?\n\nTindakan ini tidak dapat dibatalkan!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("rules-engine-settings.reset-all") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
