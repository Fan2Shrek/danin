<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Domain\Command\Room\CreateRoomCommand;
use App\Domain\Command\Room\StartRoomCommand;
use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ApiResource(operations: [
    new Post(
        uriTemplate: '/rooms/create',
        messenger: 'input',
        input: CreateRoomCommand::class,
    ),
    new Post(
        uriTemplate: '/rooms/{id}/start',
        messenger: 'input',
        input: StartRoomCommand::class,
        output: false,
    ),
    new Get(),
])]
#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    public function __construct(User $owner)
    {
        $this->owner = $owner;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }
}
