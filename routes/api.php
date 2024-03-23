<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomGameController;
use App\Http\JsonResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/google', [AuthController::class, 'signInUsingGoogle']);

Route::middleware('auth:api')
    ->group(function () {
        Route::get('/auth/logged-in-user', function (Request $request) {
            $user = $request->user();

            return JsonResponseFactory::successOutcome([
                'ulid' => $user->ulid,
                'name' => $user->name,
                'email' => $user->email,
                'profilePicture' => $user->profile_picture,
            ]);
        });

        Route::get('/rooms', [RoomController::class, 'index']);
        Route::get('/rooms/{room}', [RoomController::class, 'show']);
        Route::post('/rooms', [RoomController::class, 'create']);
        Route::patch('/rooms/{room}/join', [RoomController::class, 'joinRoom']);
        Route::patch('/rooms/{room}/get-out', [RoomController::class, 'getOut']);
        Route::patch('/rooms/{room}/ready', [RoomController::class, 'markAsReadyToPlay']);
        Route::patch('/rooms/{room}/unready', [RoomController::class, 'markAsUnReadyToPlay']);

        Route::post('/rooms/{room}/start-new-game', [RoomGameController::class, 'startNewGame']);
        Route::post('/rooms/{room}/games/{roomGame}/move', [RoomGameController::class, 'setMove']);
    });

