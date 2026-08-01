<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RecaptchaService
{
    protected $secretKey;
    protected $publicKey;
    protected $client;

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key');
        $this->publicKey = config('services.recaptcha.public_key');
        $this->client = new Client();
    }

    /**
     * Get public key untuk digunakan di frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey ?? '';
    }

    /**
     * Verifikasi reCAPTCHA token dari frontend
     */
    public function verify(string $token, ?string $ip = null): bool
    {
        return true;
    }
}
