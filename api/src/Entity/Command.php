<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
class Command
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private ?string $description = null;

    #[Gedmo\Locale]
    private ?string $locale = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    public function __construct(string $id, Game $game)
    {
        $this->id = $id;
        $this->game = $game;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return str_replace($this->game->getId().'_', '', $this->id);
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

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setTranslatableLocale(string $locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->setTranslatableLocale($locale);

        return $this;
    }
}
