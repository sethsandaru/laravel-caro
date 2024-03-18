<?php

namespace App\Http\Request\Room;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class MarkAsReadyToPlayRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Room $room */
        $room = $this->route('room');

        return $this->user()->can('canMarkReady', $room);
    }

    public function rules(): array
    {
        return [];
    }
}
