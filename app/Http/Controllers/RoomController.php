<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Request\Room\CreateRoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        $rooms = Room::orderBy('created_at', 'DESC')
            ->get();

        return JsonResponseFactory::successOutcome([
            'rooms' => $rooms,
        ]);
    }

    public function show(Room $room): JsonResponse
    {
        $room->load([
            'createdByUser',
            'secondUser',
        ]);

        return JsonResponseFactory::successOutcome([
            'room' => $room,
        ]);
    }

    public function create(CreateRoomRequest $request): JsonResponse
    {
        $user = $request->user();
        $currentRoom = $user->createdRoom ?? $user->joinedRoom;
        if ($currentRoom !== null) {
            return JsonResponseFactory::outcome('ALREADY_IN_A_ROOM', [
                'roomId' => $currentRoom->ulid,
            ])->badRequest();
        }

        $room = Room::create([
            'title' => $request->validated('title'),
            'created_by_user_id' => $user->id,
        ]);

        return JsonResponseFactory::successOutcome([
            'room' => $room,
        ]);
    }

    public function getOut(Room $room, Request $request)
    {
        $user = $request->user();
        if ($room->created_by_user_id !== $user->id && $room->second_user_id !== $user->id) {
            return JsonResponseFactory::outcome('INVALID_ROOM')->badRequest();
        }

        if ($room->created_by_user_id === $user->id) {
            $room->created_by_user_id = $room->second_user_id;
        }

        if ($room->second_user_id === $user->id) {
            $room->second_user_id = null;
        }

        if ($room->created_by_user_id === null) {
            $room->delete();
        } else {
            $room->save();
        }

        return JsonResponseFactory::successOutcome();
    }
}
