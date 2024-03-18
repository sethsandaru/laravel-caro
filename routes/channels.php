<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('playRoom.{roomId}', function (User $user, string $roomId) {
    $room = Room::findByUlid($roomId);
    if ($user->can('canView', $room)) {
        return $user->toSimpleUserInfo();
    }
}, ['guards' => 'api']);
