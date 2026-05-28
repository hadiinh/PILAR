<h1 align="center">PILAR</h1>

<p align="center">
  <b>Portal Informasi & Layanan Administrasi RW</b><br/>
  Aplikasi web manajemen warga untuk <b>RW 016, Kelurahan Melong, Cimahi Selatan</b>.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Tailwind-v4-38BDF8?logo=tailwindcss&logoColor=white" alt="Tailwind v4">
  <img src="https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white" alt="Vite 8">
  <img src="https://img.shields.io/badge/license-MIT-green" alt="MIT License">
</p>

---

## Daftar Isi

- [Tentang Aplikasi](#tentang-aplikasi)
- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Direktori](#struktur-direktori)
- [Role & Akses](#role--akses)
- [Konvensi UI](#konvensi-ui)
- [Pengembangan](#pengembangan)
- [Lisensi](#lisensi)

---

## Tentang Aplikasi

**PILAR** adalah aplikasi web berbasis Laravel yang membantu pengurus RW
mengelola data warga, jadwal kegiatan, keuangan, laporan keluhan, dan
dokumentasi foto — sekaligus menjadi portal informasi yang dapat
diakses warga kapan saja, baik dari ponsel maupun komputer.

Antarmuka sepenuhnya **Bahasa Indonesia** dan dirancang **mobile-first**
mengingat sebagian besar warga mengakses aplikasi dari smartphone.

---

## Fitur

### Untuk Warga

| Modul        | Deskripsi                                                          |
| ------------ | ------------------------------------------------------------------ |
| Beranda      | Ringkasan kegiatan, foto terbaru, saldo kas RW                     |
| Jadwal       | Daftar agenda RW (rapat, kerja bakti, posyandu, dll.)              |
| Kegiatan     | Galeri kegiatan beserta dokumentasi                                |
| Foto         | Album foto kegiatan warga                                          |
| Keuangan     | Laporan transparansi kas RW (pemasukan & pengeluaran)              |
| Laporan      | Membuat & memantau status laporan / aspirasi                       |
| Profil       | Mengubah data diri & mengganti kata sandi                          |
| Pengajuan    | Formulir pendaftaran akun (perlu persetujuan pengurus)             |

### Untuk Pengurus (Ketua RW / Admin)

| Modul                  | Deskripsi                                                  |
| ---------------------- | ---------------------------------------------------------- |
| Dashboard              | Statistik agregat (warga, kas, laporan aktif)              |
| Manajemen Warga        | CRUD warga, aktif/nonaktif akun, reset password            |
| Manajemen Keluarga     | Pengelompokan warga berdasarkan No. KK                     |
| Statistik Warga        | Distribusi usia, jenis kelamin, pekerjaan, pendidikan      |
| Jadwal & Kegiatan      | CRUD agenda & dokumentasi                                  |
| Foto                   | Upload (kompres otomatis) + bulk delete                    |
| Keuangan               | CRUD transaksi pemasukan & pengeluaran                     |
| Laporan                | Memproses & mengubah status laporan warga                  |
| Pengajuan Akun         | Review, approve, reject pendaftaran                        |
| Log Notifikasi         | Memantau pengiriman notifikasi & retry yang gagal          |
| Users                  | Daftar seluruh akun                                        |

### Layanan Pendukung

- **API Proxy Wilayah Indonesia** — provinsi → kota → kecamatan → kelurahan, di-*cache* di sisi server.
- **Rate Limiting** — login `10 req/menit`, pengajuan akun `5 req/menit`.
- **Role Middleware** — `role:ketua_rw,admin` melindungi seluruh rute manajemen.
- **Kompresi Gambar** — Intervention Image otomatis mengoptimasi foto saat diunggah.

---

## Tech Stack

**Backend**
- PHP `^8.3`
- Laravel `^13.8`
- Intervention Image
- SQLite / MySQL (sesuai konfigurasi `.env`)

**Frontend**
- Blade Templates
- Tailwind CSS `v4` (via `@tailwindcss/vite`)
- Vite `^8`
- Chart.js (CDN) untuk grafik keuangan & statistik

**Tooling**
- Laravel Pint (formatter)
- PHPUnit `^12`
- Laravel Pail (log viewer)
- Concurrently (script `composer dev`)

---

## Persyaratan

- PHP `8.3` atau lebih baru, dengan ekstensi standar Laravel (`pdo`, `mbstring`, `openssl`, `gd`, ...)
- Composer `2.x`
- Node.js `18+` dan npm
- Database: SQLite (default), atau MySQL/PostgreSQL

---

## Instalasi

```bash
# 1. Clone repository
git clone <url-repository-anda> pilar
cd pilar

# 2. Install dependency PHP & JS
composer install
npm install

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate

# 4. Siapkan database (default: SQLite)
touch database/database.sqlite
php artisan migrate --seed

# 5. Build asset produksi
npm run build
```

Atau jalankan semua langkah di atas secara otomatis:

```bash
composer setup
```

---

## Menjalankan Aplikasi

**Mode pengembangan** — menjalankan server, queue, log viewer, dan Vite secara paralel:

```bash
composer dev
```

**Atau jalankan terpisah:**

```bash
php artisan serve   # http://127.0.0.1:8000
npm run dev         # Vite dev server (HMR)
```

**Mode produksi:**

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Struktur Direktori

```
PILAR/
├── app/
│   ├── Http/Controllers/   # Auth, Dashboard, Warga, Keluarga,
│   │                       # Keuangan, Laporan, Foto, Jadwal,
│   │                       # Kegiatan, Pengajuan, Notifikasi, ...
│   └── Models/             # User, Foto, Jadwal, Kegiatan,
│                           # Keuangan, Laporan, PengajuanAkun,
│                           # NotifikasiLog
├── resources/
│   └── views/
│       ├── beranda.blade.php
│       ├── dashboard/
│       ├── warga/  keluarga/  pengajuan/  users/
│       ├── jadwal/  kegiatan/  foto/
│       ├── keuangan/  laporan/  statistik/  notifikasi/
│       ├── layouts/        # app (auth), guest (login/register)
│       ├── components/     # <x-stat>, <x-page-header>, <x-button>
│       └── partials/
├── routes/web.php
├── database/migrations/
└── public/
```

---

## Role & Akses

PILAR memiliki tiga peran pengguna:

| Role        | Akses                                                              |
| ----------- | ------------------------------------------------------------------ |
| `user`      | Warga — lihat beranda, jadwal, kegiatan, foto, kas; buat laporan   |
| `ketua_rw`  | Pengurus — akses penuh ke modul manajemen + sidebar Manajemen      |
| `admin`     | Administrator — sama dengan `ketua_rw` (akses CRUD penuh)          |

Rute manajemen dilindungi dengan middleware `role:ketua_rw,admin`.
Pendaftaran akun dilakukan melalui **Pengajuan Akun** dan harus
disetujui pengurus sebelum dapat login.

---

## Konvensi UI

Aplikasi mengikuti pola **mobile-first** dengan Tailwind CSS v4:

- **Tabel** memakai *dual view*: card list di mobile (`md:hidden`),
  tabel di desktop (`hidden md:block`).
- **Stat cards** adaptif dengan komponen `<x-stat>`:
  `p-3 sm:p-5`, `text-lg sm:text-2xl`.
- **Page header** memakai komponen `<x-page-header>`:
  `flex-col sm:flex-row` agar tombol full-width di mobile.
- **Form** memakai grid `grid-cols-1 sm:grid-cols-2 gap-3`.
- **Chart** memakai responsive height (`h-56 sm:h-72`),
  bukan inline `style="height: ..."`.

---

## Pengembangan

```bash
# Format kode PHP
vendor/bin/pint

# Jalankan test
php artisan test

# Lihat log secara real-time
php artisan pail

# Build asset (cek class Tailwind ter-pickup)
npm run build
```

---

## Lisensi

Aplikasi ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  Dibuat untuk warga <b>RW 016 Kelurahan Melong, Cimahi Selatan</b>.
</p>
