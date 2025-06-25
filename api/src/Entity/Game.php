<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\State\Game\GameCommandProvider;
use App\Api\State\Game\GameProvider;
use App\Enum\GameEnum;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Ignore;

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
    private const FOLDER = '/uploads/game/';

    #[ORM\Id]
    #[ORM\Column]
    private GameEnum $id;

    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[Gedmo\Locale]
    private ?string $locale = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Article $setupArticle = null;

    public function __construct(GameEnum $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id->value;
    }

    #[Ignore]
    public function getGame(): GameEnum
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

    #[Ignore]
    public function getImg(): ?string
    {
        return $this->img;
    }

    public function getImage(): ?string
    {
        return self::FOLDER.$this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

        return $this;
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
        $this->locale = $locale;

        return $this;
    }

    #[Ignore]
    public function getSetupArticle(): ?Article
    {
        return $this->setupArticle;
    }

    public function getSetupArticleSlug(): ?string
    {
        return $this->setupArticle?->getSlug();
    }

    public function setSetupArticle(?Article $setupArticle): static
    {
        $this->setupArticle = $setupArticle;

        return $this;
    }
}
