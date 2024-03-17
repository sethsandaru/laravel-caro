<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Request\Auth\SignInUsingGoogleRequest;
use App\Models\User;
use App\Services\GoogleOauth\GoogleOauthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function signInUsingGoogle(SignInUsingGoogleRequest $request, GoogleOauthService $googleOauthService): JsonResponse
    {
        $googleToken = $googleOauthService->getOauthToken(
            $request->validated('code')
        );

        if (!$googleToken) {
            return JsonResponseFactory::from([
                'outcome' => 'INVALID_LOGIN_TOKEN',
            ])->badRequest();
        }

        $googleUser = $googleOauthService->getUser($googleToken);
        if (!$googleUser) {
            return JsonResponseFactory::from([
                'outcome' => 'INVALID_LOGIN_USER',
            ])->badRequest();
        }

        // upsert
        $user = User::updateOrCreate([
            'email' => $googleUser->email,
        ], [
            'email' => $googleUser->email,
            'name' => $googleUser->name,
            'profile_picture' => $googleUser->picture,
            'password' => Hash::make(Str::password(12)),
        ]);

        $token = Auth::login($user);

        return response()
            ->json([
                'outcome' => 'SUCCESS',
            ])
            ->cookie('token', $token, 60 * 60 * 7);
    }
}
