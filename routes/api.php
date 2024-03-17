<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
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
        Route::patch('/rooms/{room}/get-out', [RoomController::class, 'getOut']);
    });

