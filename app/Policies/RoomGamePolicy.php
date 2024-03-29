<?php

namespace App\Policies;

use App\Models\RoomGame;
use App\Models\User;

class RoomGamePolicy
{
    public function canView(User $user, RoomGame $roomGame): bool
    {
        return in_array($user->id, [$roomGame->first_player_user_id, $roomGame->second_player_user_id]);
    }
}
