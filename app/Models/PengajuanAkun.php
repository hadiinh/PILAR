<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanAkun extends Model
{
    protected $fillable = [
        'nik',
        'no_hp',
        'password_hash',
        'status',
        'alasan_tolak',
        'user_id',
        'processed_by',
        'processed_at',
        'ip_address',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    protected $hidden = ['password_hash'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }
}
