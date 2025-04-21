<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\Functional\FunctionalTestCase;

final class MeTest extends FunctionalTestCase
{
    use UserTrait;

    public function testMe()
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $this->client->request('GET', '/api/me');

        self::assertResponseIsSuccessful();
    }

    public function testMeLogout()
    {
        $this->client->request('GET', '/api/me');

        self::assertResponseStatusCodeSame(401);
    }

    public function testMeUsername()
    {
        $user = $this->createUser(username: 'Heavy Day');
        $this->client->loginUser($user);

        $this->client->request('GET', '/api/me');

        self::assertJsonContains([
            'username' => 'Heavy Day',
        ]);
    }

    public function testMeEmail()
    {
        $user = $this->createUser(email: 'testEmail@gmail.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/api/me');

        self::assertJsonContains([
            'email' => 'testEmail@gmail.com',
        ]);
    }

    public function testMeNoPassword()
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $response = $this->client->request('GET', '/api/me');

        self::assertJsonHasNotKey('password', $response);
    }
}
