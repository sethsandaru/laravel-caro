<?php

namespace App\Services\GoogleOauth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleOauthService
{
    public function __construct(
        protected string $googleClientId,
        protected string $googleClientSecret,
        protected string $googleRedirectUri,
    ) {}

    public function getOauthToken(string $code): ?GoogleTokenResponse
    {
        $url = 'https://oauth2.googleapis.com/token';

        $response = Http::asForm()->post($url, [
            'code' => $code,
            'client_id' => $this->googleClientId,
            'client_secret' => $this->googleClientSecret,
            'redirect_uri' => $this->googleRedirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (!$response->ok()) {
            return null;
        }

        return new GoogleTokenResponse(
            $response->json('id_token'),
            $response->json('access_token')
        );
    }

    public function getUser(GoogleTokenResponse $token): ?GoogleUserResponse
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token->idToken,
        ])->get($url, [
            'alt' => 'json',
            'access_token' => $token->accessToken,
        ]);

        if (!$response->ok()) {
            return null;
        }

        return new GoogleUserResponse(
            id: $response->json('id'),
            email: $response->json('email'),
            verifiedEmail: $response->json('verified_email'),
            name: $response->json('name'),
            givenName: $response->json('given_name'),
            familyName: $response->json('family_name'),
            picture: $response->json('picture'),
            locale: $response->json('locale') ?: 'vi-VN',
        );
    }
}
