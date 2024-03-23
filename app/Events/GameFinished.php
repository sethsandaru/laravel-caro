<?php

namespace App\Events;

use App\Models\Room;
use App\Models\RoomGame;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameFinished extends BaseRoomEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Room $room,
        public RoomGame $roomGame,
        public User $winner
    ) {
        parent::__construct($this->room);
    }
}
