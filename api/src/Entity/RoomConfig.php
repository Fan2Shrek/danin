<?php

namespace App\Entity;

use App\Enum\GameEnum;
use App\Repository\RoomConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RoomConfigRepository::class)]
class RoomConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'roomConfig', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Room $room;

    #[ORM\Column(length: 255)]
    private string $transport;

    #[ORM\Column(enumType: GameEnum::class)]
    private GameEnum $game;

    #[ORM\Column]
    private array $transportSettings = [];

    #[ORM\Column(type: Types::JSON)]
    private array $commands = [];

    #[ORM\Column(type: Types::JSON)]
    private array $providers = [];

    public function __construct(Room $room, string $transport, GameEnum $game, array $transportSettings = [], array $commands = [], array $providers = [])
    {
        $this->room = $room;
        $this->transport = $transport;
        $this->game = $game;
        $this->transportSettings = $transportSettings;
        $this->commands = $commands;
        $this->providers = $providers;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }

    #[Groups(['default', 'room:read:collection'])]
    public function getGame(): ?GameEnum
    {
        return $this->game;
    }

    public function getTransportSettings(): array
    {
        return $this->transportSettings;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }
}
