<?php

declare(strict_types=1);

namespace App\Tests\Functional\Game;

use App\Tests\Functional\FunctionalTestCase;

final class GetProvidersTest extends FunctionalTestCase
{
    protected const URI = '/api/providers';
    protected static bool $requestsWithAuthentication = true;

    public function testCall()
    {
        $this->client->request('GET', static::URI);

        self::assertResponseIsSuccessful();
    }

    public function testCount()
    {
        $response = $this->client->request('GET', static::URI);

        self::assertEquals(1, $response->toArray()['totalItems']);
    }

    public function testFormat()
    {
        $response = $this->client->request('GET', static::URI);

        self::assertArraySubset([
            'id' => 'Discord',
        ], $response->toArray()['member'][0]);
    }
}
