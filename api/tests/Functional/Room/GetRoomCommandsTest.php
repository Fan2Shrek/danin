<?php

declare(strict_types=1);

namespace App\Tests\Functional\Room;

use App\Entity\Room;
use App\Entity\RoomConfig;
use App\Enum\GameEnum;
use App\Tests\Functional\FunctionalTestCase;

final class GetRoomCommandsTest extends FunctionalTestCase
{
    protected const URI = '/api/rooms/%s/commands';
    protected static bool $requestsWithAuthentication = true;

    public function testCall()
    {
        $id = $this->createRoomConfig();
        $this->client->request('GET', \sprintf(static::URI, $id));

        self::assertResponseIsSuccessful();
    }

    public function testWithRoomConfig()
    {
        $id = $this->createRoomConfig([
            'spawn',
            'bomb',
            'use',
        ]);
        $response = $this->client->request('GET', \sprintf(static::URI, $id));

        self::assertArraySubset([
            [
                'name' => 'spawn',
            ],
            [
                'name' => 'bomb',
            ],
            [
                'name' => 'use',
            ],
        ], $response->toArray()['member']);
        self::assertArrayHasKey('description', $response->toArray()['member'][0]);
    }

    private function createRoomConfig(array $commands = ['spawn']): string
    {
        $room = new Room($this->getCurrentUser());
        $this->getEM()->persist($room);
        $roomConfig = new RoomConfig(
            $room,
            'mercure',
            GameEnum::THE_BINDING_OF_ISAAC,
            [],
            $commands,
        );
        $this->getEM()->persist($roomConfig);
        $this->getEM()->flush();

        return (string) $room->getId();
    }
}
