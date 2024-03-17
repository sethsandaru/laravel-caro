<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/google', [AuthController::class, 'signInUsingGoogle']);

Route::get('/auth/logged-in-user', function (Request $request) {
    $user = $request->user();

    return \App\Http\JsonResponseFactory::successOutcome([
        'ulid' => $user->ulid,
        'name' => $user->name,
        'email' => $user->email,
        'profilePicture' => $user->profile_picture,
    ]);
})->middleware(['auth:api']);
