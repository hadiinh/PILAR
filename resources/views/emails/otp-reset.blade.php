<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Reset Kata Sandi - PILAR RW 016</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background-color: #f6f7f9; margin: 0; padding: 24px; color: #18181b;">
    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 520px; background: #ffffff; border-radius: 8px; border: 1px solid #e4e4e7;">
        <tr>
            <td style="padding: 24px 24px 12px 24px;">
                <h1 style="margin: 0; font-size: 18px; color: #18181b;">Kode Reset Kata Sandi</h1>
                <p style="margin: 4px 0 0; font-size: 13px; color: #71717a;">PILAR RW 016</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 24px 0 24px;">
                <p style="margin: 0 0 12px; font-size: 14px;">Halo {{ $user->name }},</p>
                <p style="margin: 0 0 16px; font-size: 14px;">
                    Kami menerima permintaan untuk mereset kata sandi akun PILAR RW Anda. Gunakan kode berikut untuk melanjutkan:
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 24px;">
                <div style="background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 8px; padding: 16px; text-align: center;">
                    <p style="margin: 0; font-family: 'Courier New', monospace; font-size: 32px; letter-spacing: 8px; font-weight: bold; color: #18181b;">
                        {{ $otp }}
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px 0 24px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: #52525b;">
                    Kode berlaku hingga <strong>{{ $expiresAt->translatedFormat('d F Y H:i') }} WIB</strong>
                    (sekitar {{ (int) round($expiresAt->diffInMinutes(now()) * -1) }} menit dari sekarang).
                </p>
                <p style="margin: 0 0 8px; font-size: 13px; color: #52525b;">
                    Jangan bagikan kode ini kepada siapa pun, termasuk kepada pihak yang mengaku dari pengurus RW.
                </p>
                <p style="margin: 0 0 16px; font-size: 13px; color: #52525b;">
                    Jika Anda tidak meminta reset kata sandi, abaikan email ini. Kata sandi Anda tidak akan berubah.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 24px 24px 24px; border-top: 1px solid #e4e4e7;">
                <p style="margin: 16px 0 0; font-size: 12px; color: #a1a1aa;">
                    Email ini dikirim otomatis oleh sistem PILAR RW 016. Mohon tidak membalas email ini.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
