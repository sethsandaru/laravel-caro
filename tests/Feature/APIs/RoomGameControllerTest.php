<?php

namespace Tests\Feature\APIs;

use App\Events\NewGameStarted;
use App\Events\NextTurnAvailable;
use App\Models\Room;
use App\Models\RoomGame;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RoomGameControllerTest extends TestCase
{
    public function testStartNewGameEndpointReturnsForbiddenBecauseSecondPlayerCannotStartTheGame()
    {
        $room = Room::factory()->create([
            'status' => Room::ROOM_STATUS_READY_TO_PLAY,
        ]);

        $this->actingAs($room->secondUser);

        $this->postJson('/api/rooms/' . $room->ulid .'/start-new-game')
            ->assertForbidden();
    }

    public function testStartNewGameEndpointReturnsErrorBecauseSecondPlayerIsNotReady()
    {
        $room = Room::factory()->create([
            'status' => Room::ROOM_STATUS_WAITING_FOR_CONFIRMATION,
        ]);

        $this->actingAs($room->createdByUser);

        $this->postJson('/api/rooms/' . $room->ulid .'/start-new-game')
            ->assertBadRequest()
            ->assertJsonFragment([
                'outcome' => 'SECOND_PLAYER_IS_NOT_READY',
            ]);
    }

    public function testStartNewGameEndpointCreatesANewGame()
    {
        Event::fake([
            NewGameStarted::class,
            NextTurnAvailable::class,
        ]);

        $room = Room::factory()->create([
            'status' => Room::ROOM_STATUS_READY_TO_PLAY,
        ]);

        $this->actingAs($room->createdByUser);

        $this->postJson('/api/rooms/' . $room->ulid .'/start-new-game')
            ->assertOk()
            ->assertJsonFragment([
                'outcome' => 'SUCCESS',
            ]);

        $room->refresh();

        $this->assertSame(Room::ROOM_STATUS_PLAYING, $room->status);

        $this->assertDatabaseHas(RoomGame::class, [
            'room_id' => $room->id,
            'first_player_user_id' => $room->created_by_user_id,
            'second_player_user_id' => $room->second_user_id,
        ]);

        Event::assertDispatched(NewGameStarted::class);
        Event::assertDispatched(NextTurnAvailable::class);
    }
}
