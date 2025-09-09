<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    /**
     * Menggunakan guarded kosong berarti semua atribut boleh diisi massal.
     * Ini adalah alternatif dari properti $fillable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'is_anonymous' => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Relasi: Sebuah laporan dimiliki oleh seorang siswa (jika tidak anonim).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi: Sebuah laporan ditangani oleh seorang konselor.
     */
    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    /**
     * Relasi: Sebuah laporan memiliki banyak pesan chat.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}