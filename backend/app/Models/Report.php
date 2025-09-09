<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    /**
     * Relasi: Sebuah laporan memiliki banyak pesan chat.
     * Pastikan ini mengarah ke `Chat::class`.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class); // <-- PERBAIKAN UTAMA DI SINI
    }
}