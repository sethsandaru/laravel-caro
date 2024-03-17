<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $ulid
 * @mixin Model
 */
trait UseUlidAsSecondaryIdentifier
{
    use HasUlids;

    /**
     * @return string[]
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public static function findByUlid(string $ulid): ?static
    {
        return static::query()->where('ulid', $ulid)->first();
    }
}
