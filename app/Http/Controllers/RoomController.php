<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Request\Room\CreateRoomRequest;
use App\Http\Request\Room\GetRoomByIdRequest;
use App\Http\Request\Room\JoinRoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        $rooms = Room::orderBy('created_at', 'DESC')
            ->with('createdByUser')
            ->get();

        return JsonResponseFactory::successOutcome([
            'rooms' => $rooms->map(fn (Room $room) => [
                'ulid' => $room->ulid,
                'title' => $room->title,
                'status' => $room->status,
                'totalPlayed' => $room->total_played,
                'createdByUser' => $room->createdByUser->toSimpleUserInfo(),
                'secondUser' => $room->secondUser?->toSimpleUserInfo(),
            ]),
        ]);
    }

    public function show(GetRoomByIdRequest $request, Room $room): JsonResponse
    {
        $room->load([
            'createdByUser',
            'secondUser',
        ]);

        return JsonResponseFactory::successOutcome([
            'room' => [
                'ulid' => $room->ulid,
                'title' => $room->title,
                'status' => $room->status,
                'totalPlayed' => $room->total_played,
                'createdByUser' => $room->createdByUser->toSimpleUserInfo(),
                'secondUser' => $room->secondUser?->toSimpleUserInfo(),
            ],
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
            'status' => Room::ROOM_STATUS_WAITING_FOR_ANOTHER_PLAYER,
        ]);

        return JsonResponseFactory::successOutcome([
            'roomId' => $room->ulid,
        ]);
    }

    public function joinRoom(JoinRoomRequest $request, Room $room): JsonResponse
    {
        $user = $request->user();
        DB::transaction(function () use ($room, $user) {
            $room->lockForUpdate();
            $room->update([
                'status' => Room::ROOM_STATUS_WAITING_FOR_CONFIRMATION,
                'second_user_id' => $user->id,
            ]);
        });

        return JsonResponseFactory::successOutcome([
            'roomId' => $room->ulid,
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
            $room->status = Room::ROOM_STATUS_WAITING_FOR_ANOTHER_PLAYER;
            $room->save();
        }

        return JsonResponseFactory::successOutcome();
    }
}
