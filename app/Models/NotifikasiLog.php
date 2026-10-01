<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiLog extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_tujuan',
        'jenis',
        'pesan',
        'status',
        'error',
        'retry_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jenisLabel(): string
    {
        return match ($this->jenis) {
            'jadwal_baru'          => 'Jadwal Baru',
            'kegiatan_baru'        => 'Kegiatan / Pengumuman',
            'laporan_baru'         => 'Laporan Masuk',
            'laporan_status'       => 'Status Laporan',
            'pengajuan_disetujui'  => 'Pengajuan Disetujui',
            'pengajuan_ditolak'    => 'Pengajuan Ditolak',
            'akun_warga_baru'      => 'Akun Warga Baru',
            'akun_dinonaktifkan'   => 'Akun Dinonaktifkan',
            'akun_diaktifkan'      => 'Akun Diaktifkan',
            'akun_dihapus'         => 'Akun Dihapus',
            'reset_password_admin' => 'Reset Kata Sandi (Admin)',
            'password_diubah'      => 'Kata Sandi Diubah',
            'keuangan_baru'        => 'Keuangan Baru',
            'manual'               => 'Manual',
            default                => ucwords(str_replace('_', ' ', $this->jenis)),
        };
    }
}
