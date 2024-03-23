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
use App\Services\CaroLogic\SetMoveService;
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
        RoomGame $roomGame,
        SetMoveService $setMoveService
    ): JsonResponse {
        if ($roomGame->next_turn_user_id !== $request->user()->id) {
            return JsonResponseFactory::outcome('NOT_YOUR_TURN')->badRequest();
        }

        $rowIdx = $request->validated('rowIndex');
        $colIdx = $request->validated('colIndex');

        $setMoveService->setRoomGame($roomGame)
            ->move($rowIdx, $colIdx);

        return JsonResponseFactory::successOutcome();
    }
}
