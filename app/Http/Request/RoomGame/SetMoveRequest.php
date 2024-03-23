<?php

namespace App\Http\Request\RoomGame;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class SetMoveRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Room $room */
        $room = $this->route('room');

        return $this->user()->can('canView', $room);
    }

    public function rules(): array
    {
        return [
            'rowIndex' => 'required|int',
            'colIndex' => 'required|int',
        ];
    }
}
