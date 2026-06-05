<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const KATEGORI_ANAK    = 'anak';
    public const KATEGORI_REMAJA  = 'remaja';
    public const KATEGORI_DEWASA  = 'dewasa';
    public const KATEGORI_LANSIA  = 'lansia';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nik',
        'no_kk',
        'no_hp',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'status_keluarga',
        'is_kepala_keluarga',
        'pekerjaan',
        'akun_aktif',
        'status_warga',
        'rt',
        'rw',
        'no_rumah',
        'alamat_detail',
        'kode_pos',
        'provinsi_id',
        'provinsi_nama',
        'kota_id',
        'kota_nama',
        'kecamatan_id',
        'kecamatan_nama',
        'kelurahan_id',
        'kelurahan_nama',
        'notif_wa_aktif',
        'must_change_password',
        'password_changed_at',
        'session_version',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'notif_wa_aktif'     => 'boolean',
            'tanggal_lahir'      => 'date',
            'is_kepala_keluarga' => 'boolean',
            'akun_aktif'         => 'boolean',
            'must_change_password' => 'boolean',
            'password_changed_at'  => 'datetime',
            'session_version'      => 'integer',
        ];
    }

    /**
     * Naikkan session_version untuk mem-paksa logout semua sesi aktif
     * milik user ini di request berikutnya (via CheckAccountActive).
     */
    public function incrementSessionVersion(): void
    {
        $this->increment('session_version');
    }

    /* ===== Helpers ===== */

    public function bisaTerimaWa(): bool
    {
        return $this->notif_wa_aktif && !empty($this->no_hp);
    }

    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->alamat_detail,
            $this->no_rumah ? 'No. ' . $this->no_rumah : null,
            $this->rt ? 'RT ' . str_pad($this->rt, 2, '0', STR_PAD_LEFT) : null,
            $this->rw ? 'RW ' . str_pad($this->rw, 3, '0', STR_PAD_LEFT) : null,
            $this->kelurahan_nama,
            $this->kecamatan_nama,
            $this->kota_nama,
            $this->provinsi_nama,
            $this->kode_pos,
        ]);
        return implode(', ', $parts);
    }

    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir
            ? $this->tanggal_lahir->age
            : null;
    }

    /**
     * Kategori umur otomatis berdasar tanggal lahir.
     * - anak    : 0-12
     * - remaja  : 13-17
     * - dewasa  : 18-59
     * - lansia  : 60+
     */
    public function getKategoriUmurAttribute(): ?string
    {
        $umur = $this->umur;
        if ($umur === null) return null;

        return match (true) {
            $umur <= 12 => self::KATEGORI_ANAK,
            $umur <= 17 => self::KATEGORI_REMAJA,
            $umur <= 59 => self::KATEGORI_DEWASA,
            default     => self::KATEGORI_LANSIA,
        };
    }

    public function getKategoriUmurLabelAttribute(): ?string
    {
        return match ($this->kategori_umur) {
            self::KATEGORI_ANAK    => 'Anak',
            self::KATEGORI_REMAJA  => 'Remaja',
            self::KATEGORI_DEWASA  => 'Dewasa',
            self::KATEGORI_LANSIA  => 'Lansia',
            default                => null,
        };
    }

    /* ===== Relasi keluarga via no_kk ===== */

    public function anggotaKeluarga(): HasMany
    {
        return $this->hasMany(self::class, 'no_kk', 'no_kk');
    }

    /* ===== Scopes ===== */

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('akun_aktif', true);
    }

    public function scopeWarga(Builder $q): Builder
    {
        return $q->where('role', 'user');
    }

    public function scopePengurus(Builder $q): Builder
    {
        return $q->whereIn('role', ['admin', 'ketua_rw']);
    }
}
