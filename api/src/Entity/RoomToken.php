<?php

namespace App\Entity;

use App\Repository\RoomTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomTokenRepository::class)]
class RoomToken
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $roomId;

    public function __construct(string $id, string $roomId)
    {
        $this->id = $id;
        $this->roomId = $roomId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRoomId(): string
    {
        return $this->roomId;
    }
}
