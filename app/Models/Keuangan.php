<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    protected $fillable = [
        'judul',
        'tipe',
        'jumlah',
        'deskripsi',
        'tanggal'
    ];
}