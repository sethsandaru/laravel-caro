<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function canView(User $user, Room $room): bool
    {
        return $room->created_by_user_id === $user->id
            || $room->second_user_id === $user->id;
    }

    public function canJoin(User $user, Room $room): bool
    {
        return $room->created_by_user_id !== $user->id
            || $room->second_user_id === null;
    }
}
