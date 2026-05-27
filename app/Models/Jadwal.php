<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
    'judul',
    'deskripsi',
    'tanggal',
    'jam',
    'lokasi',
    'kategori',
    'status'
];
}
