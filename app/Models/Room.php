<?php

namespace App\Models;

use App\Models\Traits\UseUlidAsSecondaryIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use UseUlidAsSecondaryIdentifier;
    use SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = [
        'created_by_user',
        'second_user_id',
        'title',
        'status',
        'total_played',
    ];

    protected $casts = [
        'created_by_user' => 'int',
        'second_user_id' => 'int',
        'total_played' => 'int',
    ];

    protected $hidden = [
        'id',
        'created_by_user',
        'second_user_id',
        'updated_at',
        'deleted_at',
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    public function secondUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_user_id');
    }
}
