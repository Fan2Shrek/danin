<?php

declare(strict_types=1);

namespace App\Tests\Functional\SelfHosted;

use App\SelfHostedKernel;
use App\Tests\Functional\FunctionalTestCase;
use App\Tests\Functional\User\UserTrait;

final class SelfHostedDisabledRouteTest extends FunctionalTestCase
{
    use UserTrait;

    public function testDisabledActiveTotp(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);
        $this->client->request('POST', '/api/activate-totp');

        self::assertResponseStatusCodeSame(404);
    }

    public function testDisabledCheckTotp(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);
        $this->client->request('POST', '/api/check-totp');

        self::assertResponseStatusCodeSame(404);
    }

    protected static function getKernelClass(): string
    {
        return SelfHostedKernel::class;
    }
}
