<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final class MeTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;
    protected Client $client;

    public function setup(): void
    {
        $this->client = self::createClient();
        $this->client->disableReboot();

        static::getContainer()->get(Connection::class)->beginTransaction();

        parent::setUp();
    }

    public function tearDown(): void
    {
        static::getContainer()->get(Connection::class)->rollBack();

        parent::tearDown();
    }

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

        $this->client->request('GET', '/api/me');

        self::assertArrayNotHasKey('password', $this->client->getResponse()->toArray(false));
    }

    public static function getEM(): EntityManagerInterface
    {
        return static::getContainer()->get(ManagerRegistry::class)->getManager();
    }

    private function createUser(
        string $username = 'default',
        string $email = 'default@gmail.com',
    ): User
    {
        $user = new User($username, $email);
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $this->getEm()->persist($user);
        $this->getEm()->flush();

        return $user;

    }
}
