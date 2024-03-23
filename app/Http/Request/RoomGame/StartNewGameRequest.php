<?php

namespace App\Http\Request\RoomGame;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class StartNewGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Room $room */
        $room = $this->route('room');

        return $this->user()->can('canStart', $room);
    }
}
