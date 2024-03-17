<?php

namespace App\Models;

use App\Models\Traits\UseUlidAsSecondaryIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
{
    use HasFactory, Notifiable;
    use UseUlidAsSecondaryIdentifier;

    protected $fillable = [
        'name',
        'email',
        'profile_picture',
        'email_verified_at',
        'password',
        'remember_token',
    ];

    protected $hidden = [
        'id',
        'password',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function createdRoom(): HasOne
    {
        return $this->hasOne(Room::class, 'created_by_user_id');
    }

    public function joinedRoom(): HasOne
    {
        return $this->hasOne(Room::class, 'second_user_id');
    }
}
