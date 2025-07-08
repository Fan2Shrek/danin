<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\Functional\FunctionalTestCase;

final class RegisterTest extends FunctionalTestCase
{
    use UserTrait;

    protected const URI = '/api/register';

    public function testRegister()
    {
        $response = $this->client->request('POST', static::URI, [
            'json' => [
                'username' => 'register-test',
                'email' => 'register@gmail.com',
                'password' => 'test',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonHasKey('username', $response);
    }

    public function testRegisterwithWrongUsername()
    {
        $this->createUser(
            username: 'wrong-username',
            email: 'wrong-username@gmail.com',
            password: 'test',
        );

        $this->client->request('POST', static::URI, [
            'headers' => [
                'Accept-Language' => 'en',
            ],
            'json' => [
                'username' => 'wrong-username',
                'email' => 'test@gmail.com',
                'password' => 'test',
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            'message' => 'register.error.username.alreadyExists',
        ]);
    }

    public function testRegisterwithWrongEmail()
    {
        $this->createUser(
            username: 'wrong-email',
            email: 'wrong-email@gmail.com',
            password: 'test',
        );

        $this->client->request('POST', static::URI, [
            'headers' => [
                'Accept-Language' => 'en',
            ],
            'json' => [
                'username' => 'test',
                'email' => 'wrong-email@gmail.com',
                'password' => 'test',
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            'message' => 'register.error.email.alreadyExists',
        ]);
    }
}
