<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_games', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();

            $table->foreignId('room_id')
                ->constrained('rooms');

            $table->foreignId('first_player_user_id')
                ->constrained('users');

            $table->foreignId('second_player_user_id')
                ->constrained('users');

            $table->jsonb('games');

            $table->foreignId('winner_user_id')
                ->nullable()
                ->constrained('users');

            $table->timestamps();
        });
    }
};
