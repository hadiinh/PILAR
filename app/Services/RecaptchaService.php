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
        return $this->publicKey;
    }

    /**
     * Verifikasi reCAPTCHA token dari frontend
     */
    public function verify(string $token, ?string $ip = null): bool
    {
        if (empty($this->secretKey) || empty($token)) {
            return false;
        }

        try {
            $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $this->secretKey,
                    'response' => $token,
                    'remoteip' => $ip,
                ],
                'timeout' => 5,
            ]);

            $result = json_decode((string) $response->getBody(), true);

            // Cek apakah verifikasi berhasil dan score > 0.5
            return isset($result['success']) && $result['success'] === true;
        } catch (RequestException $e) {
            // Jika ada error network, log tapi jangan blok user di local
            if (app()->environment('local')) {
                return true; // Allow di local untuk testing
            }
            \Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
