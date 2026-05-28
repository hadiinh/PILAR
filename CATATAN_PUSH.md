# CATATAN PUSH — PILAR RW 016

> Catatan ringkas untuk membantu menyusun **commit message**, **PR description**,
> dan **README GitHub** project PILAR. Salin bagian yang Anda butuhkan.

---

## 1. Identitas Project

| Item              | Detail                                                           |
| ----------------- | ---------------------------------------------------------------- |
| Nama Aplikasi     | **PILAR** — Portal Informasi & Layanan Administrasi RW           |
| Wilayah           | RW 016, Kelurahan Melong, Cimahi Selatan                         |
| Bahasa UI         | Bahasa Indonesia                                                 |
| Versi Saat Ini    | v1.x — *update dashboard UI & halaman admin*                     |
| Lisensi           | MIT                                                              |

---

## 2. Tech Stack

**Backend**
- PHP `^8.3`
- Laravel `^13.8`
- Intervention Image (upload & kompres foto)
- SQLite / MySQL (sesuai `.env`)

**Frontend**
- Blade Templates
- Tailwind CSS `v4` (via `@tailwindcss/vite`)
- Vite `^8.0`
- Chart.js (CDN) — grafik keuangan & statistik

**Tooling**
- Laravel Pint (formatter)
- PHPUnit `^12`
- Laravel Pail (log viewer)
- Concurrently (script `composer dev`)

---

## 3. Fitur Lengkap

### A. Untuk Warga (`role: user`)
- **Beranda** — ringkasan kegiatan, foto terbaru, saldo kas RW
- **Jadwal** — lihat agenda RW (rapat, kerja bakti, posyandu, dll.)
- **Kegiatan** — galeri kegiatan beserta dokumentasi
- **Foto** — album foto kegiatan warga
- **Keuangan** — laporan transparansi kas RW (pemasukan & pengeluaran)
- **Laporan** — buat & pantau laporan keluhan / aspirasi
- **Profil** — ubah data diri & ganti password
- **Pengajuan Akun** — formulir registrasi mandiri (perlu approve)

### B. Untuk Pengurus (`role: ketua_rw` / `admin`)
- **Dashboard Manajemen** — statistik agregat (warga, kas, laporan aktif)
- **Manajemen Warga** — CRUD warga, aktifkan / nonaktifkan akun, reset password
- **Manajemen Keluarga** — pengelompokan warga per **No. KK**
- **Statistik Warga** — distribusi usia, jenis kelamin, pekerjaan, pendidikan
- **Jadwal** — CRUD agenda
- **Kegiatan** — CRUD dokumentasi
- **Foto** — upload (kompres otomatis), bulk delete
- **Keuangan** — CRUD transaksi, kategori masuk/keluar
- **Laporan** — proses & ubah status laporan warga
- **Pengajuan Akun** — review, approve, reject pendaftaran
- **Log Notifikasi** — pantau pengiriman notifikasi, retry jika gagal
- **Users** — daftar semua akun

### C. Layanan Pendukung
- **API Proxy Wilayah Indonesia** — provinsi → kota → kecamatan → kelurahan (di-cache)
- **Rate limiting** — login (`10/menit`), pengajuan akun (`5/menit`)
- **Role middleware** — `role:ketua_rw,admin` melindungi rute manajemen

---

## 4. Highlight Update Terakhir

Berdasarkan commit history:

- `6755a5e` — **Update dashboard UI dan halaman admin**
- `4bb0fae` — Update V.1
- `35695e1` — Initial push project

**Fokus update terbaru:**
- Penyegaran tampilan dashboard pengurus (chart, stat card)
- Perapian halaman admin (warga, pengajuan, log notifikasi)
- Pola **mobile-first**: dual-view (card di mobile, table di desktop)
- Konsistensi spacing & typography responsive (`text-xl sm:text-2xl`, `p-3 sm:p-5`)

---

## 5. Struktur Direktori Singkat

```
PILAR/
├── app/
│   ├── Http/Controllers/   # Auth, Dashboard, Warga, Keuangan, Laporan, ...
│   └── Models/             # User, Foto, Jadwal, Kegiatan, Keuangan,
│                           # Laporan, PengajuanAkun, NotifikasiLog
├── resources/
│   └── views/
│       ├── beranda.blade.php
│       ├── dashboard/
│       ├── warga/ keluarga/ pengajuan/ users/
│       ├── jadwal/ kegiatan/ foto/
│       ├── keuangan/ laporan/ statistik/ notifikasi/
│       ├── layouts/        # app (auth), guest (login/register)
│       ├── components/     # <x-stat>, <x-page-header>, <x-button>
│       └── partials/
├── routes/web.php
├── database/migrations/
└── public/
```

---

## 6. Cara Menjalankan (untuk README)

```bash
# 1. Install dependency
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate --seed

# 4. Build asset
npm run build

# 5. Jalankan (mode dev — server + queue + log + vite)
composer dev
```

Atau secara terpisah:

```bash
php artisan serve
npm run dev
```

---

## 7. Saran Commit Message

Pilih salah satu sesuai scope perubahan:

```text
feat: revamp dashboard UI & halaman admin

- Update tampilan dashboard pengurus (chart keuangan & stat card)
- Rapikan halaman manajemen warga, pengajuan akun, log notifikasi
- Terapkan pola mobile-first (card list di mobile, table di desktop)
- Konsisten spacing & typography responsive di seluruh halaman admin
```

```text
ui: penyegaran dashboard dan modul admin

Memperbarui antarmuka dashboard pengurus dan halaman-halaman manajemen
agar lebih konsisten, ringkas, dan nyaman dibuka dari perangkat mobile.
```

```text
chore(ui): align admin pages with mobile-first conventions
```

---

## 8. Template PR Description

```markdown
## Ringkasan
Penyegaran tampilan **dashboard pengurus** dan beberapa halaman **admin**
(warga, pengajuan, log notifikasi) agar selaras dengan konvensi
mobile-first yang sudah diterapkan di modul lain.

## Perubahan Utama
- Dashboard: stat card adaptif, chart responsive (`h-56 sm:h-72`)
- Halaman admin: dual-view (card di mobile, table di desktop)
- Konsistensi `<x-page-header>`, `<x-stat>`, `<x-button block>`
- Hapus inline `style="height: ..."` pada chart

## Cara Tes
- [ ] Login sebagai `ketua_rw` → buka `/dashboard`, cek di mobile & desktop
- [ ] Buka `/warga`, `/pengajuan`, `/notifikasi` — pastikan card list muncul di mobile
- [ ] Jalankan `npm run build` — tidak ada class Tailwind yang missing
- [ ] `php artisan test` — semua test hijau

## Screenshot
_(lampirkan tangkapan layar mobile + desktop)_
```

---

## 9. Catatan Sebelum Push

- [ ] Pastikan `.env` **tidak ter-commit** (cek `.gitignore`)
- [ ] Hapus `database/database.sqlite` lokal kalau berisi data uji
- [ ] Jalankan `vendor/bin/pint` untuk format PHP
- [ ] Jalankan `npm run build` untuk verifikasi asset
- [ ] Jalankan `php artisan test` jika ada test suite
- [ ] Update screenshot di README jika ada perubahan UI signifikan

---

## 10. Badge untuk README GitHub (opsional)

Tempel di paling atas README.md:

```markdown
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind-v4-38BDF8?logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)
```

---

_Dokumen ini adalah catatan internal — boleh dihapus setelah push selesai
atau dipindah ke `docs/` jika ingin disimpan sebagai referensi tim._
