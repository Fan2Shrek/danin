<?php

declare(strict_types=1);

namespace App\Tests\Functional\Game;

use App\Tests\Functional\FunctionalTestCase;

final class GetGamesTest extends FunctionalTestCase
{
    public function testCall()
    {
        $this->client->request('GET', '/api/games');

        self::assertResponseIsSuccessful();
    }

    public function testGame()
    {
        $response = $this->client->request('GET', '/api/games');

        self::assertCount(1, $response->toArray()['member']);
    }

    public function testFormat()
    {
        $response = $this->client->request('GET', '/api/games');

        self::assertSame([
            'id' => 'tboi',
            'name' => 'The binding of isaac',
        ], $response->toArray()['member'][0]);
    }
}
