<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPelanggaran extends Model
{
    use HasFactory;

    /**
     * Beri tahu Laravel bahwa tabel ini tidak punya timestamps.
     */
    public $timestamps = false;

    /**
     * Nama tabelnya adalah 'kategori_pelanggaran'.
     */
    protected $table = 'kategori_pelanggaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kategori',
    ];

    // =====================================================================
    // ----------------- DEFINISI RELASI ELOQUENT ------------------
    // =====================================================================

    /**
     * Relasi Wajib: SATU Kategori MEMILIKI BANYAK JenisPelanggaran.
     * (Foreign Key di tabel 'jenis_pelanggaran': kategori_id)
     */
    public function jenisPelanggaran(): HasMany
    {
        return $this->hasMany(JenisPelanggaran::class, 'kategori_id');
    }
}