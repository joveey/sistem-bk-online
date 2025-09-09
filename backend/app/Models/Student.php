<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// Ganti 'Model' dengan 'Authenticatable' agar bisa login dan membuat token
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Tambahkan ini untuk mengaktifkan fungsionalitas token Sanctum
use Laravel\Sanctum\HasApiTokens;

// Ganti 'extends Model' menjadi 'extends Authenticatable'
class Student extends Authenticatable
{
    // Tambahkan trait HasApiTokens di sini
    use HasApiTokens, HasFactory;

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_code',
        'name',
        'class',
    ];

    /**
     * Relasi: Seorang siswa bisa memiliki banyak laporan.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}

