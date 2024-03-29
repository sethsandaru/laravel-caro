<?php

namespace Tests\Unit\Models;

use App\Models\Room;
use App\Models\RoomGame;
use App\Models\User;
use Tests\TestCase;

class RoomTest extends TestCase
{
    public function testRoomBelongsToCreatorUser()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create([
            'created_by_user_id' => $user->id,
        ]);

        $creatorUser = $room->createdByUser;

        $this->assertNotNull($creatorUser);
        $this->assertTrue($creatorUser->is($user));
    }

    public function testRoomBelongsToSecondUser()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create([
            'second_user_id' => $user->id,
        ]);

        $secondUser = $room->secondUser;

        $this->assertNotNull($secondUser);
        $this->assertTrue($secondUser->is($user));
    }

    public function testRoomHasManyGames()
    {
        $room = Room::factory()->create();
        $games = RoomGame::factory()
            ->count(3)
            ->create([
                'room_id' => $room->id,
            ]);

        $relatedGames = $room->games()->pluck('ulid');

        $this->assertSame(3, $relatedGames->count());
        $this->assertEquals(
            $games->pluck('ulid'),
            $relatedGames
        );
    }
}
