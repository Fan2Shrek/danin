<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\Functional\FunctionalTestCase;

final class MeTest extends FunctionalTestCase
{
    use UserTrait;

    protected const URI = '/api/me';

    public function testMe()
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $this->client->request('GET', static::URI);

        self::assertResponseIsSuccessful();
    }

    public function testMeLogout()
    {
        $this->client->request('GET', static::URI);

        self::assertResponseStatusCodeSame(401);
    }

    public function testMeUsername()
    {
        $user = $this->createUser(username: 'Heavy Day');
        $this->client->loginUser($user);

        $this->client->request('GET', static::URI);

        self::assertJsonContains([
            'username' => 'Heavy Day',
        ]);
    }

    public function testMeEmail()
    {
        $user = $this->createUser(email: 'testEmail@gmail.com');
        $this->client->loginUser($user);

        $this->client->request('GET', static::URI);

        self::assertJsonContains([
            'email' => 'testEmail@gmail.com',
        ]);
    }

    public function testMeNoPassword()
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $response = $this->client->request('GET', static::URI);

        self::assertJsonHasNotKey('password', $response);
    }
}
