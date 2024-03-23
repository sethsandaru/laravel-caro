<?php

namespace App\Models;

use App\Models\Traits\UseUlidAsSecondaryIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomGame extends Model
{
    use HasFactory;
    use UseUlidAsSecondaryIdentifier;

    protected $table = 'room_games';

    protected $fillable = [
        'room_id',
        'first_player_user_id',
        'second_player_user_id',
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


}
