<?php

namespace App\Http\Controllers;

use App\Events\NewGameStarted;
use App\Events\NextTurnAvailable;
use App\Http\JsonResponseFactory;
use App\Http\Request\RoomGame\SetMoveRequest;
use App\Http\Request\RoomGame\StartNewGameRequest;
use App\Models\Room;
use App\Models\RoomGame;
use App\Services\CaroLogic\DefaultGameBoardData;
use App\Services\CaroLogic\StartGameService;
use Illuminate\Http\JsonResponse;

class RoomGameController extends Controller
{
    public function startNewGame(
        StartNewGameRequest $request,
        Room $room,
        StartGameService $startGameService
    ): JsonResponse {
        if ($room->status !== Room::ROOM_STATUS_READY_TO_PLAY) {
            return JsonResponseFactory::outcome('SECOND_PLAYER_IS_NOT_READY')->badRequest();
        }

        $startGameService->setRoom($room)->startGame();

        return JsonResponseFactory::successOutcome();
    }

    public function setMove(
        SetMoveRequest $request,
        Room $room,
        RoomGame $roomGame
    ): JsonResponse {
        if ($roomGame->next_turn_user_id !== $request->user()->id) {
            return JsonResponseFactory::outcome('NOT_YOUR_TURN')->badRequest();
        }

        $rowIdx = $request->validated('rowIndex');
        $colIdx = $request->validated('colIndex');

        $userCode = $roomGame->next_turn_user_id === $room->created_by_user_id
            ? 1
            : 2;

        // board update
        $gameBoard = $roomGame->games;
        $gameBoard[$rowIdx][$colIdx] = $userCode;
        $roomGame->games = $gameBoard;

        // next mover
        $roomGame->next_turn_user_id = $roomGame->next_turn_user_id === $room->created_by_user_id
            ? $room->second_user_id
            : $room->created_by_user_id;

        $roomGame->save();
        $roomGame->refresh();

        broadcast(new NextTurnAvailable($room, $roomGame, $roomGame->nextTurnUser));

        return JsonResponseFactory::successOutcome();
    }
}
