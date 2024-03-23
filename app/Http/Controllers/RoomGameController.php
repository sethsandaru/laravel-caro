<?php

namespace App\Http\Controllers;

use App\Events\NewGameStarted;
use App\Http\JsonResponseFactory;
use App\Http\Request\RoomGame\StartNewGameRequest;
use App\Models\Room;
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
}
