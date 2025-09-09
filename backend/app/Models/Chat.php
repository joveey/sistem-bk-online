<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Chat extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'sender_id',
        'sender_type',
        'message',
    ];

    /**
     * Relasi: Sebuah pesan chat milik sebuah laporan.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Relasi Polimorfik: Pengirim pesan bisa jadi seorang Student atau User (Konselor).
     * Laravel akan otomatis mencari model berdasarkan isi kolom 'sender_type'.
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }
}