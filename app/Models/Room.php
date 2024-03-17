<?php

namespace App\Models;

use App\Models\Traits\UseUlidAsSecondaryIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    public const ROOM_STATUS_WAITING_FOR_ANOTHER_PLAYER = 'WAITING_FOR_ANOTHER_PLAYER';
    public const ROOM_STATUS_WAITING_FOR_CONFIRMATION = 'WAITING_FOR_CONFIRMATION';
    public const ROOM_STATUS_READY_TO_PLAY = 'READY_TO_PLAY';
    public const ROOM_STATUS_PLAYING = 'PLAYING';

    use HasFactory;
    use UseUlidAsSecondaryIdentifier;
    use SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = [
        'created_by_user_id',
        'second_user_id',
        'title',
        'status',
        'total_played',
    ];

    protected $casts = [
        'created_by_user_id' => 'int',
        'second_user_id' => 'int',
        'total_played' => 'int',
    ];

    protected $hidden = [
        'id',
        'created_by_user_id',
        'second_user_id',
        'updated_at',
        'deleted_at',
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function secondUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_user_id');
    }
}
