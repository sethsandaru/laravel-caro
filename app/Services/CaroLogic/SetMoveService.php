<?php

namespace App\Services\CaroLogic;

use App\Events\GameFinished;
use App\Events\NextTurnAvailable;
use App\Models\RoomGame;

class SetMoveService
{
    protected RoomGame $roomGame;

    public function __construct(private CaroWinnerCalculator $caroWinnerCalculator)
    {
    }

    public function setRoomGame(RoomGame $roomGame): self
    {
        $this->roomGame = $roomGame;

        return $this;
    }

    public function move(int $rowIdx, int $colIdx): void
    {
        $room = $this->roomGame->room;

        // pick code
        $userCode = $this->roomGame->next_turn_user_id === $room->created_by_user_id
            ? 1
            : 2;

        // board update
        $gameBoard = $this->roomGame->games;
        $gameBoard[$rowIdx][$colIdx] = $userCode;

        // here we need to calculate if this user win or not
        $winnerNumber = $this->caroWinnerCalculator->calculate($gameBoard);

        $updateValues = [
            'games' => $gameBoard,
            'next_turn_user_id' => $this->roomGame->next_turn_user_id === $room->created_by_user_id
                ? $room->second_user_id
                : $room->created_by_user_id,
        ];

        if ($winnerNumber > 0) {
            $updateValues['winner_user_id'] = $winnerNumber === 1
                ? $room->created_by_user_id
                : $room->second_user_id;
            $updateValues['next_turn_user_id'] = null;
        }

        $this->roomGame->fill($updateValues)->save();
        $this->roomGame->refresh();

        // continue the game
        if ($winnerNumber === 0) {
            broadcast(new NextTurnAvailable($room, $this->roomGame, $this->roomGame->nextTurnUser));
            return;
        }

        // finishing the game
        broadcast(new GameFinished(
            $room,
            $this->roomGame,
            $this->roomGame->winnerUser
        ));
    }
}
