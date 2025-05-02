<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\Functional\FunctionalTestCase;

final class LoginTest extends FunctionalTestCase
{
    use UserTrait;

    public function testLogin()
    {
        $this->createUser(
            email: 'extras@gmail.com',
            password: 'secret',
        );

        $response = $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => 'extras@gmail.com',
                'password' => 'secret',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonHasKey('token', $response);
    }

    public function testLoginWrongCredentials()
    {
        $this->createUser(
            email: 'extras@gmail.com',
            password: 'secret',
        );

        $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => 'fake@gmail.com',
                'password' => 'wrong',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains([
            'message' => 'Invalid credentials.',
        ]);
    }
}
