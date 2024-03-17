<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

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
}
