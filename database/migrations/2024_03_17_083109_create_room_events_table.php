<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_events', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();

            $table->foreignId('room_id')
                ->constrained('rooms');
            $table->string('type');
            $table->jsonb('payload');

            $table->timestamps();
        });
    }
};
