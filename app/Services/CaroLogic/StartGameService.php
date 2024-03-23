<?php

namespace App\Services\CaroLogic;

use App\Events\NewGameStarted;
use App\Events\NextTurnAvailable;
use App\Models\Room;
use App\Models\RoomGame;

class StartGameService
{
    protected Room $room;

    public function setRoom(Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function startGame(): RoomGame
    {
        $roomGame = $this->room->games()->create([
            'first_player_user_id' => $this->room->created_by_user_id,
            'second_player_user_id' => $this->room->second_user_id,
            'games' => DefaultGameBoardData::get(),
            ...$this->acquireFirstTurnPlayer(),
        ]);

        $this->room->fill([
            'status' => Room::ROOM_STATUS_PLAYING,
        ]);

        broadcast(new NewGameStarted($this->room, $roomGame));
        broadcast(new NextTurnAvailable($this->room, $roomGame, $roomGame->nextTurnUser));

        return $roomGame;
    }

    /**
     * @return array{
     *      first_turn_user_id: int,
     *      next_turn_user_id: int
     * }
     */
    private function acquireFirstTurnPlayer(): array
    {
        $lastGame = $this->room->games()->latest()->first();

        // if there is no game, the creator will be the first one to move
        if (!$lastGame) {
            return [
                'first_turn_user_id' => $this->room->created_by_user_id,
                'next_turn_user_id' => $this->room->created_by_user_id,
            ];
        }

        $firstTurnUserId = $lastGame->first_turn_user_id === $this->room->created_by_user_id
            // second player goes first on the next round
            ? $this->room->second_user_id
            : $this->room->created_by_user_id;

        return [
            'first_turn_user_id' => $firstTurnUserId,
            'next_turn_user_id' => $firstTurnUserId,
        ];
    }
}
