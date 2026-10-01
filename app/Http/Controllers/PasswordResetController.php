<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SetNewPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Services\NotifikasiService;
use App\Services\OtpService;
use App\Services\PasswordPolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controller untuk fitur Lupa Kata Sandi (Forgot Password).
 * Flow:
 *  1. showForgotForm() - form input NIK/email
 *  2. requestOtp() - kirim OTP ke email
 *  3. showVerifyForm() - form masukkan OTP
 *  4. verifyOtp() - cek OTP
 *  5. showResetForm() - form password baru
 *  6. resetPassword() - simpan password baru & logout session lama
 */
class PasswordResetController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected PasswordPolicyService $policyService,
        protected NotifikasiService $notifikasi,
    ) {}

    /**
     * Tampilkan form "Lupa Kata Sandi" (input NIK atau email).
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Request OTP ke email user.
     */
    public function requestOtp(ResetPasswordRequest $request)
    {
        $result = $this->otpService->request(
            $request->identifier,
            $request->ip()
        );

        if (!$result['ok']) {
            return back()
                ->withInput(['identifier' => $request->identifier])
                ->withErrors(['identifier' => $result['message']]);
        }

        if ($result['throttled']) {
            return back()
                ->withInput(['identifier' => $request->identifier])
                ->withErrors(['identifier' => $result['message']]);
        }

        // Jangan bocorkan apakah email ditemukan, selalu kasih pesan generik
        session()->put('password_reset_identifier', $request->identifier);
        session()->put('password_reset_started', true);

        return redirect()->route('password.verify-otp.show')
            ->with('info', $result['message']);
    }

    /**
     * Tampilkan form verifikasi OTP.
     */
    public function showVerifyForm()
    {
        if (!session()->get('password_reset_started')) {
            return redirect()->route('password.forgot.show');
        }

        $identifier = session()->get('password_reset_identifier');
        $maskedIdentifier = str_contains($identifier, '@')
            ? $this->otpService->maskEmail($identifier)
            : 'NIK ' . substr($identifier, -4); // Tampilkan 4 digit terakhir NIK

        return view('auth.verify-otp', [
            'maskedIdentifier' => $maskedIdentifier,
        ]);
    }

    /**
     * Verifikasi OTP.
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        if (!session()->get('password_reset_started')) {
            return redirect()->route('password.forgot.show');
        }

        $identifier = session()->get('password_reset_identifier');

        // Untuk verifikasi OTP, kita perlu email, bukan NIK
        $user = $this->findUserByIdentifier($identifier);
        if (!$user || !$user->email) {
            // Jangan bocorkan
            return back()->withErrors([
                'otp' => 'Kode verifikasi tidak valid atau sudah expired.',
            ]);
        }

        $verifiedUser = $this->otpService->verify($user->email, $request->otp);
        if (!$verifiedUser) {
            return back()->withErrors([
                'otp' => 'Kode verifikasi tidak valid atau sudah expired.',
            ]);
        }

        session()->put('password_reset_user_id', $verifiedUser->id);
        session()->put('password_reset_otp_verified', true);

        return redirect()->route('password.reset.show')
            ->with('success', 'Kode verifikasi benar. Silakan buat kata sandi baru.');
    }

    /**
     * Tampilkan form set password baru.
     */
    public function showResetForm()
    {
        if (!session()->get('password_reset_otp_verified')) {
            return redirect()->route('password.forgot.show');
        }

        $userId = session()->get('password_reset_user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->flush();
            return redirect()->route('password.forgot.show')
                ->withErrors(['error' => 'Session expired. Silakan coba lagi.']);
        }

        return view('auth.reset-password', [
            'passwordRules' => $this->policyService->rules(),
        ]);
    }

    /**
     * Reset password dan force logout session lama.
     */
    public function resetPassword(SetNewPasswordRequest $request)
    {
        if (!session()->get('password_reset_otp_verified')) {
            return redirect()->route('password.forgot.show');
        }

        $userId = session()->get('password_reset_user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->flush();
            return redirect()->route('password.forgot.show')
                ->withErrors(['error' => 'Session expired. Silakan coba lagi.']);
        }

        // Validasi password menggunakan PasswordPolicyService
        $passwordErrors = $this->policyService->validate(
            $request->password,
            $user,
            $user->password
        );

        if (!empty($passwordErrors)) {
            return back()->withErrors([
                'password' => $passwordErrors,
            ]);
        }

        // Update password & increment session version untuk force logout
        $user->update([
            'password'          => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);
        $user->incrementSessionVersion();

        // Kirim notifikasi WhatsApp jika ada nomor HP
        $this->notifikasi->notifPasswordDiubah($user, 'melalui fitur lupa kata sandi');

        // Bersihkan session
        session()->forget([
            'password_reset_identifier',
            'password_reset_started',
            'password_reset_user_id',
            'password_reset_otp_verified',
        ]);

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru Anda.');
    }

    /**
     * Tampilkan form force change password (saat first login).
     */
    public function showForceChangeForm()
    {
        return view('password.force-change', [
            'passwordRules' => $this->policyService->rules(),
        ]);
    }

    /**
     * Submit force change password.
     */
    public function submitForceChange(SetNewPasswordRequest $request)
    {
        $user = auth()->user();

        if (!$user->must_change_password) {
            return redirect()->route('beranda');
        }

        // Validasi password menggunakan PasswordPolicyService
        $passwordErrors = $this->policyService->validate(
            $request->password,
            $user,
            $user->password
        );

        if (!empty($passwordErrors)) {
            return back()->withErrors([
                'password' => $passwordErrors,
            ]);
        }

        $user->update([
            'password'          => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);
        $user->incrementSessionVersion();

        return redirect()->route('beranda')
            ->with('success', 'Kata sandi berhasil diubah.');
    }

    /* ===== Helpers ===== */

    protected function findUserByIdentifier(string $identifier): ?User
    {
        // NIK = 16 digit
        if (preg_match('/^\d{16}$/', $identifier)) {
            return User::where('nik', $identifier)
                ->where('akun_aktif', true)
                ->first();
        }

        // Email
        return User::whereRaw('LOWER(email) = ?', [strtolower($identifier)])
            ->where('akun_aktif', true)
            ->first();
    }
}
