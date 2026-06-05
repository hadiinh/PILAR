<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otp,
        public Carbon $expiresAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Reset Kata Sandi - PILAR RW 016',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-reset',
            with: [
                'user'      => $this->user,
                'otp'       => $this->otp,
                'expiresAt' => $this->expiresAt,
            ],
        );
    }
}
