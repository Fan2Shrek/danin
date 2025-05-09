<?php

declare(strict_types=1);

namespace App\Tests\Functional\Game;

use App\Tests\Functional\FunctionalTestCase;

final class GetCommandsTest extends FunctionalTestCase
{
    protected const URI = '/api/games/%s/commands';

    public function testCall()
    {
        $this->client->request('GET', \sprintf(static::URI, 'tboi'));

        self::assertResponseIsSuccessful();
    }

    public function testIsaac()
    {
        $response = $this->client->request('GET', \sprintf(static::URI, 'tboi'));

        self::assertArraySubset([
            [
                'id' => 'spawn',
            ],
            [
                'id' => 'bomb',
            ],
            [
                'id' => 'use',
            ],
        ], $response->toArray()['member']);
    }
}
