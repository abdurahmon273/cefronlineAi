<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramGatewayService
{
    protected $baseUrl = "https://gatewayapi.telegram.org/";
    protected $token;

    public function __construct()
    {
        $this->token = env('TG_GATEWAY_TOKEN');
    }

    public function sendCode($phoneNumber, $code)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . 'sendVerificationMessage', [
            'phone_number' => $phoneNumber,
            'code' => $code,
            'code_length' => strlen($code),
            'ttl' => 300 // Kodning amal qilish vaqti (soniyalarda)
        ]);

        return $response->json();
    }
}
