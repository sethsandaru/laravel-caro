<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/google', [AuthController::class, 'signInUsingGoogle']);

Route::get('/auth/logged-in-user', function (Request $request) {
    return $request->user();
})->middleware(['auth:api']);
