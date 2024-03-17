<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomEvent extends Model
{
    use HasFactory;

    protected $table = 'room_events';

    protected $fillable = [
        'room_id',
        'type',
        'payload',
    ];

    protected $casts = [
        'room_id' => 'int',
    ];
}
