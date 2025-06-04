<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\Game\GameCommandProvider;
use App\Api\State\Game\GameProvider;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/games',
            provider: GameProvider::class,
        ),
        new GetCollection(
            uriTemplate: '/games/{id}/commands',
            provider: GameCommandProvider::class,
        ),
    ],
)]
class Game
{
    // Code of the game
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private ?string $description = null;

    #[Gedmo\Locale]
    private ?string $locale = null;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setTranslatableLocale(string $locale)
    {
        $this->locale = $locale;
    }
}
