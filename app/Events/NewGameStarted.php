<?php

namespace App\Events;

use App\Models\Room;
use App\Models\RoomGame;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewGameStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Room $room,
        public RoomGame $roomGame
    ) {
    }

    public function broadcastOn(): array
    {
        $channelId = 'playRoom.' . $this->room->ulid;

        return [
            new PresenceChannel($channelId),
        ];
    }
}
