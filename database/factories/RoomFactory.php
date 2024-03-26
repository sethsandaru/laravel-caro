<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by_user_id' => fn () => User::factory()->create(),
            'second_user_id' => fn () => User::factory()->create(),
            'title' => fake()->realTextBetween(),
            'status' => fake()->randomElement([
                Room::ROOM_STATUS_WAITING_FOR_ANOTHER_PLAYER,
                Room::ROOM_STATUS_WAITING_FOR_CONFIRMATION,
                Room::ROOM_STATUS_READY_TO_PLAY,
                Room::ROOM_STATUS_PLAYING,
            ]),
        ];
    }
}
