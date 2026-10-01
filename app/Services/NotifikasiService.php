<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Kegiatan;
use App\Models\Laporan;
use App\Models\PengajuanAkun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Helper untuk merangkai pesan WA dan mengirimkannya ke audiens yang tepat.
 * Semua pengiriman dilakukan melalui FonnteService, kegagalan tidak
 * memboikot eksekusi proses pemanggil.
 */
class NotifikasiService
{
    public function __construct(protected FonnteService $fonnte) {}

    /* ===== Audience helpers ===== */

    protected function warga()
    {
        return User::query()
            ->where('role', 'user')
            ->where('akun_aktif', true)
            ->where('notif_wa_aktif', true)
            ->whereNotNull('no_hp')
            ->get();
    }

    protected function pengurus()
    {
        return User::query()
            ->whereIn('role', ['admin', 'ketua_rw'])
            ->where('akun_aktif', true)
            ->where('notif_wa_aktif', true)
            ->whereNotNull('no_hp')
            ->get();
    }

    /* ===== Jadwal ===== */

    public function broadcastJadwalBaru(Jadwal $jadwal): void
    {
        $tgl = Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y');
        $jam = Carbon::parse($jadwal->jam)->format('H:i');

        $pesan = "*[Jadwal Baru - RW 016]*\n\n"
            . "Halo Warga,\n"
            . "Pengurus RW menambahkan jadwal baru:\n\n"
            . "Judul    : {$jadwal->judul}\n"
            . "Tanggal  : {$tgl}\n"
            . "Jam      : {$jam} WIB\n"
            . "Lokasi   : {$jadwal->lokasi}\n"
            . ($jadwal->kategori ? "Kategori : {$jadwal->kategori}\n" : "")
            . ($jadwal->deskripsi ? "\nKeterangan:\n{$jadwal->deskripsi}\n" : "")
            . "\nMohon dicatat. Terima kasih.";

        // Mode kirim mengikuti config 'fonnte.queue' (default sync) — lihat
        // FonnteService::kirimKeUsers. Mode queue butuh worker (queue:work).
        $jumlah = $this->fonnte->kirimKeUsers($this->warga(), $pesan, 'jadwal_baru');
        Log::info('[Notifikasi] Broadcast jadwal baru', ['jadwal_id' => $jadwal->id, 'target' => $jumlah]);
    }

    /* ===== Kegiatan ===== */

    public function broadcastKegiatanBaru(Kegiatan $kegiatan): void
    {
        $tgl = Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y');

        $pesan = "*[Pengumuman / Kegiatan Baru - RW 016]*\n\n"
            . "Halo Warga,\n"
            . "Ada kegiatan / pengumuman baru dari pengurus RW:\n\n"
            . "Judul   : {$kegiatan->judul}\n"
            . "Tanggal : {$tgl}\n\n"
            . "Keterangan:\n" . trim(strip_tags($kegiatan->deskripsi)) . "\n\n"
            . "Terima kasih atas perhatiannya.";

        $jumlah = $this->fonnte->kirimKeUsers($this->warga(), $pesan, 'kegiatan_baru');
        Log::info('[Notifikasi] Broadcast kegiatan baru', ['kegiatan_id' => $kegiatan->id, 'target' => $jumlah]);
    }

    /* ===== Laporan ===== */

    public function notifLaporanBaruKePengurus(Laporan $laporan): void
    {
        $pelapor = $laporan->user?->name ?? 'Tidak diketahui';
        $waktu   = Carbon::parse($laporan->created_at ?? now())->translatedFormat('d M Y H:i');
        $ringkas = $laporan->deskripsi
            ? \Illuminate\Support\Str::limit($laporan->deskripsi, 160)
            : '(tidak ada deskripsi)';

        $pesan = "*[Laporan Warga Baru - RW 016]*\n\n"
            . "Ada laporan baru masuk yang membutuhkan tinjauan pengurus.\n\n"
            . "Pelapor   : {$pelapor}\n"
            . "Kategori  : {$laporan->judul}\n"
            . "Lokasi    : {$laporan->lokasi}\n"
            . "Waktu     : {$waktu}\n\n"
            . "Ringkasan:\n{$ringkas}\n\n"
            . "Silakan buka aplikasi PILAR RW untuk meninjau dan menindaklanjuti.";

        $jumlah = $this->fonnte->kirimKeUsers($this->pengurus(), $pesan, 'laporan_baru');
        Log::info('[Notifikasi] Notif laporan baru ke pengurus', [
            'laporan_id' => $laporan->id,
            'target'     => $jumlah,
        ]);
    }

