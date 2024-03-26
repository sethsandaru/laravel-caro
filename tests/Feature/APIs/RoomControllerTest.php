<?php

namespace Tests\Feature\APIs;

use App\Models\Room;
use App\Models\User;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    public function testIndexEndpointReturnsAListOfRooms()
    {
        $rooms = Room::factory()->count(2)
            ->create()
            ->map(fn (Room $room) => [
                'ulid' => $room->ulid,
                'title' => $room->title,
            ]);

        $this->actingAs(User::factory()->create());

        $this->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonCount(2, 'rooms')
            ->assertJsonFragment($rooms[0])
            ->assertJsonFragment($rooms[1]);
    }

    public function testIndexEndpointReturnsEmpty()
    {
        $this->actingAs(User::factory()->create());

        $this->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonCount(0, 'rooms');
    }

    public function testShowEndpointReturnsTheRoomInfoForCreatorAndSecondUser()
    {
        $room = Room::factory()->create();

        $this->actingAs($room->createdByUser);

        $this->getJson('/api/rooms/' . $room->ulid)
            ->assertOk()
            ->assertJsonFragment([
                'title' => $room->title,
                'status' => $room->status,
                'totalPlayed' => 0,
            ]);

        $this->actingAs($room->secondUser);
        $this->getJson('/api/rooms/' . $room->ulid)
            ->assertOk()
            ->assertJsonFragment([
                'title' => $room->title,
                'status' => $room->status,
                'totalPlayed' => 0,
            ]);
    }

    public function testShowEndpointReturns403ForUnknownUser()
    {
        $room = Room::factory()->create();

        $this->actingAs(User::factory()->create());

        $this->getJson('/api/rooms/' . $room->ulid)
            ->assertForbidden();
    }
}
