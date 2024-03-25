<?php

namespace Tests\Feature\APIs;

use App\Models\User;
use App\Services\GoogleOauth\GoogleOauthService;
use App\Services\GoogleOauth\GoogleTokenResponse;
use App\Services\GoogleOauth\GoogleUserResponse;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testSignInWithGoogleWithoutAuthCodeReceives422()
    {
        $this->postJson('/api/auth/google', [
            'code' => null,
        ])->assertUnprocessable();
    }

    public function testSignInWithGoogleReceives400InvalidToken()
    {
        $mockedGoogleOauth = $this->createMock(GoogleOauthService::class);
        $mockedGoogleOauth->expects($this->once())
            ->method('getOauthToken')
            ->with('fake-token')
            ->willReturn(null);

        $this->app->offsetSet(GoogleOauthService::class, $mockedGoogleOauth);

        $this->postJson('/api/auth/google', [
            'code' => 'fake-token',
        ])->assertBadRequest()
            ->assertJsonFragment([
                'outcome' => 'INVALID_LOGIN_TOKEN',
            ]);
    }

    public function testSignInWithGoogleReceives400InvalidUser()
    {
        $mockedGoogleOauth = $this->createMock(GoogleOauthService::class);
        $mockedGoogleOauth->expects($this->once())
            ->method('getOauthToken')
            ->with('fake-token')
            ->willReturn($oauthToken = new GoogleTokenResponse(
                'fake-id-oauth-token',
                'fake-access-token'
            ));

        $mockedGoogleOauth->expects($this->once())
            ->method('getUser')
            ->with($oauthToken)
            ->willReturn(null);

        $this->app->offsetSet(GoogleOauthService::class, $mockedGoogleOauth);

        $this->postJson('/api/auth/google', [
            'code' => 'fake-token',
        ])->assertBadRequest()
            ->assertJsonFragment([
                'outcome' => 'INVALID_LOGIN_USER',
            ]);
    }

    public function testSignInWithGoogleReceives200CreatedNewUser()
    {
        $mockedGoogleOauth = $this->createMock(GoogleOauthService::class);
        $mockedGoogleOauth->expects($this->once())
            ->method('getOauthToken')
            ->with('fake-token')
            ->willReturn($oauthToken = new GoogleTokenResponse(
                'fake-id-oauth-token',
                'fake-access-token'
            ));

        $mockedGoogleOauth->expects($this->once())
            ->method('getUser')
            ->with($oauthToken)
            ->willReturn(new GoogleUserResponse(
                id: 'fake-oauth-id',
                email: 'me@sethphat.com',
                verifiedEmail: 'me@sethphat.com',
                name: 'Seth Tran',
                givenName: 'Seth',
                familyName: 'Tran',
                picture: fake()->imageUrl(),
                locale: 'vi-VN',
            ));

        $this->app->offsetSet(GoogleOauthService::class, $mockedGoogleOauth);

        $this->postJson('/api/auth/google', [
            'code' => 'fake-token',
        ])->assertOk()->assertJsonFragment([
            'outcome' => 'SUCCESS',
        ])->assertCookie('token');

        $this->assertDatabaseHas(new User(), [
            'email' => 'me@sethphat.com',
            'name' => 'Seth Tran',
        ]);
    }

    public function testSignInWithGoogleReceives200UpdatedExistingUser()
    {
        $user = User::factory()->create([
            'name' => 'Fake Name',
            'email' => 'me@sethphat.com',
        ]);

        $mockedGoogleOauth = $this->createMock(GoogleOauthService::class);
        $mockedGoogleOauth->expects($this->once())
            ->method('getOauthToken')
            ->with('fake-token')
            ->willReturn($oauthToken = new GoogleTokenResponse(
                'fake-id-oauth-token',
                'fake-access-token'
            ));

        $mockedGoogleOauth->expects($this->once())
            ->method('getUser')
            ->with($oauthToken)
            ->willReturn(new GoogleUserResponse(
                id: 'fake-oauth-id',
                email: 'me@sethphat.com',
                verifiedEmail: 'me@sethphat.com',
                name: 'Phat Tran',
                givenName: 'Phat',
                familyName: 'Tran',
                picture: fake()->imageUrl(),
                locale: 'vi-VN',
            ));

        $this->app->offsetSet(GoogleOauthService::class, $mockedGoogleOauth);

        $this->postJson('/api/auth/google', [
            'code' => 'fake-token',
        ])->assertOk()->assertJsonFragment([
            'outcome' => 'SUCCESS',
        ])->assertCookie('token');

        $this->assertDatabaseHas(new User(), [
            'id' => $user->id,
            'email' => 'me@sethphat.com',
            'name' => 'Phat Tran',
        ]);
    }
}