    public function notifStatusLaporanKePelapor(Laporan $laporan): void
    {
        $user = $laporan->user;
        if (!$user || !$user->bisaTerimaWa()) {
            return;
        }

        $pesan = match ($laporan->status) {
            'diproses' => "*[Status Laporan Anda - RW 016]*\n\n"
                . "Halo {$user->name},\n\n"
                . "Laporan Anda dengan kategori \"{$laporan->judul}\" "
                . "sedang diproses oleh pengurus RW. "
                . "Kami akan mengabari kembali jika sudah ada perkembangan.\n\n"
                . "Terima kasih atas partisipasi Anda.",

            'selesai'  => "*[Status Laporan Anda - RW 016]*\n\n"
                . "Halo {$user->name},\n\n"
                . "Laporan Anda dengan kategori \"{$laporan->judul}\" "
                . "telah selesai ditangani oleh pengurus RW.\n\n"
                . "Terima kasih telah membantu menjaga lingkungan kita.",

            default    => null,
        };

        if (!$pesan) return;

        $this->fonnte->kirim($user->no_hp, $pesan, 'laporan_status', $user);
    }

    /* ===== Pengajuan akun ===== */

    /**
     * @param  string|null  $passwordPlain  Hanya berisi nilai bila pengajuan TIDAK punya password_hash
     *                                       sehingga sistem terpaksa generate password baru.
     */
    public function notifPengajuanDisetujui(PengajuanAkun $pengajuan, User $user, ?string $passwordPlain = null): void
    {
        $loginUrl = url('/login');

        if ($passwordPlain !== null) {
            // Jalur fallback: pengajuan lama tanpa password_hash → kirim password baru.
            $pesan = "*[Pengajuan Akun Disetujui - PILAR RW 016]*\n\n"
                . "Halo {$user->name},\n\n"
                . "Pengajuan akun Sistem RW Anda telah *disetujui*.\n\n"
                . "Detail akun:\n"
                . "NIK      : {$user->nik}\n"
                . "Password : {$passwordPlain}\n\n"
                . "Silakan login melalui:\n{$loginUrl}\n\n"
                . "Demi keamanan, segera ubah password setelah berhasil login.";
        } else {
            // Jalur normal: user login dengan password yang ia tentukan sendiri saat pengajuan.
            $pesan = "*[Pengajuan Akun Disetujui - PILAR RW 016]*\n\n"
                . "Halo {$user->name},\n\n"
                . "Pengajuan akun Sistem RW Anda telah *disetujui*.\n\n"
                . "Silakan masuk menggunakan NIK ({$user->nik}) dan *kata sandi yang Anda buat saat mengajukan akun*.\n\n"
                . "Halaman login:\n{$loginUrl}\n\n"
                . "Jika lupa kata sandi, hubungi pengurus RW untuk reset.";
        }

        $this->fonnte->kirim($pengajuan->no_hp, $pesan, 'pengajuan_disetujui', $user);
    }

    /* ===== Keamanan akun ===== */

    /**
     * Dikirim saat Admin/RW menonaktifkan akun warga.
     */
    public function notifAkunDinonaktifkan(User $user): bool
    {
        if (!$user->bisaTerimaWa()) return false;

        $pesan = "Yth. {$user->name}, akun Sistem RW Anda telah dinonaktifkan oleh Admin/RW. "
            . "Jika merasa ini keliru, silakan hubungi pengurus RW.";

        return $this->fonnte->kirim($user->no_hp, $pesan, 'akun_dinonaktifkan', $user);
    }

    /**
     * Dikirim saat Admin/RW mengaktifkan kembali akun warga.
     */
    public function notifAkunDiaktifkan(User $user): bool
    {
        if (!$user->bisaTerimaWa()) return false;

        $pesan = "Yth. {$user->name}, akun Sistem RW Anda telah diaktifkan kembali oleh Admin/RW. "
            . "Saatnya kembali menggunakan akun Anda. Jika ada kendala, silakan hubungi pengurus RW.";

        return $this->fonnte->kirim($user->no_hp, $pesan, 'akun_diaktifkan', $user);
    }

