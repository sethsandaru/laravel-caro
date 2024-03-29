<?php

namespace App\Events;

use App\Models\Room;
use App\Models\RoomGame;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewGameStarted extends BaseRoomEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        Room $room,
        public RoomGame $roomGame
    ) {
        parent::__construct($room);
    }
}
