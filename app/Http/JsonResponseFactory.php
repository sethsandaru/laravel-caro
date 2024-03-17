<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class JsonResponseFactory
{
    const STATUS_OK = 200;
    const STATUS_CREATED = 201;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_VALIDATION_ERROR = 422;
    const STATUS_DUPLICATED = 409;

    public function __construct(protected mixed $responseData)
    {
    }

    public static function from(mixed $responseData): static
    {
        return new JsonResponseFactory($responseData);
    }

    public static function outcome(string $outcome, array $additional = []): static
    {
        return static::from([
            'outcome' => $outcome,
            ...$additional,
        ]);
    }

    public static function successOutcome(array $additional = []): JsonResponse
    {
        return static::outcome('SUCCESS', $additional)->ok();
    }

    public function ok(): JsonResponse
    {
        return new JsonResponse($this->responseData, static::STATUS_OK);
    }

    public function created(): JsonResponse
    {
        return new JsonResponse($this->responseData, static::STATUS_CREATED);
    }

    public function badRequest(): JsonResponse
    {
        return new JsonResponse($this->responseData, static::STATUS_BAD_REQUEST);
    }

    public function validationError(): JsonResponse
    {
        return new JsonResponse($this->responseData, static::STATUS_VALIDATION_ERROR);
    }

    public function duplicated(): JsonResponse
    {
        return new JsonResponse($this->responseData, static::STATUS_DUPLICATED);
    }
}
