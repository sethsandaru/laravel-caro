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

    public function canMarkReady(User $user, Room $room): bool
    {
        return $room->second_user_id === $user->id
            && $room->status === Room::ROOM_STATUS_WAITING_FOR_CONFIRMATION;
    }

    public function canMarkUnReady(User $user, Room $room): bool
    {
        return $room->second_user_id === $user->id
            && $room->status === Room::ROOM_STATUS_READY_TO_PLAY;
    }

    public function canStart(User $user, Room $room): bool
    {
        return $room->created_by_user_id === $user->id;
    }
}
