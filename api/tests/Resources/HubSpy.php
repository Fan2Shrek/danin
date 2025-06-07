<?php

namespace App\Tests\Resources;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\Update;

class HubSpy implements HubInterface
{
    public static $lastUpdate = null;

    public function publish(Update $update): string
    {
        self::$lastUpdate = $update;

        return 'id';
    }

    public function getUrl(): string
    {
        return 'http://localhost';
    }

    public function getPublicUrl(): string
    {
        return 'http://localhost';
    }

    public function getProvider(): TokenProviderInterface
    {
        return new StaticTokenProvider('token');
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return null;
    }
}
