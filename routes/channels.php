<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('playRoom.{roomId}', function (User $user, string $roomId) {
    $room = Room::findByUlid($roomId);
    if (in_array($user->id, [$room->created_by_user_id, $room->second_user_id])) {
        return $user->toSimpleUserInfo();
    }

    return null;
}, ['guards' => 'api']);
