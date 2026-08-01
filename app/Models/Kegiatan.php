<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Kegiatan extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'deskripsi',
        'gambar',
        'tanggal',
    ];

    /**
     * Status dihitung otomatis dari tanggal kegiatan (bukan kolom database):
     * akan-datang / berlangsung / selesai.
     */
    public function getStatusAttribute(): string
    {
        $today = Carbon::today();
        $tgl = $this->tanggal ? Carbon::parse($this->tanggal) : $today;

        if ($tgl->gt($today)) {
            return 'akan-datang';
        }
        if ($tgl->isSameDay($today)) {
            return 'berlangsung';
        }
        return 'selesai';
    }

    public static function statusLabels(): array
    {
        return [
            'akan-datang' => 'Akan Datang',
            'berlangsung' => 'Sedang Berjalan',
            'selesai'     => 'Selesai',
        ];
    }

    public static function statusTones(): array
    {
        return [
            'akan-datang' => 'info',
            'berlangsung' => 'success',
            'selesai'     => 'neutral',
        ];
    }
}
