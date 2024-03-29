<?php

namespace Database\Factories;

use App\Models\Room;
use App\Services\CaroLogic\DefaultGameBoardData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomGame>
 */
class RoomGameFactory extends Factory
{
    public function definition(): array
    {
        $room = Room::factory()->create();

        return [
            'room_id' => $room->id,
            'first_player_user_id' => $room->created_by_user_id,
            'second_player_user_id' => $room->second_user_id,
            'first_turn_user_id' => fake()->randomElement([
                $room->created_by_user_id,
                $room->second_user_id,
            ]),
            'next_turn_user_id' => fake()->randomElement([
                $room->created_by_user_id,
                $room->second_user_id,
            ]),
            'games' => DefaultGameBoardData::get(),
        ];
    }
}
