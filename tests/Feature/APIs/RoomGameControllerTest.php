<?php

namespace Tests\Feature\APIs;

use App\Events\NewGameStarted;
use App\Events\NextTurnAvailable;
use App\Models\Room;
use App\Models\RoomGame;
use App\Services\CaroLogic\SetMoveResult;
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

    public function testSetMoveReturnsNotUserTurnError()
    {
        $room = Room::factory()->create([
            'status' => Room::ROOM_STATUS_PLAYING,
        ]);
        $roomGame = RoomGame::factory()->setRoom($room)->create([
            'next_turn_user_id' => $room->second_user_id,
        ]);

        $this->actingAs($room->createdByUser);

        $this->postJson(
            '/api/rooms/' . $room->ulid .'/games/' . $roomGame->ulid .  '/move',
            [
                'rowIndex' => 1,
                'colIndex' => 1,
            ]
        )->assertBadRequest()->assertJsonFragment([
            'outcome' => SetMoveResult::NOT_YOUR_TURN->value,
        ]);
    }

    public function testSetMoveReturnsConflictTurnError()
    {
        $room = Room::factory()->create([
            'status' => Room::ROOM_STATUS_PLAYING,
        ]);
        $roomGame = RoomGame::factory()->setRoom($room)->create([
            'next_turn_user_id' => $room->second_user_id,
        ]);

        // hijack game board, ugly way but don't have any other way :((
        $board = $roomGame->games;
        $board[1][1] = 1;

        $roomGame->games = $board;
        $roomGame->save();

        $this->actingAs($room->secondUser);

        $this->postJson(
            '/api/rooms/' . $room->ulid .'/games/' . $roomGame->ulid .  '/move',
            [
                'rowIndex' => 1,
                'colIndex' => 1,
            ]
        )->assertBadRequest()->assertJsonFragment([
            'outcome' => SetMoveResult::CONFLICTED_MOVE->value,
        ]);
    }

    public function testSetMoveReturnsOkAndSetMoveOnTheBoard()
    {
        Event::fake([
            NextTurnAvailable::class,
        ]);

        $room = Room::factory()->create([
            'status' => Room::ROOM_STATUS_PLAYING,
        ]);
        $roomGame = RoomGame::factory()->create([
            'room_id' => $room->id,
            'first_player_user_id' => $room->created_by_user_id,
            'second_player_user_id' => $room->second_user_id,
            'first_turn_user_id' => $room->created_by_user_id,
            'next_turn_user_id' => $room->second_user_id,
        ]);

        $this->actingAs($room->secondUser);

        $this->postJson(
            '/api/rooms/' . $room->ulid .'/games/' . $roomGame->ulid .  '/move',
            [
                'rowIndex' => 5,
                'colIndex' => 5,
            ]
        )->assertOk()->assertJsonFragment([
            'outcome' => 'SUCCESS',
        ]);

        Event::assertDispatched(
            NextTurnAvailable::class,
            fn (NextTurnAvailable $event) => $event->user->is($room->createdByUser)
                && $event->roomGame->is($roomGame)
                && $event->roomGame->games[5][5] === 2, // 2 === second player
        );
    }
}
