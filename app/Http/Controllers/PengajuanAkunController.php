<?php

namespace App\Http\Controllers;

use App\Models\PengajuanAkun;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Services\NotifikasiService;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PengajuanAkunController extends Controller
{
    public function __construct(
        protected NotifikasiService $notifikasi,
        protected RecaptchaService $recaptcha
    ) {}

    /* =====================================================
     | Bagian publik (warga calon pengguna)
     * ===================================================== */

    public function create()
    {
        return view('pengajuan.create', [
            'recaptchaPublicKey' => $this->recaptcha->getPublicKey(),
        ]);
    }

    public function store(Request $request)
    {
        // Rate limit per IP supaya tidak diabuse
        $key = 'pengajuan:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'nik' => "Terlalu banyak pengajuan dari perangkat ini. Coba lagi dalam {$seconds} detik.",
            ]);
        }
        RateLimiter::hit($key, 600);

        $validated = $request->validate([
            'nik'                   => 'required|string|digits:16',
            'no_hp'                 => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'password'              => ['required', 'string', 'confirmed', new StrongPassword()],
            'password_confirmation' => 'required|string',
        ], [
            'nik.required'  => 'NIK wajib diisi.',
            'nik.digits'    => 'NIK harus terdiri dari 16 angka.',
            'no_hp.regex'   => 'Format nomor HP tidak valid.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // 1. NIK harus terdaftar sebagai warga
        $user = User::where('nik', $validated['nik'])->first();
        if (!$user) {
            return back()->withInput()->withErrors([
                'nik' => 'NIK tidak ditemukan dalam data warga RW. Silakan hubungi pengurus RW.',
            ]);
        }

        // 2. Akun sudah aktif → tidak boleh ajukan lagi
        if ($user->akun_aktif) {
            return back()->withInput()->withErrors([
                'nik' => 'Akun untuk NIK ini sudah aktif. Silakan langsung masuk dengan kata sandi Anda.',
            ]);
        }

        // 3. Pending duplikat
        $adaPending = PengajuanAkun::where('nik', $validated['nik'])
            ->where('status', 'pending')
            ->exists();
        if ($adaPending) {
            return back()->withInput()->withErrors([
                'nik' => 'Pengajuan untuk NIK ini sedang menunggu persetujuan pengurus RW.',
            ]);
        }

        PengajuanAkun::create([
            'nik'           => $validated['nik'],
            'no_hp'         => $validated['no_hp'],
            'password_hash' => Hash::make($validated['password']),
            'status'        => 'pending',
            'user_id'       => $user->id,
            'ip_address'    => $request->ip(),
        ]);

        return redirect()->route('login')->with('success',
            'Pengajuan akun berhasil dikirim. Anda akan menerima notifikasi WhatsApp ketika pengajuan ditinjau.');
    }

    /* =====================================================
     | Bagian admin/RW
     * ===================================================== */

    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $q      = trim((string) $request->get('q'));

        $query = PengajuanAkun::with(['user', 'processor'])->latest();

        if (in_array($status, ['pending', 'disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('nik', 'like', "%{$q}%")
                   ->orWhere('no_hp', 'like', "%{$q}%")
                   ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        $items = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'   => PengajuanAkun::where('status', 'pending')->count(),
            'disetujui' => PengajuanAkun::where('status', 'disetujui')->count(),
            'ditolak'   => PengajuanAkun::where('status', 'ditolak')->count(),
        ];

        return view('pengajuan.admin-index', compact('items', 'stats', 'status', 'q'));
    }

    public function show(PengajuanAkun $pengajuan)
    {
        $pengajuan->load(['user', 'processor']);
        return view('pengajuan.admin-show', compact('pengajuan'));
    }

    public function approve(Request $request, PengajuanAkun $pengajuan)
    {
        if ($pengajuan->status !== 'pending') {
            return back()->withErrors(['status' => 'Pengajuan ini sudah diproses sebelumnya.']);
        }

        $user = $pengajuan->user ?? User::where('nik', $pengajuan->nik)->first();
        if (!$user) {
            return back()->withErrors(['status' => 'Warga dengan NIK ini tidak ditemukan.']);
        }

        // Pakai password yang user pilih saat mengajukan akun (sudah ter-hash di pengajuan).
        // Fallback: bila tidak ada (data legacy/korup), generate random dan kirim via WA.
        $passwordBaruPlain = null;
        $passwordHash      = $pengajuan->password_hash;

        if (empty($passwordHash)) {
            $passwordBaruPlain = Str::password(10, true, true, false, false);
            $passwordHash      = Hash::make($passwordBaruPlain);
        }

        $user->update([
            'password'    => $passwordHash,
            'akun_aktif'  => true,
            'no_hp'       => $user->no_hp ?: $pengajuan->no_hp,
        ]);

        $pengajuan->update([
            'status'       => 'disetujui',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'user_id'      => $user->id,
        ]);

        try {
            $this->notifikasi->notifPengajuanDisetujui($pengajuan, $user, $passwordBaruPlain);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('pengajuan.index')
            ->with('success', "Pengajuan disetujui. Notifikasi persetujuan dikirim via WhatsApp ke {$pengajuan->no_hp}.");
    }

    public function reject(Request $request, PengajuanAkun $pengajuan)
    {
        if ($pengajuan->status !== 'pending') {
            return back()->withErrors(['status' => 'Pengajuan ini sudah diproses sebelumnya.']);
        }

        $validated = $request->validate([
            'alasan_tolak' => 'required|string|max:500',
        ]);

        $pengajuan->update([
            'status'       => 'ditolak',
            'alasan_tolak' => $validated['alasan_tolak'],
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        try {
            $this->notifikasi->notifPengajuanDitolak($pengajuan, $pengajuan->user);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan ditolak. Notifikasi alasan telah dikirim via WhatsApp.');
    }
}
