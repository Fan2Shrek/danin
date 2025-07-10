<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\Event\IncomingEventProvider;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(operations: [
    new GetCollection(
        provider: IncomingEventProvider::class,
    ),
])]
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $link;

    #[ORM\Column]
    private \DateTimeImmutable $startAt;

    public function __construct(string $title, string $link, \DateTimeImmutable $startAt)
    {
        $this->title = $title;
        $this->link = $link;
        $this->startAt = $startAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }
}
