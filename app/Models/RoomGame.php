<?php

namespace App\Models;

use App\Models\Traits\UseUlidAsSecondaryIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomGame extends Model
{
    use HasFactory;
    use UseUlidAsSecondaryIdentifier;

    protected $table = 'room_games';

    protected $fillable = [
        'room_id',
        'first_player_user_id',
        'second_player_user_id',
        'first_turn_user_id',
        'next_turn_user_id',
        'games',
        'winner_user_id',
    ];

    protected $casts = [
        'room_id' => 'int',
        'first_player_user_id' => 'int',
        'second_player_user_id' => 'int',
        'games' => 'array',
        'winner_user_id' => 'int',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function firstTurnUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_turn_user_id');
    }

    public function nextTurnUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'next_turn_user_id');
    }

    public function winnerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }
}
