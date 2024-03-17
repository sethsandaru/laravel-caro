<?php

namespace App\Services\GoogleOauth;

readonly class GoogleTokenResponse
{
    public function __construct(
        public string $idToken,
        public string $accessToken,
    ) {}
}
