<?php

namespace App\Services\CaroLogic;

use App\Events\SecondPlayerUnready;
use App\Models\Room;

class RefreshRoomService
{
    protected Room $room;

    public function setRoom(Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function refresh(): void
    {
        // when finished playing, revert the status to confirmation,
        // second player needs to "ready" again

        $this->room->fill([
            'status' => Room::ROOM_STATUS_WAITING_FOR_CONFIRMATION,
            'total_played' => $this->room->games()->count(),
        ])->save();

        broadcast(new SecondPlayerUnready(
            $this->room
        ));
    }
}
