<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users');

            $table->foreignId('second_user_id')
                ->nullable()
                ->constrained('users');

            $table->string('title');

            /**
             * WAITING_FOR_ANOTHER_PERSON
             * READY_TO_PLAY
             * PLAYING
             */
            $table->string('status')->default('WAITING_FOR_ANOTHER_PLAYER');

            $table->unsignedSmallInteger('total_played')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
