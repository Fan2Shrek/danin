<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;

final class RefreshToken extends FunctionalTestCase
{
    use UserTrait;

    protected const URI = '/api/token/refresh';

    public function testRefresh()
    {
        $user = $this->createUser(
            email: 'extras@gmail.com',
            password: 'secret',
        );

        $refreshToken = $this->loginAsUser($user);

        $response = $this->client->request('POST', static::URI, [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonHasKey('token', $response);
        self::assertJsonHasKey('refresh_token', $response);
        self::assertSame(
            $response->toArray()['refresh_token'],
            $refreshToken
        );
    }

    public function testRefreshWithoutToken()
    {
        $this->client->request('POST', static::URI, [
            'json' => [
                'refresh_token' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains([
            'message' => 'Invalid refresh token.',
        ]);
    }

    private function loginAsUser(User $user): string
    {
        $response = $this->client->request('POST', static::URI, [
            'json' => [
                'email' => $user->getUserIdentifier(),
                'password' => $user->getPassword(),
            ],
        ]);

        return $response->toArray()['refresh_token'];
    }
}
