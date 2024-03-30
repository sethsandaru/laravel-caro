<?php

namespace App\Services\CaroLogic;

use App\Events\GameFinished;
use App\Events\NextTurnAvailable;
use App\Models\RoomGame;
use App\Models\User;
use Illuminate\Support\Facades\Event;

class SetMoveService
{
    protected RoomGame $roomGame;
    protected User $user;

    public function __construct(
        private readonly CaroWinnerCalculator $caroWinnerCalculator,
        private readonly RefreshRoomService $refreshRoomService
    ) {
    }

    public function setRoomGame(RoomGame $roomGame): self
    {
        $this->roomGame = $roomGame;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function move(int $rowIdx, int $colIdx): SetMoveResult
    {
        if ($this->roomGame->next_turn_user_id !== $this->user->id) {
            return SetMoveResult::NOT_YOUR_TURN;
        }

        $room = $this->roomGame->room;

        // pick code
        $userCode = $this->roomGame->next_turn_user_id === $room->created_by_user_id
            ? CaroPlayerIdentifier::PLAYER_1
            : CaroPlayerIdentifier::PLAYER_2;

        // board update
        $gameBoard = $this->roomGame->games;
        if ($gameBoard[$rowIdx][$colIdx] !== 0) {
            return SetMoveResult::CONFLICTED_MOVE;
        }

        $gameBoard[$rowIdx][$colIdx] = $userCode->value;

        // here we need to calculate if this user win or not
        $winnerNumber = $this->caroWinnerCalculator->calculate($gameBoard);

        $updateValues = [
            'games' => $gameBoard,
            'next_turn_user_id' => $this->roomGame->next_turn_user_id === $room->created_by_user_id
                ? $room->second_user_id
                : $room->created_by_user_id,
        ];

        // data if we have a winner
        if ($winnerNumber !== CaroPlayerIdentifier::NO_ONE) {
            $updateValues = [
                ...$updateValues,
                'winner_user_id' => $winnerNumber === CaroPlayerIdentifier::PLAYER_1
                    ? $room->created_by_user_id
                    : $room->second_user_id,
                'next_turn_user_id' => null,
            ];
        }

        $this->roomGame->fill($updateValues)->save();
        $this->roomGame->refresh();

        // continue the game
        if ($winnerNumber === CaroPlayerIdentifier::NO_ONE) {
            Event::dispatch(new NextTurnAvailable($room, $this->roomGame, $this->roomGame->nextTurnUser));
            return SetMoveResult::SUCCESS;
        }

        // finishing the game
        Event::dispatch(new GameFinished(
            $room,
            $this->roomGame,
            $this->roomGame->winnerUser
        ));

        $this->refreshRoomService->setRoom($room)
            ->refresh();

        return SetMoveResult::SUCCESS;
    }
}
