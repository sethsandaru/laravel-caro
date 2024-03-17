<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/google', [AuthController::class, 'signInUsingGoogle']);
