<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelanggaran extends Model
{
    use HasFactory;

    /**
     * Beri tahu Laravel bahwa tabel ini tidak punya timestamps.
     */
    public $timestamps = false;

    /**
     * Nama tabelnya adalah 'jenis_pelanggaran'.
     */
    protected $table = 'jenis_pelanggaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kategori_id',
        'nama_pelanggaran',
        'poin',
    ];

    // =====================================================================
    // ----------------- DEFINISI RELASI ELOQUENT ------------------
    // =====================================================================

    /**
     * Relasi Wajib: SATU JenisPelanggaran DIMILIKI OLEH SATU KategoriPelanggaran.
     * (Foreign Key: kategori_id)
     */
    public function kategoriPelanggaran(): BelongsTo
    {
        return $this->belongsTo(KategoriPelanggaran::class, 'kategori_id');
    }

    /**
     * Relasi Wajib: SATU JenisPelanggaran TELAH TERCATAT BANYAK KALI di RiwayatPelanggaran.
     * (Foreign Key di tabel 'riwayat_pelanggaran': jenis_pelanggaran_id)
     */
    public function riwayatPelanggaran(): HasMany
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'jenis_pelanggaran_id');
    }
}