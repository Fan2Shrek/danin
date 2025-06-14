<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use App\Tests\Functional\User\UserTrait;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class FunctionalTestCase extends ApiTestCase
{
    use JsonAssertionTrait;
    use UserTrait;

    protected static ?bool $alwaysBootKernel = true;
    protected static bool $requestsWithAuthentication = false;
    protected Client $client;
    private ?User $currentUser = null;

    public function setup(): void
    {
        $this->client = self::createClient([], [
            'headers' => [
                'Accept-Language' => 'fr',
            ],
        ]);
        $this->client->disableReboot();

        static::getContainer()->get(Connection::class)->beginTransaction();

        if (static::$requestsWithAuthentication) {
            $this->client->loginUser($this->currentUser = $this->createUser());
        }

        parent::setUp();
    }

    public function tearDown(): void
    {
        static::getContainer()->get(Connection::class)->rollBack();

        parent::tearDown();
    }

    protected static function getEM(): EntityManagerInterface
    {
        return static::getContainer()->get(ManagerRegistry::class)->getManager();
    }

    protected function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }
}
