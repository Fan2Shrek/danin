<?php

namespace App\Entity;

use App\Enum\GameEnum;
use App\Repository\RoomConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomConfigRepository::class)]
class RoomConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'roomConfig', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Room $room;

    #[ORM\Column(length: 255)]
    private string $transport;

    #[ORM\Column(enumType: GameEnum::class)]
    private GameEnum $game;

    #[ORM\Column]
    private array $transportSettings = [];

    #[ORM\Column(type: Types::JSON)]
    private array $commands = [];

    public function __construct(Room $room, string $transport, GameEnum $game, array $transportSettings = [], array $commands = [])
    {
        $this->room = $room;
        $this->transport = $transport;
        $this->game = $game;
        $this->transportSettings = $transportSettings;
        $this->commands = $commands;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }

    public function setTransport(string $transport): static
    {
        $this->transport = $transport;

        return $this;
    }

    public function getGame(): ?GameEnum
    {
        return $this->game;
    }

    public function setGame(GameEnum $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getTransportSettings(): array
    {
        return $this->transportSettings;
    }

    public function setTransportSettings(array $transportSettings): static
    {
        $this->transportSettings = $transportSettings;

        return $this;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function setCommands(array $commands): static
    {
        $this->commands = $commands;

        return $this;
    }
}
