<?php

declare(strict_types=1);

namespace App\Tests\Functional\Game;

use App\Tests\Functional\FunctionalTestCase;

final class GetGamesTest extends FunctionalTestCase
{
    protected const URI = '/api/games';

    public function testCall()
    {
        $this->client->request('GET', static::URI);

        self::assertResponseIsSuccessful();
    }

    public function testGame()
    {
        $response = $this->client->request('GET', static::URI);

        self::assertCount(1, $response->toArray()['member']);
    }

    public function testFormat()
    {
        $response = $this->client->request('GET', static::URI);

        self::assertArraySubset([
            'id' => 'tboi',
            'name' => 'The Binding of Isaac',
        ], $response->toArray()['member'][0]);
    }
}
