<?php

namespace App\Http\Controllers;

use App\Events\NewGameStarted;
use App\Http\JsonResponseFactory;
use App\Http\Request\RoomGame\StartNewGameRequest;
use App\Models\Room;
use App\Services\CaroLogic\DefaultGameBoardData;
use Illuminate\Http\JsonResponse;

class RoomGameController extends Controller
{
    public function startNewGame(StartNewGameRequest $request, Room $room): JsonResponse
    {
        if ($room->status !== Room::ROOM_STATUS_READY_TO_PLAY) {
            return JsonResponseFactory::outcome('SECOND_PLAYER_IS_NOT_READY')->badRequest();
        }

        $roomGame = $room->games()->create([
            'first_player_user_id' => $room->created_by_user_id,
            'second_player_user_id' => $room->second_user_id,
            'games' => DefaultGameBoardData::get(),
        ]);

        broadcast(new NewGameStarted($room, $roomGame));

        return JsonResponseFactory::successOutcome();
    }
}
