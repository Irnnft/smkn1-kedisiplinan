<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Siswa extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * Configure activity log options for Siswa model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_siswa', 'nisn', 'kelas_id', 'wali_murid_user_id'])
            ->useLogName('siswa')
            ->logOnlyDirty();
    }

    /**
     * Nama tabelnya adalah 'siswa', bukan 'siswas'.
     */
    protected $table = 'siswa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kelas_id',
        'wali_murid_user_id',
        'nisn',
        'nama_siswa',
        'nomor_hp_wali_murid',
    ];

    /**
     * Kita memiliki timestamps 'created_at' dan 'updated_at' di tabel ini.
     * Jadi, $timestamps = true (ini default, tidak perlu ditulis).
     */

    // =====================================================================
    // ----------------- DEFINISI RELASI ELOQUENT ------------------
    // =====================================================================

    /**
     * Relasi Wajib: SATU Siswa DIMILIKI OLEH SATU Kelas.
     * (Foreign Key: kelas_id)
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi Opsional: SATU Siswa DIMILIKI OLEH SATU User (Wali Murid).
     * (Foreign Key: wali_murid_user_id)
     */
    public function waliMurid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_murid_user_id');
    }

    /**
     * Relasi Wajib: SATU Siswa MEMILIKI BANYAK Riwayat Pelanggaran.
     * (Foreign Key di tabel 'riwayat_pelanggaran': siswa_id)
     */
    public function riwayatPelanggaran(): HasMany
    {
        // Kita bisa urutkan langsung dari yang terbaru
        return $this->hasMany(RiwayatPelanggaran::class, 'siswa_id')->latest('tanggal_kejadian');
    }

    /**
     * Relasi Wajib: SATU Siswa MEMILIKI BANYAK Kasus Tindak Lanjut.
     * (Foreign Key di tabel 'tindak_lanjut': siswa_id)
     */
    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'siswa_id')->latest();
    }
}