<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class BetaAuthorizerService
{
    /**
     * @param int $senderId
     * @param int $receiverId
     * @param float $amount
     * @return JsonResponse|array
     */
    public function authorize(int $senderId, int $receiverId, float $amount): JsonResponse|array
    {
        $base64Email = base64_encode(env('AUTH_EMAIL'));
        $url = ENV('BETA_AUTH_URL');
        $data = [
            "sender" => $senderId,
            "receiver" => $receiverId,
            "amount" => $amount
        ];

        $response = Http::withHeaders([
            'Authorization'  => "Bearer {$base64Email}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($url, $data);

        return $response->json();
    }
}
