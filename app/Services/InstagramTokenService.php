<?php

// app/Services/InstagramTokenService.php

namespace App\Services;

use App\Models\InstagramToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramTokenService
{
    // Naya long-lived token pehli baar save karo
    public function saveInitialToken(string $shortToken): array
    {
        $response = Http::get('https://graph.facebook.com/oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => config('services.instagram.client_id'),
            'client_secret'     => config('services.instagram.client_secret'),
            'fb_exchange_token' => $shortToken,
        ]);

        if ($response->failed()) {
            return ['success' => false, 'message' => $response->json()['error']['message']];
        }

        $data = $response->json();

        InstagramToken::create([
            'access_token' => $data['access_token'],
            'expires_at'   => now()->addSeconds($data['expires_in']),
        ]);

        return ['success' => true, 'expires_at' => now()->addSeconds($data['expires_in'])->toDateString()];
    }

    // Token refresh karo (har 50 din baad)
    public function refreshToken(): bool
    {
        $current = InstagramToken::latest()->first();

        if (!$current) {
            Log::error('Instagram: Koi token nahi mila DB mein');
            return false;
        }

        $response = Http::get('https://graph.instagram.com/refresh_access_token', [
            'grant_type'   => 'ig_refresh_token',
            'access_token' => $current->access_token,
        ]);

        if ($response->failed()) {
            Log::error('Instagram Token Refresh Failed: ' . $response->body());
            return false;
        }

        $data = $response->json();

        InstagramToken::create([
            'access_token' => $data['access_token'],
            'expires_at'   => now()->addSeconds($data['expires_in']),
        ]);

        // Purane tokens delete karo
        InstagramToken::where('id', '!=', InstagramToken::latest()->first()->id)->delete();

        Log::info('Instagram Token Successfully Refreshed ✅');
        return true;
    }

    // Current valid token lo
    public function getToken(): ?string
    {
        $token = InstagramToken::latest()->first();
        return $token?->access_token;
    }
}
