<?php

namespace App\Events;

use App\Models\Room;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NextTurnAvailable extends BaseRoomEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(Room $room, public User $user)
    {
        parent::__construct($room);
    }
}
