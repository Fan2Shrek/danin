<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;

final class EventFixtures extends AbstractFixtures
{
    public function getEntityClass(): string
    {
        return Event::class;
    }

    public function getData(): iterable
    {
        yield [
            'title' => 'Event 1',
            'link' => 'http://google.com',
            'startAt' => new \DateTimeImmutable('2023-01-01 10:00:00'),
        ];

        yield [
            'title' => 'Event 2',
            'link' => 'http://google.com',
            'startAt' => new \DateTimeImmutable('2023-01-02 10:00:00'),
        ];

        yield [
            'title' => 'Event 3',
            'link' => 'http://google.com',
            'startAt' => new \DateTimeImmutable('2023-01-03 10:00:00'),
        ];
    }
}
