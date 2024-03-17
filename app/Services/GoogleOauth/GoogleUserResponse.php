<?php

namespace App\Services\GoogleOauth;

readonly class GoogleUserResponse
{
    public function __construct(
        public string $id,
        public string $email,
        public bool $verifiedEmail,
        public string $name,
        public string $givenName,
        public string $familyName,
        public string $picture,
        public string $locale,
    ) {}
}