    /**
     * Dikirim saat Admin/RW menghapus akun warga.
     * HARUS dipanggil SEBELUM soft delete agar no_hp masih terbaca.
     */
    public function notifAkunDihapus(User $user): bool
    {
        if (!$user->bisaTerimaWa()) return false;

        $pesan = "Yth. {$user->name}, akun Sistem RW Anda telah dihapus oleh Admin/RW. "
            . "Jika merasa ini keliru, silakan hubungi pengurus RW.";

        return $this->fonnte->kirim($user->no_hp, $pesan, 'akun_dihapus', $user);
    }

    /**
     * Dikirim setelah Admin/RW menambah warga baru via panel.
     * Berisi NIK + password default yang harus diganti saat login pertama.
     */
    public function notifWargaBaru(User $user, string $passwordPlain): bool
    {
        if (!$user->bisaTerimaWa()) return false;

        $tgl = Carbon::now()->translatedFormat('d F Y H:i');
        $loginUrl = url('/login');

        $pesan = "*[Akun Baru - PILAR RW 016]*\n\n"
            . "Halo {$user->name},\n\n"
            . "Akun Sistem RW Anda telah dibuat oleh pengurus pada {$tgl} WIB.\n\n"
            . "NIK      : {$user->nik}\n"
            . "Password : {$passwordPlain}\n\n"
            . "Halaman login:\n{$loginUrl}\n\n"
            . "Demi keamanan, Anda akan diminta mengubah kata sandi saat login pertama kali.";

        return $this->fonnte->kirim($user->no_hp, $pesan, 'akun_warga_baru', $user);
    }

    /**
     * Dikirim setelah Admin/RW melakukan reset kata sandi warga.
     */
    public function notifResetPasswordAdmin(User $user, string $passwordPlain): bool
    {
        if (!$user->bisaTerimaWa()) return false;

        $tgl = Carbon::now()->translatedFormat('d F Y H:i');

        $pesan = "*[Kata Sandi Direset - PILAR RW 016]*\n\n"
            . "Halo {$user->name},\n\n"
            . "Kata sandi akun Anda telah direset oleh pengurus pada {$tgl} WIB.\n\n"
            . "Kata sandi baru: {$passwordPlain}\n\n"
            . "Demi keamanan, mohon segera ubah kata sandi setelah berhasil login. "
            . "Jika ini bukan permintaan Anda, hubungi pengurus RW.";

        return $this->fonnte->kirim($user->no_hp, $pesan, 'reset_password_admin', $user);
    }

    /**
     * Konfirmasi sederhana setelah user mengubah/mereset password sendiri.
     * Tidak berisi password (untuk lupa password, ubah mandiri, login pertama).
     */
    public function notifPasswordDiubah(User $user, string $sumber = 'ubah mandiri'): bool
    {
        if (!$user->bisaTerimaWa()) return false;

        $tgl = Carbon::now()->translatedFormat('d F Y H:i');

        $pesan = "*[Kata Sandi Diubah - PILAR RW 016]*\n\n"
            . "Halo {$user->name},\n\n"
            . "Kata sandi akun Anda berhasil diubah ({$sumber}) pada {$tgl} WIB.\n\n"
            . "Jika bukan Anda yang melakukan perubahan ini, segera hubungi pengurus RW.";

        return $this->fonnte->kirim($user->no_hp, $pesan, 'password_diubah', $user);
    }

    public function notifPengajuanDitolak(PengajuanAkun $pengajuan, ?User $user = null): void
    {
        $nama = $user?->name ?? 'Warga';
        $alasan = $pengajuan->alasan_tolak ?: 'Data tidak memenuhi kriteria pengajuan.';

        $pesan = "*[Pengajuan Akun Ditolak - PILAR RW 016]*\n\n"
            . "Halo {$nama},\n\n"
            . "Pengajuan akun Sistem RW Anda *ditolak*.\n\n"
            . "Alasan:\n{$alasan}\n\n"
            . "Silakan hubungi pengurus RW untuk informasi lebih lanjut.";

        $this->fonnte->kirim($pengajuan->no_hp, $pesan, 'pengajuan_ditolak', $user);
    }
}
