<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

Broadcast::channel('playRoom.{roomId}', function (User $user, int $roomId) {
    $room = Room::findByUlid($roomId);
    if ($room->created_by_user_id === $user->id) {
        return $user->toSimpleUserInfo();
    }

    if ($room->second_user_id === null) {
        DB::transaction(function () use ($room, $user) {
            $room->lockForUpdate();
            $room->second_user_id = $user->id;
            $room->save();
        });

        return $user->toSimpleUserInfo();
    }

    return null;
});
